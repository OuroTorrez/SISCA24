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
    <title>Historicos - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/historicosStyle.css">

</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <?php if (isset($error)) { ?>
        <div id="Errores">
            <div id="Error">
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php } ?>
    <h1 class="PageTitle">Acta de inventario</h1>

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
        <div id="ConsultaOpcMenu" class="OpcMenu">
            <form id="filterForm">
                <?php if ($_SESSION['id_almacen'] == 0) { ?>
                    <div class="FormData" styles="width: 100%;">
                        <label for="almacen" class="req">Seleccione Almacén:</label> </br>
                        <select id="almacen" name="almacen" required>
                            <?php
                            $query = $conn->prepare("SELECT * FROM almacenes");
                            $query->execute();
                            $query->bind_result($id_almacen, $almacen);
                            $query->store_result();
                            while ($query->fetch()) {
                                echo "<option value='$id_almacen'>$almacen</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <!-- Almacen ID hidden input -->
                    <input type="hidden" id="almacen" name="almacen" value="<?php echo $_SESSION['id_almacen']; ?>">
                <?php } ?>
                <!-- Selectores de Mes y Año -->
                <div class="FormData" styles="width: 100%;">
                    <label for="mes" class="req">Seleccione Mes:</label>
                    <!-- The second value will be selected initially -->
                    <select id="mes" name="mes">
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>
                <div class="FormData" styles="width: 100%;">
                    <label for="anio" class="req">Seleccione Año:</label>
                    <select id="anio" name="anio" required>
                        <?php
                        $anioActual = max(date("Y"), 2024); // Asegura que el año mínimo sea 2024
                        for ($a = $anioActual; $a >= 2024; $a--) {
                            echo "<option value='$a'>$a</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- Botón de Aplicar Filtros -->
                <button type="submit" id="ok">
                    <i class="bi bi-floppy"></i>
                    <span>Aplicar Filtros</span>
                </button>
            </form>
        </div>

        <div id="Consulta"></div>
    </div>
</body>

</html>
<script>
    $('#filterForm').submit(function (e) {
        e.preventDefault();
        var almacen = document.getElementById('almacen').value;
        var mes = document.getElementById('mes').value;
        var anio = document.getElementById('anio').value;
        // Send the value to the server
        $.ajax({
            url: 'handleHistoricos.php',
            type: 'POST',
            data: {
                almacen: almacen,
                mes: mes,
                anio: anio,
                accion: "showHistoricos"
            },
            success: function (response) {
                // Actualizar el contenido de la consulta con la respuesta
                $("#Consulta").html(response);
                attachTableHoverEffects(); // Re-attach hover effects
            },
            error: function (response) {
                console.log("response error:");
                console.log(response);
            }
        });

    });

    function attachTableHoverEffects() {
        // Selecciona todas las tablas existentes para aplicar los estilos de efecto hover
        var tables = document.querySelectorAll('#Consulta .tablaHistoricos');

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