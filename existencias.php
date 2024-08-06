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
    <title>Existencias - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/existenciasStyle.css">
</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>
    <!-- Content -->
    <?php if (isset($error)) { ?>
        <div id="Errores">
            <div id="Error">
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php } ?>
    <h1 class="PageTitle">Consulta de existencias</h1>

    <div id="UserTitle">
        <?php
        //Obtener e imprimir el nombre y almacen del usuario
        if (!empty($conn) && ($conn instanceof mysqli)) {
            $usuario = $_SESSION['usuario'];
            $query = $conn->prepare("SELECT u.nombres, u.apellido_paterno, u.apellido_materno, a.almacen, a.id_almacen AS almacen FROM usuarios u INNER JOIN almacenes a ON u.id_almacen = a.id_almacen WHERE u.usuario = ?");
            $query->bind_param("s", $usuario);
            $query->execute();
            $query->bind_result($nombre, $apellido_paterno, $apellido_materno, $almacen, $usr_id_almacen);
            $query->store_result();
            $query->fetch();
            echo "<h2>$nombre $apellido_paterno $apellido_materno</h2>";
            echo "<h3>$almacen</h3>";
        }
        ?>
    </div>
    <div id="ConsultaCont">
        <?php if($_SESSION['id_almacen'] == 0) { ?>
        <div id="ConsultaOpcMenu" class="OpcMenu">
            <?php
                $query = $conn->prepare("SELECT * FROM almacenes");
                $query->execute();
                $query->bind_result($id_almacen, $almacen);
                $query->store_result();
                while ($query->fetch()) {
                    if ($usr_id_almacen == $id_almacen) {
                        echo "<a class='OpcMenuButton active' data-target='$id_almacen' onclick='showExistencias($id_almacen)'>$almacen</a>";
                    } else {
                        echo "<hr>";
                        echo "<a class='OpcMenuButton' data-target='$id_almacen' onclick='showExistencias($id_almacen)'>$almacen</a>";
                    }
                }
            ?>
        </div>
        <?php } ?>
        <div id="Consulta">

        </div>
    </div>
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        showExistencias(<?php echo $_SESSION['id_almacen']; ?>);
    });

    function showExistencias(almacen) {
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
                accion: "showExistencias"
            },
            success: function(response) {
                // Actualizar el contenido de la consulta con la respuesta
                $("#Consulta").html(response);
                attachTableHoverEffects(); // Re-attach hover effects
            },
            error: function(response) {
                console.log("response error:");
                console.log(response);
            }
        });
    }

    function attachTableHoverEffects() {
        // Selecciona todas las tablas existentes para aplicar los estilos de efecto hover
        var tables = document.querySelectorAll('#Consulta .tablaExistencias');

        tables.forEach(function (table) {
            table.addEventListener('mouseover', function (event) {
                var cell = event.target.closest('th, td');
                if (cell && !cell.parentNode.classList.contains('ProgramaTitle')) { // Añadimos esta condición para excluir el encabezado y las celdas de título del programa
                    var cells = this.querySelectorAll('th, td');
                    var index = Array.from(cell.parentNode.children).indexOf(cell);
                    cells.forEach(function (otherCell) {
                        if (Array.from(otherCell.parentNode.children).indexOf(otherCell) === index) {
                            otherCell.style.backgroundColor = '#3b1b2f4c';
                        }
                    });
                    cell.parentNode.style.backgroundColor = '#3b1b2f4c';
                    cell.style.backgroundColor = '#6915495d';
                }
            });

            table.addEventListener('mouseout', function (event) {
                var cell = event.target.closest('th, td');
                if (cell && !cell.parentNode.classList.contains('ProgramaTitle')) { // Añadimos esta condición para excluir el encabezado y las celdas de título del programa
                    var cells = this.querySelectorAll('th, td');
                    cells.forEach(function (otherCell) {
                        otherCell.style.backgroundColor = '';
                    });
                    cell.parentNode.style.backgroundColor = '';
                }
            });
        });
    }
</script>
