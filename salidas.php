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
        <title>Salidas - DIF Michoacán</title>
        <!-- Styles -->
        <link rel="stylesheet" href="Styles/salidasStyles.css">
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
            <h1 class="PageTitle">Capturar salidas</h1>
            <div id="UserTitle">
                <!-- Imprimir nombre y almacen, así como las dotaciones que se esperan -->
                <?php
                    if(!empty($conn) && ($conn instanceof mysqli)){
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
                        // Retrieve dotaciones from dotaciones table
                        echo "<label id='SelectDotacionesLabel'>Canastas:</label>";
                        echo "<select name='SelectDotaciones' id='SelectDotaciones'>";
                        echo "<option hidden selected>Selecciona una opción</option>";
                        $query = $conn->prepare("SELECT DISTINCT programa FROM dotaciones");
                        $query->execute();
                        $query->bind_result($programa);
                        $query->store_result();
                        while($query->fetch()){
                            echo "<option value='$programa'>$programa</option>";
                        }
                        echo "</select>";
                    }
                ?>
            </div>
            <div id="SalidasForm">
                
            </div>
        </content>
    </body>
</html>
<script>
document.getElementById('SelectDotaciones').addEventListener('change', function() {
    var programa = document.getElementById('SelectDotaciones').value;
    // Send the value to the server
    $.ajax({
        url: 'handleSalidas.php',
        type: 'POST',
        data: {
            data: programa
        },
        success: function(response) {
            // You can use the response here
            $("#SalidasForm").html(response);
        },
        error: function(response) {
            console.log("response error:");
            console.log(response);
        }
    });

});
</script>