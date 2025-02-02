<?php
require_once "../models/Proyecto.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? '';
    
    switch ($accion) {
        case 'insertar':
            echo json_encode(["mensaje" => Proyecto::insertarProyecto($_POST["nombre"], $_POST["municipio"], $_POST["departamento"], $_POST["fecha_inicio"], $_POST["fecha_fin"]) ? "Proyecto guardado con éxito" : "Error al guardar"]);
            break;
        case 'modificar':
            echo json_encode(["mensaje" => Proyecto::modificarProyecto($_POST["id"], $_POST["nombre"], $_POST["municipio"], $_POST["departamento"], $_POST["fecha_inicio"], $_POST["fecha_fin"]) ? "Proyecto modificado con éxito" : "Error al modificar"]);
            break;
        case 'eliminar':
            echo json_encode(["mensaje" => Proyecto::eliminarProyecto($_POST["id"]) ? "Proyecto eliminado con éxito" : "Error al eliminar"]);
            break;
        case 'obtener':
            echo json_encode(Proyecto::obtenerProyecto($_POST["id"]));
            break;

        case 'listar':
            echo json_encode(Proyecto::listarProyectos());
            break;
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo json_encode(Proyecto::listarProyectos());
}