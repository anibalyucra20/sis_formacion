<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Persona extends Model
{
    protected $table = 'personas';

    public function getAll()
    {
        $db = self::getDB();
        $sql = "SELECT p.*, COUNT(cert.id) AS total_cursos
                FROM {$this->table} p
                LEFT JOIN certificados cert ON cert.persona_id = p.id
                GROUP BY p.id
                ORDER BY p.apellidos ASC, p.nombres ASC";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated($search = '', $length = 10, $start = 0, $orderCol = 0, $orderDir = 'asc')
    {
        $columnas = [
            0 => 'p.id',
            1 => 'p.dni',
            2 => 'p.apellidos',
            3 => 'p.nombres',
            4 => 'p.email',
            5 => 'p.telefono',
            6 => 'total_cursos',
            7 => 'p.fecha_registro'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'p.apellidos';
        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.dni LIKE :search OR p.nombres LIKE :search OR p.apellidos LIKE :search OR p.email LIKE :search OR p.telefono LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT p.*, COUNT(cert.id) AS total_cursos
                FROM {$this->table} p
                LEFT JOIN certificados cert ON cert.persona_id = p.id
                {$sqlWhere}
                GROUP BY p.id
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

        $sqlFiltered = "SELECT COUNT(DISTINCT p.id) FROM {$this->table} p {$sqlWhere}";
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
        $stmt = $db->prepare("SELECT p.*, COUNT(cert.id) as total_cursos 
                              FROM {$this->table} p
                              LEFT JOIN certificados cert ON cert.persona_id = p.id
                              WHERE p.id = ?
                              GROUP BY p.id LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByDni($dni, $exceptId = null)
    {
        $db = self::getDB();
        $dni = trim((string)$dni);
        if ($exceptId) {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE dni = ? AND id != ? LIMIT 1");
            $stmt->execute([$dni, (int)$exceptId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE dni = ? LIMIT 1");
            $stmt->execute([$dni]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($data)
    {
        $db = self::getDB();
        $dni = trim((string)$data['dni']);

        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $sql = "UPDATE {$this->table} SET 
                        dni = :dni,
                        nombres = :nombres,
                        apellidos = :apellidos,
                        email = :email,
                        telefono = :telefono
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':dni'       => $dni,
                ':nombres'   => trim($data['nombres']),
                ':apellidos' => trim($data['apellidos']),
                ':email'     => !empty($data['email']) ? trim($data['email']) : null,
                ':telefono'  => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':id'        => $id
            ]);
            return $id;
        } else {
            $sql = "INSERT INTO {$this->table} 
                        (dni, nombres, apellidos, email, telefono)
                    VALUES 
                        (:dni, :nombres, :apellidos, :email, :telefono)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':dni'       => $dni,
                ':nombres'   => trim($data['nombres']),
                ':apellidos' => trim($data['apellidos']),
                ':email'     => !empty($data['email']) ? trim($data['email']) : null,
                ':telefono'  => !empty($data['telefono']) ? trim($data['telefono']) : null
            ]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Upsert para carga masiva (si existe por DNI actualiza, si no, inserta)
     */
    public function upsertByDni($dni, $nombres, $apellidos, $email = null, $telefono = null)
    {
        $db = self::getDB();
        $dni = trim((string)$dni);
        $existente = $this->findByDni($dni);

        if ($existente) {
            $sql = "UPDATE {$this->table} SET 
                        nombres = :nombres,
                        apellidos = :apellidos,
                        email = COALESCE(:email, email),
                        telefono = COALESCE(:telefono, telefono)
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':nombres'   => trim($nombres),
                ':apellidos' => trim($apellidos),
                ':email'     => !empty($email) ? trim($email) : null,
                ':telefono'  => !empty($telefono) ? trim($telefono) : null,
                ':id'        => $existente['id']
            ]);
            return ['id' => $existente['id'], 'action' => 'updated'];
        } else {
            $sql = "INSERT INTO {$this->table} (dni, nombres, apellidos, email, telefono) 
                    VALUES (:dni, :nombres, :apellidos, :email, :telefono)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':dni'       => $dni,
                ':nombres'   => trim($nombres),
                ':apellidos' => trim($apellidos),
                ':email'     => !empty($email) ? trim($email) : null,
                ':telefono'  => !empty($telefono) ? trim($telefono) : null
            ]);
            return ['id' => (int)$db->lastInsertId(), 'action' => 'inserted'];
        }
    }

    public function eliminar($id)
    {
        $db = self::getDB();
        $stmtChk = $db->prepare("SELECT COUNT(*) FROM certificados WHERE persona_id = ?");
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
