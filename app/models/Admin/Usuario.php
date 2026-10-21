<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Usuario extends Model
{
    protected $table = 'usuarios';

    public function getPaginated($search = '', $length = 10, $start = 0, $orderCol = 0, $orderDir = 'asc')
    {
        $columnas = [
            0 => 'u.id',
            1 => 'u.nombre',
            2 => 'u.email',
            3 => 'u.telefono',
            4 => 'u.activo',
            5 => 'u.fecha_registro'
        ];

        $ordenarPor = $columnas[$orderCol] ?? 'u.id';
        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.nombre LIKE :search OR u.apellidos LIKE :search OR u.email LIKE :search OR u.telefono LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sqlWhere = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT u.id, u.rol_id, u.nombre, u.apellidos, u.email, u.telefono, u.activo, u.fecha_registro
                FROM {$this->table} u
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

        $sqlFiltered = "SELECT COUNT(*) FROM {$this->table} u {$sqlWhere}";
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
        $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email, $exceptId = null)
    {
        $db = self::getDB();
        if ($exceptId) {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE LOWER(email) = LOWER(?) AND id != ? LIMIT 1");
            $stmt->execute([trim($email), (int)$exceptId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([trim($email)]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($data)
    {
        $db = self::getDB();
        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $params = [
                ':nombre'    => trim($data['nombre']),
                ':apellidos' => trim($data['apellidos']),
                ':email'     => trim($data['email']),
                ':telefono'  => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':activo'    => isset($data['activo']) ? (int)$data['activo'] : 1,
                ':id'        => $id
            ];

            if (!empty($data['password'])) {
                $sql = "UPDATE {$this->table} SET
                            nombre        = :nombre,
                            apellidos     = :apellidos,
                            email         = :email,
                            password_hash = :password_hash,
                            telefono      = :telefono,
                            activo        = :activo
                        WHERE id = :id";
                $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                $sql = "UPDATE {$this->table} SET
                            nombre        = :nombre,
                            apellidos     = :apellidos,
                            email         = :email,
                            telefono      = :telefono,
                            activo        = :activo
                        WHERE id = :id";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $id;
        } else {
            $sql = "INSERT INTO {$this->table} 
                        (rol_id, nombre, apellidos, email, password_hash, telefono, activo, reset_password, token_password)
                    VALUES 
                        (1, :nombre, :apellidos, :email, :password_hash, :telefono, :activo, 0, '')";
            $params = [
                ':nombre'        => trim($data['nombre']),
                ':apellidos'     => trim($data['apellidos']),
                ':email'         => trim($data['email']),
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':telefono'      => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':activo'        => isset($data['activo']) ? (int)$data['activo'] : 1
            ];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (int)$db->lastInsertId();
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE {$this->table} SET activo = :activo WHERE id = :id");
        return $stmt->execute([
            ':activo' => (int)$nuevoEstado,
            ':id'     => (int)$id
        ]);
    }

    public function eliminar($id)
    {
        $db = self::getDB();
        $stmtSesiones = $db->prepare("DELETE FROM sesiones WHERE usuario_id = ?");
        $stmtSesiones->execute([(int)$id]);

        $stmt = $db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public function countTotal()
    {
        $db = self::getDB();
        return (int)$db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
