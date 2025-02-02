<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Proyectos</h2>
        <form id="formProyecto">
            <input type="hidden" name="id">
            <input type="hidden" name="accion" value="insertar">
            <input type="text" class="form-control" name="nombre" placeholder="Nombre" required>
            <input type="text" class="form-control" name="municipio" placeholder="Municipio" required>
            <input type="text" class="form-control" name="departamento" placeholder="Departamento" required>
            <input type="date" class="form-control" name="fecha_inicio" required>
            <input type="date" class="form-control" name="fecha_fin" required>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
        <table class="table mt-3"><thead><tr><th>Nombre</th><th>Municipio</th><th>Departamento</th><th>Fecha de Inicio</th><th>Fecha Final</th><th>Acciones</th></tr></thead><tbody id="listaProyectos"></tbody></table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cargarProyectos() {
            $.get("../controllers/ProyectoController.php", function(data) {
                let proyectos = JSON.parse(data);
                let html = "";
                proyectos.forEach(proyecto => {
                    html += `<tr>
                        <td>${proyecto.nombre}</td>
                        <td>${proyecto.municipio}</td>
                        <td>${proyecto.departamento}</td>
                        <td>${proyecto.fecha_inicio}</td>
                        <td>${proyecto.fecha_fin}</td>

                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editar(${proyecto.id})">Editar</button>
                        </td>
                    </tr>`;
                });
                $("#listaProyectos").html(html);
            });
        }
        function editar(id) {
        $.post("../controllers/ProyectoController.php", { id, accion: "obtener" }, function(data) {
            $("#formProyecto [name=id]").val(data.id);
            $("#formProyecto [name=nombre]").val(data.nombre);
            $("#formProyecto [name=municipio]").val(data.municipio);
            $("#formProyecto [name=departamento]").val(data.departamento);
            $("#formProyecto [name=fecha_inicio]").val(data.fecha_inicio);
            $("#formProyecto [name=fecha_fin]").val(data.fecha_fin);
            $("#formProyecto [name=accion]").val("modificar");
        }, "json");
    }
    function eliminar(id) {
        if(confirm("¿Estás seguro de que deseas eliminar este proyecto?")) {
            $.post("../controllers/ProyectoController.php", { id, accion: "eliminar" }, function(data) {
                alert(data.mensaje);
                cargarProyectos();
            }, "json");
        }
    }
    $("#formProyecto").submit(function(e) {
        e.preventDefault();
        $.post("../controllers/ProyectoController.php", $(this).serialize(), function(data) {
            alert(data.mensaje);
            $("#formProyecto")[0].reset();
            $("#formProyecto [name=accion]").val("insertar");
            cargarProyectos();
        }, "json");
    });
        cargarProyectos();
    </script>
</body>
</html>
