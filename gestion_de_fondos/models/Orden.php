<?php
require_once "../config/db.php";

class Orden {
    public static function insertarOrden($proveedor, $fecha, $id_proyecto, $rubros) {
        global $pdo;
        try {
            $pdo->beginTransaction(); // Iniciar transacción

            // Verificar que haya fondos suficientes en cada rubro antes de registrar la orden
            foreach ($rubros as $rubro) {
                $sql_verificar = "SELECT 
            r.monto_donacion AS total_donado,
            COALESCE(SUM(o.monto), 0) AS total_gastado,
            (r.monto_donacion - COALESCE(SUM(o.monto), 0)) AS disponible
        FROM rubros r
        LEFT JOIN detalle_orden o ON r.id = o.id_rubro
        WHERE r.id_proyecto = :id_proyecto AND r.id = :id_rubro
        GROUP BY r.id, r.monto_donacion;
                ";

                $stmt = $pdo->prepare($sql_verificar);
                $stmt->execute(['id_proyecto' => $id_proyecto, 'id_rubro' => $rubro['id_rubro']]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$resultado || $resultado['disponible'] < $rubro['monto']) {
                    $pdo->rollBack();
                    return ["error" => "Fondos insuficientes para el rubro seleccionado."];
                }
            }

            // Insertar la orden de compra
            $sql = "INSERT INTO ordenes_compra (proveedor, fecha, id_proyecto) VALUES (:proveedor, :fecha, :id_proyecto)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'proveedor' => $proveedor,
                'fecha' => $fecha,
                'id_proyecto' => $id_proyecto
            ]);

            // Obtener el ID de la orden recién insertada
            $id_orden = $pdo->lastInsertId();

            // Insertar detalles de la orden (rubros y montos)
            $sql_detalle = "INSERT INTO detalle_orden (id_orden, id_rubro, monto) VALUES (:id_orden, :id_rubro, :monto)";
            $stmt_detalle = $pdo->prepare($sql_detalle);

            foreach ($rubros as $rubro) {
                $stmt_detalle->execute([
                    'id_orden' => $id_orden,
                    'id_rubro' => $rubro['id_rubro'],
                    'monto' => $rubro['monto']
                ]);
            }

            $pdo->commit(); // Confirmar la transacción
            return ["mensaje" => "Orden registrada con éxito"];
        } catch (Exception $e) {
            $pdo->rollBack(); // Revertir en caso de error
            return ["error" => "Error al registrar la orden: " . $e->getMessage()];
        }
    }

    public static function listarOrdenes() {
        global $pdo;
        try {
            $sql = "SELECT 
                        oc.id AS id_orden,
                        oc.proveedor,
                        oc.fecha,
                        p.nombre AS proyecto,
                        ra.nombre AS rubro,
                        do.monto AS monto_rubro
                    FROM ordenes_compra oc
                    JOIN proyectos p ON oc.id_proyecto = p.id
                    JOIN detalle_orden do ON oc.id = do.id_orden
                    JOIN rubros r ON do.id_rubro = r.id
                    JOIN rubros_aux ra ON r.id_rubro = ra.id
                    ORDER BY oc.id DESC";
    
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => "Error al listar órdenes: " . $e->getMessage()];
        }
    }
    
}
?>
