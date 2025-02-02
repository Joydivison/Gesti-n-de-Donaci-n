<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Órdenes de Compra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Órdenes de Compra</h2>
        
        <form id="formOrden">
            <input type="hidden" name="accion" value="insertar">

            <label>Proveedor:</label>
            <input type="text" class="form-control" name="proveedor" required>

            <label>Fecha:</label>
            <input type="date" class="form-control" name="fecha" required>

            <label>Proyecto:</label>
            <select class="form-control" name="id_proyecto" id="id_proyecto" required>
                <!-- Se cargarán los proyectos con AJAX -->
            </select>

            <label>Rubros:</label>
            <div id="rubros-container">
                <!-- Aquí se agregarán dinámicamente los rubros -->
            </div>

            <button type="button" class="btn btn-primary mt-2" onclick="agregarRubro()">Añadir Rubro</button>

            <button type="submit" class="btn btn-success mt-3">Registrar Orden</button>
        </form>

        <hr>

        <h3>Órdenes Registradas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Proyecto</th>
                </tr>
            </thead>
            <tbody id="tablaOrdenes">
                <!-- Se cargarán las órdenes de compra con AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        // Cargar proyectos al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            fetch("../controllers/ProyectoController.php", {
                method: "POST",
                body: new URLSearchParams({ accion: "listar" })
            })
            .then(response => response.json())
            .then(data => {
                let selectProyecto = document.getElementById("id_proyecto");
                data.forEach(proyecto => {
                    let option = document.createElement("option");
                    option.value = proyecto.id;
                    option.textContent = proyecto.nombre;
                    selectProyecto.appendChild(option);
                });
            });

            // Cargar órdenes existentes
            cargarOrdenes();
        });

        // Función para agregar un nuevo rubro dinámicamente
        function agregarRubro() {
            let id_proyecto = document.getElementById("id_proyecto").value;

            if (!id_proyecto) {
                alert("Selecciona un proyecto antes de agregar rubros.");
                return;
            }

            fetch("../controllers/RubroController.php", {
                method: "POST",
                body: new URLSearchParams({ accion: "listar_por_proyecto", id_proyecto })
            })
            .then(response => response.json())
            .then(data => {
                let div = document.createElement("div");
                div.classList.add("rubro-item", "mt-2");

                let select = document.createElement("select");
                select.name = "id_rubro[]";
                select.classList.add("form-control");
                select.required = true;

                data.forEach(rubro => {
                    let option = document.createElement("option");
                    option.value = rubro.id;
                    option.textContent = rubro.nombre;
                    select.appendChild(option);
                });

                let inputMonto = document.createElement("input");
                inputMonto.type = "number";
                inputMonto.name = "monto[]";
                inputMonto.classList.add("form-control", "mt-1");
                inputMonto.placeholder = "Monto";
                inputMonto.required = true;

                div.appendChild(select);
                div.appendChild(inputMonto);

                document.getElementById("rubros-container").appendChild(div);
            });
        }

        // Enviar formulario de orden de compra
        document.getElementById("formOrden").onsubmit = function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            let rubros = [];
            document.querySelectorAll(".rubro-item").forEach(div => {
                let id_rubro = div.querySelector("select").value;
                let monto = div.querySelector("input").value;
                rubros.push({ id_rubro, monto });
            });

            formData.append("rubros", JSON.stringify(rubros));

            fetch("../controllers/OrdenController.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.mensaje || data.error);
                cargarOrdenes();
            });
        };

        // Cargar órdenes de compra en la tabla
        function cargarOrdenes() {
    fetch("../controllers/OrdenController.php", {
        method: "POST",
        body: new URLSearchParams({ accion: "listar" })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Datos recibidos:", data); // 👀 Verificar los datos en consola
        let tabla = document.getElementById("tablaOrdenes");
        tabla.innerHTML = ""; // Limpiar tabla

        if (!Array.isArray(data) || data.length === 0) {
            tabla.innerHTML = "<tr><td colspan='6'>No hay órdenes registradas.</td></tr>";
            return;
        }

        // Agrupar órdenes por ID
        let ordenesMap = {};

        data.forEach(orden => {
            if (!ordenesMap[orden.id_orden]) {
                ordenesMap[orden.id_orden] = {
                    id_orden: orden.id_orden,
                    proveedor: orden.proveedor,
                    fecha: orden.fecha,
                    proyecto: orden.proyecto,
                    rubros: [],
                    montoTotal: 0 // Inicializar el monto total
                };
            }

            // Agregar rubro y sumar al total
            ordenesMap[orden.id_orden].rubros.push({
                nombre: orden.rubro,
                monto: parseFloat(orden.monto_rubro)
            });
            ordenesMap[orden.id_orden].montoTotal += parseFloat(orden.monto_rubro);
        });

        // Crear las filas de la tabla
        Object.values(ordenesMap).forEach(orden => {
            let tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${orden.id_orden}</td>
                <td>${orden.proveedor}</td>
                <td>${orden.fecha}</td>
                <td>${orden.proyecto}</td>
                <td>
                    ${orden.rubros
                        .map(rubro => `<strong>${rubro.nombre}</strong>: Q${rubro.monto.toFixed(2)}`)
                        .join("<br>")}
                </td>
                <td>Q${orden.montoTotal.toFixed(2)}</td>
            `;
            tabla.appendChild(tr);
        });
    })
    .catch(error => console.error("Error al cargar órdenes:", error));
}


    </script>
</body>
</html>
