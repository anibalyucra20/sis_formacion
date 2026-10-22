<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Curso extends Model
{
    protected $table = 'cursos';

    public function getAll($soloActivos = false)
    {
        $db = self::getDB();
        $sql = "SELECT c.*, COUNT(cert.id) AS total_certificados
                FROM {$this->table} c
                LEFT JOIN certificados cert ON cert.curso_id = c.id";
        if ($soloActivos) {
            $sql .= " WHERE c.estado = 'Activo'";
        }
        $sql .= " GROUP BY c.id ORDER BY c.id DESC";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated($search = '', $estado = '', $modalidad = '', $length = 10, $start = 0, $orderCol = 0, $orderDir = 'desc')
    {
        $columnas = [
            0 => 'c.id',
            1 => 'c.nombre',
            2 => 'c.horas_academicas',
            3 => 'c.modalidad',
            4 => 'c.estado',
            5 => 'total_certificados'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'c.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(c.codigo LIKE :s1 OR c.nombre LIKE :s2 OR c.instructor LIKE :s3 OR c.descripcion LIKE :s4)";
            $term = '%' . $search . '%';
            $params[':s1'] = $term;
            $params[':s2'] = $term;
            $params[':s3'] = $term;
            $params[':s4'] = $term;
        }

        if (!empty($estado)) {
            $where[] = "c.estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($modalidad)) {
            $where[] = "c.modalidad = :modalidad";
            $params[':modalidad'] = $modalidad;
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT c.*, COUNT(cert.id) AS total_certificados
                FROM {$this->table} c
                LEFT JOIN certificados cert ON cert.curso_id = c.id
                {$sqlWhere}
                GROUP BY c.id
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

        // Conteo filtrado
        $sqlFiltered = "SELECT COUNT(DISTINCT c.id) FROM {$this->table} c {$sqlWhere}";
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
        $stmt = $db->prepare("SELECT c.*, COUNT(cert.id) as total_certificados 
                              FROM {$this->table} c 
                              LEFT JOIN certificados cert ON cert.curso_id = c.id
                              WHERE c.id = ? 
                              GROUP BY c.id LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCodigo($codigo, $exceptId = null)
    {
        $db = self::getDB();
        if ($exceptId) {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE UPPER(codigo) = UPPER(?) AND id != ? LIMIT 1");
            $stmt->execute([trim($codigo), (int)$exceptId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE UPPER(codigo) = UPPER(?) LIMIT 1");
            $stmt->execute([trim($codigo)]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($data)
    {
        $db = self::getDB();
        $codigo = strtoupper(trim($data['codigo']));

        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $sql = "UPDATE {$this->table} SET 
                        codigo = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        horas_academicas = :horas,
                        fecha_inicio = :f_inicio,
                        fecha_fin = :f_fin,
                        instructor = :instructor,
                        modalidad = :modalidad,
                        estado = :estado
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':codigo'      => $codigo,
                ':nombre'      => trim($data['nombre']),
                ':descripcion' => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
                ':horas'       => (int)($data['horas_academicas'] ?? 40),
                ':f_inicio'    => !empty($data['fecha_inicio']) ? $data['fecha_inicio'] : null,
                ':f_fin'       => !empty($data['fecha_fin']) ? $data['fecha_fin'] : null,
                ':instructor'  => !empty($data['instructor']) ? trim($data['instructor']) : null,
                ':modalidad'   => $data['modalidad'] ?? 'Virtual',
                ':estado'      => $data['estado'] ?? 'Activo',
                ':id'          => $id
            ]);
            return $id;
        } else {
            $sql = "INSERT INTO {$this->table} 
                        (codigo, nombre, descripcion, horas_academicas, fecha_inicio, fecha_fin, instructor, modalidad, estado)
                    VALUES 
                        (:codigo, :nombre, :descripcion, :horas, :f_inicio, :f_fin, :instructor, :modalidad, :estado)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':codigo'      => $codigo,
                ':nombre'      => trim($data['nombre']),
                ':descripcion' => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
                ':horas'       => (int)($data['horas_academicas'] ?? 40),
                ':f_inicio'    => !empty($data['fecha_inicio']) ? $data['fecha_inicio'] : null,
                ':f_fin'       => !empty($data['fecha_fin']) ? $data['fecha_fin'] : null,
                ':instructor'  => !empty($data['instructor']) ? trim($data['instructor']) : null,
                ':modalidad'   => $data['modalidad'] ?? 'Virtual',
                ':estado'      => $data['estado'] ?? 'Activo'
            ]);
            return (int)$db->lastInsertId();
        }
    }

    public function eliminar($id)
    {
        $db = self::getDB();
        // Verificar si tiene certificados
        $stmtChk = $db->prepare("SELECT COUNT(*) FROM certificados WHERE curso_id = ?");
        $stmtChk->execute([(int)$id]);
        if ((int)$stmtChk->fetchColumn() > 0) {
            return false;
        }

        $stmt = $db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public function countTotal()
    {
        $db = self::getDB();
        return (int)$db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
