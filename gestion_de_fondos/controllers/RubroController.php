<?php
require_once "../models/Rubro.php";
require_once "../models/Proyecto.php";



header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? '';
    switch ($accion) {
        case 'listar_proyectos':
            echo json_encode(Proyecto::listarProyectos());
            break;
        case 'insertar_rubro':
            echo json_encode(Rubro::insertarRubro($_POST["proyecto_id"], $_POST["rubros_id"]));
            break;
        case 'listar_rubros':
            $rubros = Rubro::listarRubros();
            echo json_encode($rubros);
            break;
        case 'modificar_rubro':
            echo json_encode(Rubro::modificarRubro($_POST["id"], $_POST["monto"], $_POST["monto_donado"]));
            
            break;

        case 'listar_proyectos2':
            echo json_encode(Rubro::listarRubros2());
            break;

        case 'obtener':
            echo json_encode(Rubro::obtenerRubros($_POST["id"]));
             break;

        case 'listar_por_proyecto':
            $id_proyecto = $_POST["id_proyecto"] ?? 0;
            echo json_encode(Rubro::listarRubrosPorProyecto($id_proyecto));
            break;
    }
}
?>
