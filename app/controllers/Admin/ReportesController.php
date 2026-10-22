<?php

namespace App\Controllers\Admin;

require_once __DIR__ . '/../../models/Admin/Certificado.php';
require_once __DIR__ . '/../../models/Admin/Curso.php';
require_once __DIR__ . '/../../models/Admin/Persona.php';
require_once __DIR__ . '/../../models/Admin/Configuracion.php';

use Core\Controller;
use Core\Auth;
use App\Models\Admin\Certificado;
use App\Models\Admin\Curso;
use App\Models\Admin\Persona;
use App\Models\Admin\Configuracion;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportesController extends Controller
{
    protected $certModel;
    protected $cursoModel;
    protected $personaModel;
    protected $cfgModel;

    public function __construct()
    {
        parent::__construct();
        $this->certModel = new Certificado();
        $this->cursoModel = new Curso();
        $this->personaModel = new Persona();
        $this->cfgModel = new Configuracion();
    }

    public function index()
    {
        $cursos = $this->cursoModel->getAll();

        $this->view('admin/reportes/index', [
            'cursos'    => $cursos,
            'pageTitle' => 'Reportes Generales de Certificación',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    /**
     * Endpoint DataTables Server-Side con filtros automáticos (Curso, Fechas, DNI)
     */
    public function data()
    {
        header('Content-Type: application/json; charset=utf-8');

        $draw      = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
        $start     = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $length    = isset($_GET['length']) ? (int)$_GET['length'] : 10;
        $orderCol  = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
        $orderDir  = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';
        $search    = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';

        // Filtros específicos
        $curso_id  = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
        $desde     = !empty($_GET['desde']) ? trim($_GET['desde']) : null;
        $hasta     = !empty($_GET['hasta']) ? trim($_GET['hasta']) : null;
        $dni       = !empty($_GET['dni']) ? trim($_GET['dni']) : null;

        try {
            $result = $this->certModel->getReporteFiltrado($search, $curso_id, $desde, $hasta, $dni, $length, $start, $orderCol, $orderDir);

            echo json_encode([
                'draw'            => $draw,
                'recordsTotal'    => $result['recordsTotal'],
                'recordsFiltered' => $result['recordsFiltered'],
                'data'            => $result['data']
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Exportar reporte a Excel (.xlsx) nativo con todos los filtros activos
     */
    public function exportarExcel()
    {
        $curso_id = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
        $desde    = !empty($_GET['desde']) ? trim($_GET['desde']) : null;
        $hasta    = !empty($_GET['hasta']) ? trim($_GET['hasta']) : null;
        $dni      = !empty($_GET['dni']) ? trim($_GET['dni']) : null;

        $datos = $this->certModel->getReporteCompleto($curso_id, $desde, $hasta, $dni);

        $subtitulo = "Todos los registros";
        if ($curso_id) {
            $curso = $this->cursoModel->find($curso_id);
            $subtitulo = "Curso: " . ($curso['nombre'] ?? '');
        } elseif ($dni) {
            $subtitulo = "Filtrado por DNI: " . $dni;
        } elseif ($desde || $hasta) {
            $subtitulo = "Periodo: " . ($desde ?: 'Inicio') . " al " . ($hasta ?: 'Hoy');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte_Certificados');

        // Título principal
        $sheet->setCellValue('A1', 'REPORTE GENERAL DE CERTIFICADOS EMITIDOS');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF1E3A8A');

        $sheet->setCellValue('A2', $subtitulo . ' | Generado el: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setARGB('FF64748B');

        // Encabezados
        $headers = ['#', 'DNI', 'PARTICIPANTE', 'CURSO / CAPACITACIÓN', 'HORAS', 'CONDICIÓN', 'FECHA EMISIÓN'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        $rowNum = 4;
        foreach ($headers as $idx => $h) {
            $col = $cols[$idx];
            $sheet->setCellValue("{$col}{$rowNum}", $h);
            $sheet->getStyle("{$col}{$rowNum}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A8A');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $rowNum = 5;
        $num = 1;
        foreach ($datos as $d) {
            $sheet->setCellValue("A{$rowNum}", $num++);
            $sheet->setCellValueExplicit("B{$rowNum}", $d['dni'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$rowNum}", $d['participante']);
            $sheet->setCellValue("D{$rowNum}", $d['curso_nombre']);
            $sheet->setCellValue("E{$rowNum}", ($d['horas'] ?? 40) . ' h');
            $sheet->setCellValue("F{$rowNum}", $d['condicion'] ?? 'Aprobado');
            $sheet->setCellValue("G{$rowNum}", date('d/m/Y', strtotime($d['fecha_emision'])));
            $rowNum++;
        }

        if ($rowNum > 5) {
            $sheet->getStyle("A4:G" . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $filename = "Reporte_Certificados_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportarCursoExcel()
    {
        return $this->exportarExcel();
    }
}
