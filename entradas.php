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
    <title>Captura - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/entradasStyles.css">
</head>

<body>
    <!-- Header menu -->
    <?php include 'header.php'; ?>
    <!-- Contenido -->
    <content>
        <?php if (isset($error)) { ?>
            <div id="Errores">
                <div id="Error">
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        <?php } ?>
        <h1 class="PageTitle">Capturar canastas</h1>
        <div id="UserTitle">
            <!-- Imprimir nombre y almacen, así como las dotaciones que se esperan -->
            <?php
            if (!empty($conn) && ($conn instanceof mysqli)) {
                // Retrieve name and store from users table where usuario = $_SESSION['usuario']
                $usuario = $_SESSION['usuario'];
                $query = $conn->prepare("SELECT u.nombres, u.apellido_paterno, u.apellido_materno, a.almacen AS almacen FROM usuarios u INNER JOIN almacenes a ON u.id_almacen = a.id_almacen WHERE u.usuario = ?");
                $query->bind_param("s", $usuario);
                $query->execute();
                $query->bind_result($nombre, $apellido_paterno, $apellido_materno, $almacen);
                $query->store_result();
                $query->fetch();
                echo "<h2>$nombre $apellido_paterno $apellido_materno</h2>";
                echo "<h3>$almacen</h3>";


                // Selector de ejercicio actual por año para las dotaciones

                echo "<label class='SelectDotacionesLabel'>Ejercicio:</label>";
                echo "<select name='SelectEjercicio' id='SelectEjercicio'>";
                $query = $conn->prepare("SELECT DISTINCT LEFT(clave, 4) as anioClave FROM dotaciones");
                $query->execute();
                $query->bind_result($anioClave);
                $query->store_result();
                while ($query->fetch()) {
                    echo "<option value='$anioClave'>$anioClave</option>";
                }
                echo "</select>";
                $query->close();

                // Retrieve dotaciones from dotaciones table
                echo "<label class='SelectDotacionesLabel'>Canastas:</label>";
                echo "<select name='SelectDotaciones' id='SelectDotaciones'>";
                echo "<option hidden selected>Selecciona una opción</option>";
                $query = $conn->prepare("SELECT DISTINCT programa FROM dotaciones");
                $query->execute();
                $query->bind_result($programa);
                $query->store_result();
                while ($query->fetch()) {
                    echo "<option value='$programa'>$programa</option>";
                }
                echo "</select>";
            }
            ?>
        </div>
        <div class="EntradasForm">

        </div>
    </content>
</body>

</html>
<script>
    $(document).ready(function () {
        document.getElementById('SelectDotaciones').addEventListener('change', function () {
            var programa = document.getElementById('SelectDotaciones').value;
            var ejercicio = document.getElementById('SelectEjercicio').value;
            // Send the value to the server
            $.ajax({
                url: 'handleEntradas.php',
                type: 'POST',
                data: {
                    data: programa,
                    ejercicio: ejercicio
                },
                success: function (response) {
                    // You can use the response here
                    $(".EntradasForm").html(response);
                },
                error: function (response) {
                    console.log("response error:");
                    console.log(response);
                }
            });
        });
    });

    $(document).ready(function () {
            document.getElementById('SelectEjercicio').addEventListener('change', function () {
                var programa = document.getElementById('SelectDotaciones').value;
                var ejercicio = document.getElementById('SelectEjercicio').value;
                // Send the value to the server
                $.ajax({
                    url: 'handleEntradas.php',
                    type: 'POST',
                    data: {
                        data: programa,
                        ejercicio: ejercicio
                    },
                    success: function (response) {
                        // You can use the response here
                        $(".EntradasForm").html(response);
                    },
                    error: function (response) {
                        console.log("response error:");
                        console.log(response);
                    }
                });
            });
        });
</script>