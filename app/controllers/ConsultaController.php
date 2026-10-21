<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Admin\Certificado;
use App\Models\Admin\Configuracion;
use App\Utils\MYPDF;

class ConsultaController extends Controller
{
    protected $certModel;
    protected $cfgModel;

    public function __construct()
    {
        parent::__construct();
        $this->certModel = new Certificado();
        $this->cfgModel  = new Configuracion();
    }

    /**
     * Vista pública principal de consulta por DNI
     */
    public function index()
    {
        $dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';
        $resultado = null;
        $buscado = false;

        if (!empty($dni)) {
            $buscado = true;
            $resultado = $this->certModel->buscarPorDni($dni);
        }

        $configuracion = $this->cfgModel->get();

        $this->view('consulta/index', [
            'pageTitle'     => 'Consulta de Certificados de Cursos',
            'dni'           => $dni,
            'buscado'       => $buscado,
            'resultado'     => $resultado,
            'configuracion' => $configuracion,
            'module'        => 'public'
        ]);
    }

    /**
     * Endpoint AJAX para búsqueda rápida por DNI
     */
    public function buscar()
    {
        $dni = trim($_GET['dni'] ?? $_POST['dni'] ?? '');

        if (empty($dni)) {
            return $this->json(['success' => false, 'message' => 'Por favor ingrese un número de DNI válido.']);
        }

        $resultado = $this->certModel->buscarPorDni($dni);

        if (!$resultado) {
            return $this->json([
                'success' => false,
                'message' => 'No se encontraron registros de certificados emitidos para el DNI ' . htmlspecialchars($dni) . '.'
            ]);
        }

        return $this->json([
            'success' => true,
            'data'    => $resultado
        ]);
    }

    /**
     * Vista de verificación oficial de un certificado específico por su código
     */
    public function verificar($codigo = null)
    {
        if (empty($codigo)) {
            $codigo = trim($_GET['codigo'] ?? '');
        }

        if (empty($codigo)) {
            header('Location: ' . BASE_URL . '/consulta');
            exit;
        }

        $certificado = $this->certModel->findByCodigo($codigo);
        $configuracion = $this->cfgModel->get();

        $this->view('consulta/verificar', [
            'pageTitle'     => 'Verificación de Certificado - ' . htmlspecialchars($codigo),
            'certificado'   => $certificado,
            'codigo'        => $codigo,
            'configuracion' => $configuracion,
            'module'        => 'public'
        ]);
    }

    /**
     * Alias para verificar certificado
     */
    public function certificado($codigo = null)
    {
        return $this->verificar($codigo);
    }

    /**
     * Descargar o visualizar certificado en PDF oficial (TCPDF)
     */
    public function pdf($codigo = null)
    {
        if (empty($codigo)) {
            $codigo = trim($_GET['codigo'] ?? '');
        }

        $cert = $this->certModel->findByCodigo($codigo);
        if (!$cert) {
            http_response_code(404);
            echo "Certificado no encontrado.";
            exit;
        }

        $cfg = $this->cfgModel->get();

        // Generar PDF apaisado A4 (Landscape) con TCPDF
        if (!class_exists('\TCPDF')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
        }

        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Sistema de Control de Certificados');
        $pdf->SetAuthor($cfg['institucion']);
        $pdf->SetTitle('Certificado - ' . $cert['codigo_certificado']);
        $pdf->SetSubject('Certificado de Capacitación');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // Fondo y marco ornamental elegante
        $pdf->SetLineWidth(1.2);
        $pdf->SetDrawColor(30, 58, 138); // Navy blue
        $pdf->Rect(10, 10, 277, 190);

        $pdf->SetLineWidth(0.4);
        $pdf->SetDrawColor(202, 138, 4); // Gold border
        $pdf->Rect(13, 13, 271, 184);

        $fechaEmisionFormateada = date('d/m/Y', strtotime($cert['fecha_emision']));

        $html = '
        <div style="text-align: center; font-family: helvetica;">
            <p style="font-size: 13px; color: #64748b; letter-spacing: 2px; margin-bottom: 2px;">'.strtoupper($cfg['institucion']).'</p>
            <h1 style="font-size: 32px; color: #1e3a8a; letter-spacing: 4px; font-weight: bold; margin: 4px 0;">CERTIFICADO</h1>
            <p style="font-size: 11px; color: #94a3b8; letter-spacing: 1px;">REGISTRO OFICIAL N°: <span style="color:#b45309; font-weight: bold;">'.$cert['codigo_certificado'].'</span></p>
            
            <p style="font-size: 13px; color: #475569; margin-top: 15px;">Se otorga el presente reconocimiento y constancia académica a:</p>
            
            <h2 style="font-size: 26px; color: #0f172a; font-weight: bold; margin: 10px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px;">
                '.strtoupper($cert['participante']).'
            </h2>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">DNI: <strong>'.$cert['dni'].'</strong></p>
            
            <p style="font-size: 13px; color: #334155; line-height: 1.5; margin: 15px 30px 10px 30px;">
                Por haber aprobado satisfactoriamente en calidad de <strong>'.$cert['condicion'].'</strong> el curso de capacitación y formación profesional:
            </p>
            
            <h3 style="font-size: 20px; color: #1e3a8a; font-weight: bold; margin: 8px 20px;">
                "'.$cert['curso_nombre'].'"
            </h3>
            
            <p style="font-size: 12px; color: #475569; margin: 5px 0 20px 0;">
                Con una duración acreditada de <strong>'.$cert['horas'].' horas académicas</strong> | Modalidad: <strong>'.$cert['curso_modalidad'].'</strong> | Calificación: <strong>'.$cert['nota'].'</strong>
            </p>
            
            <table style="width: 100%; margin-top: 25px;">
                <tr>
                    <td style="width: 33%; text-align: center;">
                        <br><br>
                        <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 5px;">
                            <strong style="font-size: 11px; color: #1e293b;">'.($cert['curso_instructor'] ?: 'Instructor Principal').'</strong><br>
                            <span style="font-size: 9px; color: #64748b;">Ponente / Instructor</span>
                        </div>
                    </td>
                    <td style="width: 34%; text-align: center; vertical-align: middle;">
                        <span style="font-size: 10px; color: #64748b;">
                            Fecha de Emisión:<br>
                            <strong style="font-size: 12px; color: #1e3a8a;">'.$fechaEmisionFormateada.'</strong>
                        </span>
                    </td>
                    <td style="width: 33%; text-align: center;">
                        <br><br>
                        <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 5px;">
                            <strong style="font-size: 11px; color: #1e293b;">'.$cfg['nombre_firma'].'</strong><br>
                            <span style="font-size: 9px; color: #64748b;">'.$cfg['cargo_firma'].'</span>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p style="font-size: 8px; color: #94a3b8; margin-top: 18px;">
                Verifique la autenticidad de este documento ingresando a: '.BASE_URL.'/verificar/'.$cert['codigo_certificado'].'
            </p>
        </div>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Certificado_' . $cert['codigo_certificado'] . '.pdf', 'I');
        exit;
    }
}
