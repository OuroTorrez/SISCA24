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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - DIF Michoacán</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/generalStyles.css">
    <link rel="stylesheet" href="Styles/consultasStyles.css">
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Data tables -->
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'ventanaResponse.php'; ?>
    <h1 class="PageTitle">Consulta de entradas</h1>
    <?php if (isset($error)) { ?>
        <div id="Errores">
            <div id="Error">
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php } ?>
    <div class="CenteredSection" style="padding-bottom: 50px;">
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
        }

        $query = $conn->prepare("SELECT DISTINCT rd.folio, p.nombre, rd.dotacion, DATE_FORMAT(rd.fecha_registro, '%d/%m/%Y %H:%i:%s') AS fecha_registro, d.programa, rd.pdf_docs
        FROM registro_dotaciones rd INNER JOIN proveedores p ON rd.id_proveedor = p.id_proveedor 
        INNER JOIN dotaciones_registradas dr ON rd.folio = dr.folio INNER JOIN dotaciones d ON dr.clave  = d.clave
        WHERE rd.id_almacen = (SELECT id_almacen FROM usuarios WHERE usuario = ?)");
        $query->bind_param("s", $usuario);
        if ($query->execute()) {
            $query->bind_result($folio, $proveedor, $dotacion, $fecha, $programa, $pdf_docs);
            $query->store_result();
            if ($query->num_rows > 0) {
                ?>
                <table id="tablaRegistros">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Proveedor</th>
                            <th>Dotación</th>
                            <th>Fecha de registro</th>
                            <th>Entrega</th>
                            <th>Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($query->fetch()) {
                            ?>
                            <tr>
                                <td data-search="<?php echo $programa, $folio ?>">
                                    <?php echo $folio; ?>
                                </td>
                                <td data-tooltip="<?php echo $programa ?>"><?php echo $proveedor; ?></td>
                                <td><?php echo $dotacion; ?></td>
                                <td><?php echo $fecha; ?></td>
                                <td><a data-tooltip="Consultar registro de entradas"
                                        onclick="consultarPDFEntradas(<?php echo $folio ?>, 'portrait', false)"><i
                                            class="bi bi-file-earmark-text"></i></a></td>
                                <?php
                                if ($pdf_docs != null) {
                                    ?>
                                    <td><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>)"><i
                                            class="bi bi-file-earmark-text"></i></td>
                                    <?php
                                } else {
                                    ?>
                                    <td><a data-tooltip="Subir documentos" onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>)"><i
                                                class="bi bi-cloud-upload"></i></a></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<h3>No hay registros de entradas</h3>";
            }
        } else {
            echo "<h3>No se pudieron obtener los registros de entradas</h3>";
        }
        ?>
    </div>
</body>

</html>
<script>
    // Selecciona la tabla para aplicar los estilos de efecto hover
    var table = document.querySelector('#tablaRegistros');
    table.addEventListener('mouseover', function (event) {
        // Encuentra la celda más cercana al elemento actual
        var cell = event.target.closest('td, th'); // Selector actualizado para incluir <th>
        if (cell) {
            var cells = document.querySelectorAll('#tablaRegistros td, #tablaRegistros th'); // Selector actualizado para incluir <th>
            var index = Array.from(cell.parentNode.children).indexOf(cell);
            cells.forEach(function (otherCell) {
                if (Array.from(otherCell.parentNode.children).indexOf(otherCell) === index) {
                    otherCell.style.backgroundColor = '#3b1b2f4c'; // color para la columna
                }
            });
            // Resalta la fila
            cell.parentNode.style.backgroundColor = '#3b1b2f4c'; // color para la fila
            // Resalta la celda específica
            cell.style.backgroundColor = '#6915495d'; // color para la celda
        }
    });

    // Quita el resaltado al salir de la tabla
    table.addEventListener('mouseout', function (event) {
        var cell = event.target.closest('td, th'); // Selector actualizado para incluir <th>
        if (cell) {
            var cells = document.querySelectorAll('#tablaRegistros td, #tablaRegistros th'); // Selector actualizado para incluir <th>
            cells.forEach(function (otherCell) {
                otherCell.style.backgroundColor = '';
            });
            // Quita el resaltado de la fila
            cell.parentNode.style.backgroundColor = '';
        }
    });


    $(document).ready(function () {
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
            "columnDefs": [{
                "targets": [5,4],
                "orderable": false
            }],
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        var tooltips = document.querySelectorAll('[data-tooltip]');

        tooltips.forEach(function (tooltip) {
            tooltip.addEventListener('mouseover', function (event) {
                var rect = tooltip.getBoundingClientRect();
                var tooltipWidth = tooltip.offsetWidth;

                // Calcular el espacio disponible a la derecha del tooltip
                var spaceRight = window.innerWidth - rect.right;

                // Calcular el espacio disponible a la izquierda del tooltip
                var spaceLeft = rect.left;

                // Verificar si el tooltip sale de la pantalla a la derecha
                if (spaceRight < tooltipWidth) {
                    console.log('derecha', spaceRight, tooltipWidth, rect.left + 10, 'px');
                    tooltip.style.setProperty('--tooltip-left', '-' + (tooltipWidth - spaceRight + 20) + 'px');
                }

                // Verificar si el tooltip sale de la pantalla a la izquierda
                if (spaceLeft < 0) {
                    tooltip.style.setProperty('--tooltip-left', 'calc(100% - ' + (rect.left + 10) + 'px)');
                }
            });
        });
    });

</script>