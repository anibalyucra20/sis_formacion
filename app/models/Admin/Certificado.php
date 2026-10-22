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
            1 => 'p.dni',
            2 => 'participante',
            3 => 'c.nombre',
            4 => 'c.horas_academicas',
            5 => 'cert.fecha_emision'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'cert.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.dni LIKE :s1 OR p.nombres LIKE :s2 OR p.apellidos LIKE :s3 OR c.nombre LIKE :s4)";
            $term = '%' . $search . '%';
            $params[':s1'] = $term;
            $params[':s2'] = $term;
            $params[':s3'] = $term;
            $params[':s4'] = $term;
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
                       c.horas_academicas AS horas,
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
                       c.horas_academicas AS horas,
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
        // Si se pasa un ID numérico, buscar por id
        if (is_numeric($codigo)) {
            return $this->find((int)$codigo);
        }
        return null;
    }

    /**
     * Consulta pública por DNI: retorna los datos de la persona y todos sus cursos/certificados
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

        // 2. Obtener todos los certificados emitidos a esta persona (horas tomadas del curso)
        $sqlCert = "SELECT cert.*, 
                           c.horas_academicas AS horas,
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
            $totalHoras += (int)($c['horas'] ?? $c['curso_horas'] ?? 0);
        }

        return [
            'persona'      => $persona,
            'certificados' => $cursos,
            'total_cursos' => count($cursos),
            'total_horas'  => $totalHoras
        ];
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
        $nota = !empty($data['nota']) ? trim($data['nota']) : null;
        $condicion = $data['condicion'] ?? 'Aprobado';
        $fecha_emision = !empty($data['fecha_emision']) ? $data['fecha_emision'] : date('Y-m-d');
        $observaciones = !empty($data['observaciones']) ? trim($data['observaciones']) : null;

        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $sql = "UPDATE {$this->table} SET 
                        curso_id = :curso_id,
                        persona_id = :persona_id,
                        nota = :nota,
                        condicion = :condicion,
                        fecha_emision = :fecha,
                        observaciones = :obs
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':curso_id'   => $curso_id,
                ':persona_id' => $persona_id,
                ':nota'       => $nota,
                ':condicion'  => $condicion,
                ':fecha'      => $fecha_emision,
                ':obs'        => $observaciones,
                ':id'         => $id
            ]);
            return $id;
        } else {
            $sql = "INSERT INTO {$this->table} 
                        (curso_id, persona_id, nota, condicion, fecha_emision, observaciones)
                    VALUES 
                        (:curso_id, :persona_id, :nota, :condicion, :fecha, :obs)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':curso_id'   => $curso_id,
                ':persona_id' => $persona_id,
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
        return (int)$db->query("SELECT COALESCE(SUM(c.horas_academicas), 0) FROM {$this->table} cert INNER JOIN cursos c ON c.id = cert.curso_id")->fetchColumn();
    }

    /**
     * Reporte de alumnos por curso
     */
    public function getReporteCurso($curso_id)
    {
        $db = self::getDB();
        $sql = "SELECT cert.*, 
                       c.horas_academicas AS horas,
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
                       c.horas_academicas AS horas,
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
            1 => 'p.dni',
            2 => 'participante',
            3 => 'c.nombre',
            4 => 'c.horas_academicas',
            5 => 'cert.condicion',
            6 => 'cert.fecha_emision'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'cert.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.dni LIKE :s1 OR p.nombres LIKE :s2 OR p.apellidos LIKE :s3 OR c.nombre LIKE :s4)";
            $term = '%' . $search . '%';
            $params[':s1'] = $term;
            $params[':s2'] = $term;
            $params[':s3'] = $term;
            $params[':s4'] = $term;
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
                       c.horas_academicas AS horas,
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
                       c.horas_academicas AS horas,
                       p.dni, p.nombres, p.apellidos, CONCAT(p.apellidos, ', ', p.nombres) AS participante,
                       c.codigo AS curso_codigo, c.nombre AS curso_nombre
                FROM {$this->table} cert
                INNER JOIN personas p ON p.id = cert.persona_id
                INNER JOIN cursos c ON c.id = cert.curso_id
                {$sqlWhere}
                ORDER BY cert.fecha_emision DESC, cert.id DESC";

        $db = self::getDB();
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

