<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Certificado extends Model
{
    protected $table = 'certificados';

    public function getPaginated($search = '', $curso_id = null, $condicion = '', $length = 10, $start = 0, $orderCol = 0, $orderDir = 'desc')
    {
        $columnas = [
            0 => 'cert.id',
            1 => 'cert.codigo_certificado',
            2 => 'p.dni',
            3 => 'participante',
            4 => 'c.nombre',
            5 => 'cert.horas',
            6 => 'cert.condicion',
            7 => 'cert.fecha_emision'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'cert.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(cert.codigo_certificado LIKE :search OR p.dni LIKE :search OR p.nombres LIKE :search OR p.apellidos LIKE :search OR c.nombre LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($curso_id)) {
            $where[] = "cert.curso_id = :curso_id";
            $params[':curso_id'] = (int)$curso_id;
        }

        if (!empty($condicion)) {
            $where[] = "cert.condicion = :condicion";
            $params[':condicion'] = $condicion;
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       p.email AS persona_email, p.telefono AS persona_telefono,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre, c.instructor AS curso_instructor, c.modalidad AS curso_modalidad
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                {$sqlWhere}
                ORDER BY {$ordenarPor} {$orderDir}
                LIMIT :limit OFFSET :offset";

        $db = self::getDB();
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlFiltered = "SELECT COUNT(*) 
                        FROM {$this->table} cert
                        INNER JOIN personas p ON p.id = cert.persona_id
                        INNER JOIN cursos c ON c.id = cert.curso_id
                        {$sqlWhere}";
        $stmtFiltered = $db->prepare($sqlFiltered);
        foreach ($params as $k => $v) {
            $stmtFiltered->bindValue($k, $v);
        }
        $stmtFiltered->execute();
        $recordsFiltered = (int)$stmtFiltered->fetchColumn();

        $sqlTotal = "SELECT COUNT(*) FROM {$this->table}";
        $recordsTotal = (int)$db->query($sqlTotal)->fetchColumn();

        return [
            'data'            => $data,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    public function find($id)
    {
        $db = self::getDB();
        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       p.email AS persona_email, p.telefono AS persona_telefono,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre, c.descripcion AS curso_descripcion,
                       c.horas_academicas AS curso_horas, c.fecha_inicio AS curso_inicio, c.fecha_fin AS curso_fin,
                       c.instructor AS curso_instructor, c.modalidad AS curso_modalidad
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                WHERE cert.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCodigo($codigo)
    {
        $db = self::getDB();
        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       p.email AS persona_email, p.telefono AS persona_telefono,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre, c.descripcion AS curso_descripcion,
                       c.horas_academicas AS curso_horas, c.fecha_inicio AS curso_inicio, c.fecha_fin AS curso_fin,
                       c.instructor AS curso_instructor, c.modalidad AS curso_modalidad
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                WHERE UPPER(TRIM(cert.codigo_certificado)) = UPPER(TRIM(?)) LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([trim($codigo)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta pública por DNI: retorna los datos de la persona y todos sus certificados
     */
    public function buscarPorDni($dni)
    {
        $db = self::getDB();
        $dni = trim((string)$dni);

        // 1. Obtener la persona
        $stmtPersona = $db->prepare("SELECT * FROM personas WHERE dni = ? LIMIT 1");
        $stmtPersona->execute([$dni]);
        $persona = $stmtPersona->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            return null;
        }

        // 2. Obtener todos los certificados emitidos a esta persona
        $sqlCert = "SELECT cert.*, 
                           c.codigo AS curso_codigo, c.nombre AS curso_nombre, c.descripcion AS curso_descripcion,
                           c.horas_academicas AS curso_horas, c.fecha_inicio AS curso_inicio, c.fecha_fin AS curso_fin,
                           c.instructor AS curso_instructor, c.modalidad AS curso_modalidad, c.estado AS curso_estado
                    FROM {$this->table} cert
                    INNER JOIN cursos c ON c.id = cert.curso_id
                    WHERE cert.persona_id = ?
                    ORDER BY cert.fecha_emision DESC, cert.id DESC";
        $stmtCert = $db->prepare($sqlCert);
        $stmtCert->execute([$persona['id']]);
        $cursos = $stmtCert->fetchAll(PDO::FETCH_ASSOC);

        $totalHoras = 0;
        foreach ($cursos as $c) {
            $totalHoras += (int)$c['horas'];
        }

        return [
            'persona'      => $persona,
            'certificados' => $cursos,
            'total_cursos' => count($cursos),
            'total_horas'  => $totalHoras
        ];
    }

    /**
     * Generar siguiente código correlativo de certificado (ej: CERT-2026-00001)
     */
    public function generarSiguienteCodigo($anio = null)
    {
        $db = self::getDB();
        if (!$anio) {
            $anio = date('Y');
        }

        $prefix = "CERT-{$anio}-";
        $sql = "SELECT codigo_certificado FROM {$this->table} 
                WHERE codigo_certificado LIKE :prefix 
                ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':prefix' => $prefix . '%']);
        $ultimo = $stmt->fetchColumn();

        if ($ultimo) {
            $partes = explode('-', $ultimo);
            $numero = (int)end($partes);
            $siguiente = $numero + 1;
        } else {
            $siguiente = 1;
        }

        return sprintf("CERT-%s-%05d", $anio, $siguiente);
    }

    public function findByCursoPersona($curso_id, $persona_id, $exceptId = null)
    {
        $db = self::getDB();
        if ($exceptId) {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE curso_id = ? AND persona_id = ? AND id != ? LIMIT 1");
            $stmt->execute([(int)$curso_id, (int)$persona_id, (int)$exceptId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE curso_id = ? AND persona_id = ? LIMIT 1");
            $stmt->execute([(int)$curso_id, (int)$persona_id]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($data)
    {
        $db = self::getDB();
        $curso_id = (int)$data['curso_id'];
        $persona_id = (int)$data['persona_id'];
        $codigo = strtoupper(trim($data['codigo_certificado']));
        $horas = (int)($data['horas'] ?? 40);
        $nota = !empty($data['nota']) ? trim($data['nota']) : null;
        $condicion = $data['condicion'] ?? 'Aprobado';
        $fecha_emision = !empty($data['fecha_emision']) ? $data['fecha_emision'] : date('Y-m-d');
        $observaciones = !empty($data['observaciones']) ? trim($data['observaciones']) : null;

        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $sql = "UPDATE {$this->table} SET 
                        curso_id = :curso_id,
                        persona_id = :persona_id,
                        codigo_certificado = :codigo,
                        horas = :horas,
                        nota = :nota,
                        condicion = :condicion,
                        fecha_emision = :fecha,
                        observaciones = :obs
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':curso_id'   => $curso_id,
                ':persona_id' => $persona_id,
                ':codigo'     => $codigo,
                ':horas'      => $horas,
                ':nota'       => $nota,
                ':condicion'  => $condicion,
                ':fecha'      => $fecha_emision,
                ':obs'        => $observaciones,
                ':id'         => $id
            ]);
            return $id;
        } else {
            $sql = "INSERT INTO {$this->table} 
                        (curso_id, persona_id, codigo_certificado, horas, nota, condicion, fecha_emision, observaciones)
                    VALUES 
                        (:curso_id, :persona_id, :codigo, :horas, :nota, :condicion, :fecha, :obs)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':curso_id'   => $curso_id,
                ':persona_id' => $persona_id,
                ':codigo'     => $codigo,
                ':horas'      => $horas,
                ':nota'       => $nota,
                ':condicion'  => $condicion,
                ':fecha'      => $fecha_emision,
                ':obs'        => $observaciones
            ]);
            return (int)$db->lastInsertId();
        }
    }

    public function eliminar($id)
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public function countTotal()
    {
        $db = self::getDB();
        return (int)$db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function sumHorasTotal()
    {
        $db = self::getDB();
        return (int)$db->query("SELECT COALESCE(SUM(horas), 0) FROM {$this->table}")->fetchColumn();
    }

    /**
     * Reporte de alumnos por curso
     */
    public function getReporteCurso($curso_id)
    {
        $db = self::getDB();
        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       p.email, p.telefono,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre, c.instructor, c.horas_academicas
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                WHERE cert.curso_id = ?
                ORDER BY p.apellidos ASC, p.nombres ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int)$curso_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reporte general por rango de fechas
     */
    public function getReporteFechas($desde = null, $hasta = null, $curso_id = null)
    {
        $db = self::getDB();
        $where = [];
        $params = [];

        if (!empty($desde)) {
            $where[] = "cert.fecha_emision >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $where[] = "cert.fecha_emision <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if (!empty($curso_id)) {
            $where[] = "cert.curso_id = :curso_id";
            $params[':curso_id'] = (int)$curso_id;
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                {$sqlWhere}
                ORDER BY cert.fecha_emision DESC, cert.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reporte con paginación para DataTables con filtros automáticos
     */
    public function getReporteFiltrado($search = '', $curso_id = null, $desde = null, $hasta = null, $dni = null, $length = 10, $start = 0, $orderCol = 0, $orderDir = 'desc')
    {
        $columnas = [
            0 => 'cert.id',
            1 => 'cert.codigo_certificado',
            2 => 'p.dni',
            3 => 'participante',
            4 => 'c.nombre',
            5 => 'cert.horas',
            6 => 'cert.condicion',
            7 => 'cert.fecha_emision'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'cert.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(cert.codigo_certificado LIKE :search OR p.dni LIKE :search OR p.nombres LIKE :search OR p.apellidos LIKE :search OR c.nombre LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($curso_id)) {
            $where[] = "cert.curso_id = :curso_id";
            $params[':curso_id'] = (int)$curso_id;
        }

        if (!empty($desde)) {
            $where[] = "cert.fecha_emision >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $where[] = "cert.fecha_emision <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if (!empty($dni)) {
            $where[] = "p.dni LIKE :dni";
            $params[':dni'] = '%' . trim($dni) . '%';
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                {$sqlWhere}
                ORDER BY {$ordenarPor} {$orderDir}
                LIMIT :limit OFFSET :offset";

        $db = self::getDB();
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlFiltered = "SELECT COUNT(*) 
                        FROM {$this->table} cert
                        INNER JOIN personas p ON p.id = cert.persona_id
                        INNER JOIN cursos c ON c.id = cert.curso_id
                        {$sqlWhere}";
        $stmtFiltered = $db->prepare($sqlFiltered);
        foreach ($params as $k => $v) {
            $stmtFiltered->bindValue($k, $v);
        }
        $stmtFiltered->execute();
        $recordsFiltered = (int)$stmtFiltered->fetchColumn();

        $sqlTotal = "SELECT COUNT(*) FROM {$this->table}";
        $recordsTotal = (int)$db->query($sqlTotal)->fetchColumn();

        return [
            'data'            => $data,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    /**
     * Reporte completo sin paginar para exportación a Excel con todos los filtros
     */
    public function getReporteCompleto($curso_id = null, $desde = null, $hasta = null, $dni = null)
    {
        $db = self::getDB();
        $where = [];
        $params = [];

        if (!empty($curso_id)) {
            $where[] = "cert.curso_id = :curso_id";
            $params[':curso_id'] = (int)$curso_id;
        }

        if (!empty($desde)) {
            $where[] = "cert.fecha_emision >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $where[] = "cert.fecha_emision <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if (!empty($dni)) {
            $where[] = "p.dni LIKE :dni";
            $params[':dni'] = '%' . trim($dni) . '%';
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT cert.*, 
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                {$sqlWhere}
                ORDER BY cert.fecha_emision DESC, cert.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cursos con más alumnos certificados para dashboard
     */
    public function getTopCursos($limit = 5)
    {
        $db = self::getDB();
        $sql = "SELECT c.nombre, COUNT(cert.id) as total
                FROM cursos c
                LEFT JOIN {$this->table} cert ON cert.curso_id = c.id
                GROUP BY c.id
                ORDER BY total DESC
                LIMIT " . (int)$limit;
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

