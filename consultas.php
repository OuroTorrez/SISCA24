<?php
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Consulta entradas - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/consultasStyles.css">
</head>

<body>
    <!-- Header menu -->
    <?php include 'header.php'; ?>
    <!-- Ventana de respuesta -->
    <?php include 'ventanaResponse.php'; ?>
    <!-- Contenido -->
    <content>
        <?php if (isset($error)) { ?>
            <div id="Errores">
                <div id="Error">
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        <?php } ?>
        <h1 class="PageTitle">Consulta de entradas</h1>

        <div id="UserTitle" style="padding-bottom: 50px;">
            <?php
            //Obtener e imprimir el nombre y almacen del usuario
            if (!empty($conn) && ($conn instanceof mysqli)) {
                $usuario = $_SESSION['usuario'];
                $query = $conn->prepare("SELECT u.nombres, u.apellido_paterno, u.apellido_materno, a.almacen AS almacen FROM usuarios u INNER JOIN almacenes a ON u.id_almacen = a.id_almacen WHERE u.usuario = ?");
                $query->bind_param("s", $usuario);
                $query->execute();
                $query->bind_result($nombre, $apellido_paterno, $apellido_materno, $almacen);
                $query->store_result();
                $query->fetch();
                echo "<h2>$nombre $apellido_paterno $apellido_materno</h2>";
                echo "<h3>$almacen</h3>";
                if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 4){
                    $query = $conn->prepare("SELECT * FROM almacenes");
                    $query->execute();
                    $query->bind_result($id_almacen, $almacen);
                    $query->store_result();
                    echo "<select id='almacenSelect' onchange='showEntradas(this.value)'>";
                    while ($query->fetch()) {
                        if ($_SESSION['id_almacen'] == $id_almacen) {
                            echo "<option value='$id_almacen' selected>$almacen</option>";
                        } else {
                            echo "<option value='$id_almacen'>$almacen</option>";
                        }
                    }
                    echo "</select>";
                }
            }
            ?>
        </div>
        <div id="Consulta">
            
        </div>
    </content>
</body>

</html>
<script>

var columnDefs;

if (<?php echo $_SESSION['rol'] == 4 ? 'true' : 'false'; ?>) {
    columnDefs = [{
        "targets": [5, 4, 6],
        "orderable": false
    }];
} else {
    columnDefs = [{
        "targets": [4, 5],
        "orderable": false
    }];
}

    function showEntradas(almacen) {
    var buttons = document.querySelectorAll(".OpcMenuButton");
    
    // Iterar sobre todos los botones y ajustar la clase activa
    buttons.forEach(button => {
        if (button.getAttribute('data-target') == almacen) {
            button.classList.add('active'); // Marcar el botón como activo si coincide con el target
        } else {
            button.classList.remove('active'); // Quitar la clase activa de los otros botones
        }
    });

    $.ajax({
        url: 'handleAdministradores.php',
        type: 'POST',
        data: {
            almacen: almacen,
            accion: "showEntradas"
        },
        success: function(response) {
            // Actualizar el contenido de la consulta con la respuesta
            $("#Consulta").html(response);
            attachTableHoverEffects(); // Re-attach hover effects

            // Inicializar DataTables
            $('#tablaRegistros').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ entradas por página",
                    "zeroRecords": "No se encontraron entradas",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "No se encontraron entradas con esos criterios",
                    "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "<i class='bi bi-chevron-double-left'></i>",
                        "last": "<i class='bi bi-chevron-double-right'></i>",
                        "next": "<i class='bi bi-chevron-right'></i>",
                        "previous": "<i class='bi bi-chevron-left'></i>"
                    },
                    "order": [
                        [0, "desc"]
                    ]
                },
                "columnDefs": columnDefs
            });
        },
        error: function(response) {
            console.log("response error:");
            console.log(response);
        }
    });
}

    function attachTableHoverEffects() {
        var table = document.querySelector('#tablaRegistros');
        if (table) {
            table.addEventListener('mouseover', function (event) {
                var cell = event.target.closest('td, th');
                if (cell) {
                    var row = cell.parentNode;
                    if (row.classList.contains('cancelled')) {
                        var cells = Array.from(row.children);
                        cells.forEach(function (otherCell) {
                            otherCell.style.backgroundColor = '#ff000080'; // color rojo para la columna
                        });
                        cell.style.backgroundColor = '#ff4d4d'; // color más claro para la celda específica
                    } else {
                        var cells = document.querySelectorAll('#tablaRegistros td, #tablaRegistros th');
                        var index = Array.from(cell.parentNode.children).indexOf(cell);
                        cells.forEach(function (otherCell) {
                            if (Array.from(otherCell.parentNode.children).indexOf(otherCell) === index) {
                                otherCell.style.backgroundColor = '#3b1b2f4c'; // color para la columna
                            }
                        });
                        row.style.backgroundColor = '#3b1b2f4c'; // color para la fila
                        cell.style.backgroundColor = '#6915495d'; // color para la celda
                    }
                }
            });

            table.addEventListener('mouseout', function (event) {
                var cell = event.target.closest('td, th');
                if (cell) {
                    var cells = document.querySelectorAll('#tablaRegistros td, #tablaRegistros th');
                    cells.forEach(function (otherCell) {
                        otherCell.style.backgroundColor = '';
                    });
                    cell.parentNode.style.backgroundColor = '';
                }
            });
        }

        var activoSliders = document.querySelectorAll('.activo');
        activoSliders.forEach(function (element) {
            element.addEventListener('click', function () {
                if (element.checked) {
                    ResponseCancel('Cancelar registro de entrada ' + element.value, 'Cancelar', 'Entradas', element.value, function() {uncheckSlider(element);}, element);
                } else {
                    console.log('unchecked');
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        showEntradas(<?php echo $_SESSION['id_almacen']; ?>);
    });

    document.addEventListener('DOMContentLoaded', function () {
        var tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(function (tooltip) {
            tooltip.addEventListener('mouseover', function (event) {
                var rect = tooltip.getBoundingClientRect();
                var tooltipWidth = tooltip.offsetWidth;
                var spaceRight = window.innerWidth - rect.right;
                var spaceLeft = rect.left;
                if (spaceRight < tooltipWidth) {
                    tooltip.style.setProperty('--tooltip-left', '-' + (tooltipWidth - spaceRight + 20) + 'px');
                }
                if (spaceLeft < 0) {
                    tooltip.style.setProperty('--tooltip-left', 'calc(100% - ' + (rect.left + 10) + 'px)');
                }
            });
        });
    });
</script>
