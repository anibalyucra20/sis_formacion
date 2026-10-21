<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Rol extends Model
{
    protected $table = 'roles';

    /**
     * Obtener todos los roles
     */
    public function getAll()
    {
        $stmt = self::$db->query("SELECT * FROM {$this->table} ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar un rol por ID
     */
    public function find($id)
    {
        $stmt = self::$db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los permisos del sistema
     */
    public function getAllPermisos()
    {
        $stmt = self::$db->query("SELECT * FROM permisos ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener los IDs de permisos asignados a un rol específico
     */
    public function getPermisosIdsByRol($rol_id)
    {
        $stmt = self::$db->prepare("SELECT permiso_id FROM rol_permisos WHERE rol_id = ?");
        $stmt->execute([(int)$rol_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Guardar/sincronizar permisos asignados a un rol
     */
    public function guardarPermisosRol($rol_id, array $permisoIds = [])
    {
        $rol_id = (int)$rol_id;

        // Limpiar permisos previos del rol
        $stmtDel = self::$db->prepare("DELETE FROM rol_permisos WHERE rol_id = ?");
        $stmtDel->execute([$rol_id]);

        // Insertar los nuevos permisos seleccionados
        if (!empty($permisoIds)) {
            $stmtIns = self::$db->prepare("INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (?, ?)");
            foreach ($permisoIds as $permId) {
                $stmtIns->execute([$rol_id, (int)$permId]);
            }
        }

        return true;
    }

    /**
     * Asegurar que existan permisos comunes para la tienda virtual
     */
    public function asegurarPermisosBasicos()
    {
        $permisosDefault = [
            ['ver_dashboard', 'Permite visualizar estadísticas principales e indicadores.'],
            ['gestionar_productos', 'Permite crear, editar, subir fotos y eliminar productos.'],
            ['gestionar_categorias', 'Permite administrar categorías del catálogo.'],
            ['gestionar_usuarios', 'Permite administrar usuarios y asignar permisos.'],
            ['gestionar_ventas', 'Permite cambiar estados de envíos, pagos y pedidos.']
        ];

        $stmtCheck = self::$db->prepare("SELECT COUNT(*) FROM permisos WHERE nombre = ?");
        $stmtIns   = self::$db->prepare("INSERT INTO permisos (nombre, descripcion) VALUES (?, ?)");

        foreach ($permisosDefault as $p) {
            $stmtCheck->execute([$p[0]]);
            if ($stmtCheck->fetchColumn() == 0) {
                $stmtIns->execute([$p[0], $p[1]]);
            }
        }
    }
}
