<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Rubros</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Administrar Rubros</h2>
        <form id="formRubro">
        <input type="hidden" class="form-control" name="id" id="id">
            <input type="hidden" name="accion" value="insertar_rubro">
            <label for="proyecto_id">Selecciona un Proyecto:</label>
            <select class="form-control" name="proyecto_id" id="proyecto_id" required ></select>
            <label for="rubros_id">Selecciona un Rubro:</label>
            <select class="form-control" name="rubros_id" id="rubros_id" required></select>
            <label for="proyecto_id">Monto Actual :</label>
            <input type="text" class="form-control" name="monto" id="monto" required readonly>
            <input type="text" class="form-control" name="monto_donado" id="monto_donado" placeholder="Monto a Donar" required readonly="true">
       
            <button type="submit" class="btn btn-primary mt-2">Guardar Rubro</button>
        </form>
        <h3 class="mt-4">Lista de Rubros</h3>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Nombre del Rubro</th>
                    <th>Proyecto Asignado</th>
                    <th>Presupuesto Asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="listaRubros"></tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
            document.getElementById("monto_donado").readOnly = true;  // Bloquear edición
        function cargarProyectos(callback) {
        
    $.post("../controllers/RubroController.php", {accion: "listar_proyectos"}, function(data) {
        let select = $("#proyecto_id");
        select.html("<option value=''>Seleccione un proyecto</option>");
        data.forEach(proyecto => {
            select.append(`<option value="${proyecto.id}">${proyecto.nombre}</option>`);
         
        });

        if (callback) callback();
    }, "json");
}


function cargarProyectosR(callback) {
    $.post("../controllers/RubroController.php", {accion: "listar_proyectos2"}, function(data) {
        if (!Array.isArray(data)) {
            console.error("Error en la respuesta del servidor:", data);
            return;
        }

        let select2 = $("#rubros_id");
        select2.html("<option value=''>Seleccione un Rubro</option>");

        data.forEach(rubro => {
            select2.append(`<option value="${rubro.rubro_id}">${rubro.rubro_nombre}</option>`);
        });

        if (callback) callback();
    }, "json").fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error en la petición AJAX:", textStatus, errorThrown);
    });
}

        function cargarRubros() {
           
            $.post("../controllers/RubroController.php", {accion: "listar_rubros"}, function(data) {
                let html = "";
                data.forEach(rubro => {
                    html += `<tr>
                        <td>${rubro.rubro_nombre}</td>
                        <td>${rubro.proyecto_nombre}</td>
                        <td>${rubro.monto}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editarRubro(${rubro.rubro_id2})">Donar</button>
                       
                        </td>
                    </tr>`;
                });
                $("#listaRubros").html(html);
            }, "json");
        }

        function editarRubro(id) {
            document.getElementById("monto_donado").readOnly = false; // Permitir edición
            document.getElementById("proyecto_id").setAttribute("disabled", true);
            document.getElementById("rubros_id").setAttribute("disabled", true);
          
            $.post("../controllers/RubroController.php", { id, accion: "obtener" }, function(data) {

                
            $("#formRubro [name=id]").val(data.rubro_ide);
            $("#formRubro [name=proyecto_id]").val(data.proyecto_id);
            $("#formRubro [name=rubros_id]").val(data.rubro_id);
            $("#formRubro [name=monto]").val(data.monto);
            $("#formRubro [name=monto_donado]").val(0);
            $("#formRubro [name=accion]").val("modificar_rubro");
    }, "json");
   
}


            $("#formRubro").on("submit", function() {
                document.getElementById("monto_donado").readOnly = true;
                document.getElementById("proyecto_id").removeAttribute("disabled");
                document.getElementById("rubros_id").removeAttribute("disabled");
            });

    
document.getElementById("monto_donado").readOnly = true;  // Bloquear edición
        $("#formRubro").submit(function(e) {
            e.preventDefault();
            $.post("../controllers/RubroController.php", $(this).serialize(), function(data) {
                alert(data.mensaje || data.error);
                cargarRubros();
                $("#formRubro")[0].reset();
                $("#formRubro [name=accion]").val("insertar_rubro");
            }, "json");
        });

        $(document).ready(function() {
            
            cargarProyectos();
            cargarRubros();
            cargarProyectosR();
        });

     
    </script>
</body>
</html>