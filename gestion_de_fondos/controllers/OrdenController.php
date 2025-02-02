<?php
require_once "../models/Orden.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? '';

    switch ($accion) {
        case 'insertar':
            $proveedor = $_POST["proveedor"] ?? '';
            $fecha = $_POST["fecha"] ?? '';
            $id_proyecto = $_POST["id_proyecto"] ?? '';
            $rubros = json_decode($_POST["rubros"], true) ?? []; // Recibir lista de rubros y montos

            echo json_encode(Orden::insertarOrden($proveedor, $fecha, $id_proyecto, $rubros));
            break;

            case 'listar':
                $ordenes = Orden::listarOrdenes();
                if (isset($ordenes['error'])) {
                    echo json_encode(["error" => $ordenes['error']]);
                } else {
                    echo json_encode($ordenes);
                }
                break;
    }
}
?>
