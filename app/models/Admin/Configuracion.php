<?php

namespace App\Models\Admin;

use Core\Model;
use PDO;

class Configuracion extends Model
{
    protected $table = 'configuracion_sistema';

    public function get()
    {
        $db = self::getDB();
        $stmt = $db->query("SELECT * FROM {$this->table} WHERE id = 1 LIMIT 1");
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return [
                'nombre_sistema' => 'Sistema de Control de Certificados',
                'institucion'    => 'Centro de Capacitación y Formación Continua',
                'direccion'      => 'Av. De la Cultura 750, Lima - Perú',
                'telefono'       => '+51 987 654 321',
                'email_contacto' => 'certificados@formacion.edu.pe',
                'logo'           => '',
                'nombre_firma'   => 'Dr. Alejandro Benavides',
                'cargo_firma'    => 'Director de Certificaciones Académicas'
            ];
        }
        return $data;
    }

    public function guardar($data)
    {
        $db = self::getDB();
        $sql = "UPDATE {$this->table} SET 
                    nombre_sistema = :nombre_sistema,
                    institucion    = :institucion,
                    direccion      = :direccion,
                    telefono       = :telefono,
                    email_contacto = :email_contacto,
                    nombre_firma   = :nombre_firma,
                    cargo_firma    = :cargo_firma
                WHERE id = 1";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nombre_sistema' => trim($data['nombre_sistema']),
            ':institucion'    => trim($data['institucion']),
            ':direccion'      => trim($data['direccion']),
            ':telefono'       => trim($data['telefono']),
            ':email_contacto' => trim($data['email_contacto']),
            ':nombre_firma'   => trim($data['nombre_firma']),
            ':cargo_firma'    => trim($data['cargo_firma'])
        ]);
    }
}
