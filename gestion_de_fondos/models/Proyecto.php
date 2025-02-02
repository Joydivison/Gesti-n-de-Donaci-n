<?php
require_once "../config/db.php";

class Proyecto {
    public static function generarCodigoProyecto($pdo) {
        $sql = "SELECT codigo FROM proyectos ORDER BY id DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ultimo) {
            $num = (int) substr($ultimo['codigo'], 2) + 1;
        } else {
            $num = 1;
        }
        return "P-" . str_pad($num, 4, "0", STR_PAD_LEFT);
    }

    public static function obtenerProyecto($id) {
        global $pdo;
        $sql = "SELECT * FROM proyectos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function insertarProyecto($nombre, $municipio, $departamento, $fecha_inicio, $fecha_fin) {
        global $pdo;
        $codigo = self::generarCodigoProyecto($pdo);
        $sql = "INSERT INTO proyectos (codigo, nombre, municipio, departamento, fecha_inicio, fecha_fin) VALUES (:codigo, :nombre, :municipio, :departamento, :fecha_inicio, :fecha_fin)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(['codigo' => $codigo, 'nombre' => $nombre, 'municipio' => $municipio, 'departamento' => $departamento, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    }

    public static function listarProyectos() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM proyectos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function modificarProyecto($id, $nombre, $municipio, $departamento, $fecha_inicio, $fecha_fin) {
        global $pdo;
        $sql = "UPDATE proyectos SET nombre = :nombre, municipio = :municipio, departamento = :departamento, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'municipio' => $municipio, 'departamento' => $departamento, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    }

    public static function eliminarProyecto($id) {
        global $pdo;
        $sql = "DELETE FROM proyectos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }


    public static function listarProyectos2() {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT id, nombre FROM proyectos");
            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $proyectos ?: [];
        } catch (Exception $e) {
            return ["error" => "Error al listar proyectos: " . $e->getMessage()];
        }
    }
    
    

}

