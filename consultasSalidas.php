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
    <title>Consulta salidas - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/consultasSalidasStyles.css">
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
        <h1 class="PageTitle">Consulta de salidas</h1>

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
            }

            $query = $conn->prepare("SELECT DISTINCT sd.folio, sd.afavor, sd.municipio, sd.dotacion, sd.fecha_registro, sd.pdf_docs, sd.pdf_docs_coord, d.programa
        FROM salidas_dotaciones sd
        INNER JOIN salidas_registradas sr ON sd.folio = sr.folio
        INNER JOIN dotaciones d ON sr.clave = d.clave
        WHERE sd.id_almacen = (SELECT id_almacen FROM usuarios WHERE usuario = ?)");
        $query->bind_param("s", $usuario);
        if ($query->execute()) {
            $query->bind_result($folio, $afavor, $municipio, $dotacion, $fecha, $pdf_docs, $pdf_docs_coord, $programa);
            $query->store_result();
            if ($query->num_rows > 0) {
                ?>
                <table id="tablaRegistros" style="padding-bottom: 50px;">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>A favor de</th>
                            <th>Municipio</th>
                            <th>Dotacion</th>
                            <th>Fecha de registro</th>
                            <th>Salida</th>
                            <th>Documentos</th>
                            <?php if($_SESSION['rol'] == 3){ ?>
                                <th>Documentos</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($query->fetch()) {
                            ?>
                            <tr>
                            <td class="t-center" data-search="<?php echo $programa, $folio ?>"><?php echo $folio ?></td>
                            <td data-tooltip="<?php echo $programa ?>"><?php echo $afavor ?></td>
                            <td class="t-center"><?php echo $municipio ?></td>
                            <td class="t-center"><?php echo $dotacion ?></td>
                            <td class="t-center"><?php echo $fecha ?></td>
                            <td class="t-center"><a data-tooltip="Consultar registro de salida" onclick="consultarPDFSalidas(<?php echo $folio ?>, 'portrait', false)"><i
                                        class="bi bi-file-earmark-text"></i></a></td>
                            <?php
                            if ($pdf_docs != null) {
                                ?>
                                <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'Salidas', <?php echo $_SESSION['rol'] ?>)"><i class="bi bi-file-earmark-text"></i></a>
                                </td>
                                <?php
                            } else if ($_SESSION['rol'] != 3){
                                ?>
                                <td class="t-center"><a data-tooltip="Subir documentos" onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>, 'Salidas')"><i class="bi bi-cloud-upload"></i></a></td>
                                <?php
                            } else if ($pdf_docs == null && $_SESSION['rol'] == 3){
                                ?>
                                <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                                <?php
                            }
                            if($pdf_docs_coord != null && $_SESSION['rol'] == 3){
                                ?>
                                <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'SalidasCoord', <?php echo $_SESSION['rol'] ?>)"><i class="bi bi-file-earmark-text"></i></a>
                                </td>
                                <?php
                            } else if ($_SESSION['rol'] == 3){
                                ?>
                                <td class="t-center"><a data-tooltip="Subir documentos" onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>, 'SalidasCoord')"><i class="bi bi-cloud-upload"></i></a></td>
                                <?php
                            }
                        }
                        ?>
                        </tr>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<h3>No hay registros de salidas</h3>";
            }
        } else {
            echo "<h3>No se pudieron obtener los registros de salidas ". $conn->error ."</h3>";
        }
            ?>
        </div>
        <?php
        
        ?>
    </content>
</body>
</html>
<script>
    // Selecciona la tabla para aplicar los estulos de efecto hover
    var table = document.querySelector('#tablaRegistros');
    table.addEventListener('mouseover', function (event) {
        // Encuentra la celda más cercana al elemento actual
        var cell = event.target.closest('td, th'); // Selector actualizado para incluir <th>
        if (cell) {
            var cells = document.querySelectorAll(
                '#tablaRegistros td, #tablaRegistros th'); // Selector actualizado para incluir <th>
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
            var cells = document.querySelectorAll(
                '#tablaRegistros td, #tablaRegistros th'); // Selector actualizado para incluir <th>
            cells.forEach(function (otherCell) {
                otherCell.style.backgroundColor = '';
            });
            // Quita el resaltado de la fila
            cell.parentNode.style.backgroundColor = '';
        }
    });

    // data tables
    $(document).ready(function () {
        $('#tablaRegistros').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ salidas por página",
                "zeroRecords": "No se encontraron salidas",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No se encontraron salidas con esos criterios",
                "infoFiltered": "(filtrado de _MAX_ salidas totales)",
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
                "targets": [5, 6],
                "orderable": false
            }],
        });
    });

    // Previene que el tooltip se salga de la pantalla (a veces funciona xd)
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
                    tooltip.style.setProperty('--tooltip-left', '-' + (tooltipWidth - spaceRight +
                        20) + 'px');
                }

                // Verificar si el tooltip sale de la pantalla a la izquierda
                if (spaceLeft < 0) {
                    tooltip.style.setProperty('--tooltip-left', 'calc(100% - ' + (rect.left + 10) +
                        'px)');
                }
            });
        });
    });
</script>