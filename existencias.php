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
            $query = $conn->prepare("SELECT u.nombres, u.apellido_paterno, u.apellido_materno, a.almacen AS almacen FROM usuarios u INNER JOIN almacenes a ON u.id_almacen = a.id_almacen WHERE u.usuario = ?");
            $query->bind_param("s", $usuario);
            $query->execute();
            $query->bind_result($nombre, $apellido_paterno, $apellido_materno, $almacen);
            $query->store_result();
            $query->fetch();
            echo "<h2>$nombre $apellido_paterno $apellido_materno</h2>";
            echo "<h3>$almacen</h3>";
        }
        ?>
    </div>
    <div id="Consulta">
        <?php
        // Obtener id_almacen del usuario
        $query = $conn->prepare("SELECT id_almacen FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id_almacen);   
        $query->store_result();
        $query->fetch();

            $query = $conn->prepare("SELECT d.clave, d.programa, d.producto, d.medida, 
            COALESCE((SELECT SUM(dr.cantidad) FROM dotaciones_registradas dr INNER JOIN registro_dotaciones rd ON dr.folio = rd.folio WHERE dr.clave = d.clave AND rd.id_almacen = ?), 0) 
            - COALESCE((SELECT SUM(sr.cantidad) FROM salidas_registradas sr INNER JOIN salidas_dotaciones sd ON sr.folio = sd.folio WHERE sr.clave = d.clave AND sd.id_almacen = ?), 0) AS existencias
            FROM dotaciones d");
            $query->bind_param("ii", $id_almacen, $id_almacen);
            if($query->execute()){
                $query->bind_result($clave, $programa, $producto, $medida, $existencias);
                $query->store_result();
                ?>
                <table class="tablaExistencias">
                    <tr>
                        <th colspan='5' class='ProgramaTitle'>Personas Adultas Mayores</th>
                    </tr>
                    <tr>
                        <th style="width: 15%;">Clave</th>
                        <th style="width: 50%;">Producto</th>
                        <th style="width: 25%;">Medida</th>
                        <th style="width: 10%;">Existencias</th>
                    </tr>
                <?php
                $previousPrograma = "Personas Adultas Mayores";
                if($query->num_rows > 0){
                    while($query->fetch()){
                        if ($programa != $previousPrograma) {
                            ?>
                            </table>
                            <table class="tablaExistencias">
                            <tr><th colspan='5' class='ProgramaTitle'><?php echo $programa ?></th></tr>
                            <tr>
                                <th style="width: 15%;">Clave</th>
                                <th style="width: 50%;">Producto</th>
                                <th style="width: 25%;">Medida</th>
                                <th style="width: 10%;">Existencias</th>
                            </tr>
                            <?php
                            $previousPrograma = $programa;
                        }

                        echo "<tr>";
                        echo "<td>$clave</td>";
                        echo "<td>$producto</td>";
                        echo "<td>$medida</td>";
                        echo "<td>$existencias</td>";
                        echo "</tr>";
                    }
                }else{
                    echo "<p>No hay existencias registradas</p>";
                }
            }else{
                echo "<p>Error al consultar las existencias</p>";
            }
        ?>
        </table>
    </div>
</body>

</html>
<script>
    // Selecciona todas las tablas existentes para aplicar los estilos de efecto hover
    var tables = document.querySelectorAll('#Consulta .tablaExistencias');

    tables.forEach(function(table) {
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
</script>








