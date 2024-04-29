<?php
    include 'conexion.php';
    $conn = conectar();
    if(empty($conn) || !($conn instanceof mysqli)){
        $error = "⛔Error de conexión: <br>" . $conn;
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura - DIF Michoacán</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/generalStyles.css">
    <link rel="stylesheet" href="Styles/entradasStyles.css">
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <!-- Header menu -->
    <?php include 'header.php'; ?>
    <!-- Contenido -->
    <content>
        <h1 class="PageTitle">Capturar dotaciones</h1>
        <div id="EntradasTitle">
            <?php if (isset($error)) { ?>
            <div id="Errores">
                <div id="Error">
                    <p><?php echo $error; ?></p>
                </div>
            </div>
            <?php } ?>
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
                    echo "<label id='SelectDotacionesLabel'>Dotaciones:</label>";
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
        <div class="EntradasForm">

        </div>
    </content>
</body>

</html>
<script>
document.getElementById('SelectDotaciones').addEventListener('change', function() {
    var programa = document.getElementById('SelectDotaciones').value;
    // Send the value to the server
    $.ajax({
        url: 'handleEntradas.php',
        type: 'POST',
        data: {
            data: programa
        },
        success: function(response) {
            // You can use the response here
            $(".EntradasForm").html(response);
        },
        error: function(response) {
            console.log("response error:");
            console.log(response);
        }
    });

});
</script>