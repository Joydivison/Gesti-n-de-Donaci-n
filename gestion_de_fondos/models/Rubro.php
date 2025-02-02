<?php
require_once "../config/db.php";
global $pdo;

class Rubro {
    public static function insertarRubro($proyecto_id, $nombre) {
        global $pdo;
        if (empty($proyecto_id) || empty($nombre)) {
            return ["error" => "Todos los campos son obligatorios."];
        }

        try {
            $sql = "INSERT INTO rubros (id_proyecto, id_rubro) VALUES (:id_proyecto, :nombre)";
            $stmt = $pdo->prepare($sql);    
            $stmt->execute(['id_proyecto' => $proyecto_id, 'nombre' => $nombre]);
            return ["mensaje" => "Rubro agregado con éxito"];
        } catch (Exception $e) {
            return ["error" => "Error al guardar rubro: " . $e->getMessage()];
        }
    }

    public static function listarRubros() {
        global $pdo;
        try {
            $sql = "SELECT rubros_aux.nombre AS rubro_nombre, proyectos.nombre AS proyecto_nombre, rubros_aux.id AS rubro_id, proyectos.id AS proyecto_id, 
            rubros.monto_donacion AS monto, rubros.id As rubro_id2 FROM rubros_aux,rubros,proyectos WHERE rubros_aux.id=rubros.id_rubro AND proyectos.id=rubros.id_proyecto ";
            $stmt = $pdo->query($sql);
            $rubros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rubros ?: [];
        } catch (Exception $e) {
            error_log("Error al listar rubros: " . $e->getMessage());
            return ["error" => "Error al listar rubros"];
        }
    }

    public static function modificarRubro($id, $monto, $monto_donado) {
        global $pdo;
        if (!isset($id) || !isset($monto) || !isset($monto_donado)) {
           
            return ["error" => "Todos los campos son obligatoriooooos."];
            
        }

        $total=$monto+$monto_donado;

        try {
            $sql = "UPDATE rubros SET monto_donacion = :total WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id, 'total' => $total]);
            return ["mensaje" => "Donacion registrada con exito"];
        } catch (Exception $e) {
            error_log("Error al modificar rubro: " . $e->getMessage());
            return ["error" => "Error al modificar rubro"];
        }
        
    }



    public static function listarRubros2() {
        global $pdo;
        try {
            $sql = "SELECT id AS rubro_id, nombre AS rubro_nombre FROM rubros_aux";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rubros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Depuración: Ver si hay datos en la consulta
            if (!$rubros) {
                error_log("No se encontraron rubros en la BD.");
            }
    
            return $rubros ?: [];
        } catch (Exception $e) {
            error_log("Error al listar rubros: " . $e->getMessage());
            return ["error" => "Error al listar rubros"];
        }
    }


    public static function obtenerRubros($id) {
        global $pdo;
        $sql = "SELECT rubros_aux.nombre AS rubro_nombre, proyectos.nombre AS proyecto_nombre, rubros_aux.id AS rubro_id, proyectos.id AS proyecto_id, 
        rubros.monto_donacion AS monto, rubros.id AS rubro_ide
        FROM rubros_aux,rubros,proyectos WHERE rubros_aux.id=rubros.id_rubro AND proyectos.id=rubros.id_proyecto AND rubros.id= :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listarRubrosPorProyecto($id_proyecto) {
        global $pdo;
        try {
            $sql = "SELECT r.id, ra.nombre 
                    FROM rubros r
                    JOIN rubros_aux ra ON r.id_rubro = ra.id
                    WHERE r.id_proyecto = :id_proyecto";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id_proyecto' => $id_proyecto]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => "Error al listar rubros: " . $e->getMessage()];
        }
    }
    
    

}