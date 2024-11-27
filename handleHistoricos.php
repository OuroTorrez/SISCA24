<?php
include 'conexion.php';
// Establish database connection
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
?>
<?php if (isset($error)) { ?>
    <div id="Errores">
        <div id="Error">
            <p><?php echo $error; ?></p>
        </div>
    </div>
<?php } ?>

<?php
if ($_POST['accion'] == "showHistoricos") {
    if (isset($_POST['almacen'])) {
        $almacen = $_POST['almacen'];
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];
        $mesant = $mes - 1;
        $anioant = $anio;
        if ($mesant == 0) {
            $mesant = 12;
            $anioant = $anio - 1;
        }
    echo $almacen;
    } else {
        $almacen = 0;
    }
    if ($almacen != 0) {
        $query = $conn->prepare("SELECT d.clave, d.programa, d.producto, d.medida,
        
        -- Calculo de existencias acumuladas hasta el mes anterior al consultado
        COALESCE(entradas_acum_ant.cantidad, 0) - COALESCE(salidas_acum_ant.cantidad, 0) AS existencias_ant,
        
        -- Cantidad de entradas durante el mes y año especificado
        COALESCE(entradas_mes.cantidad, 0) AS entradas,
        
        -- Cantidad de salidas durante el mes y año especificado
        COALESCE(salidas_mes.cantidad, 0) AS salidas,
        
        -- Cálculo de existencias
        COALESCE(entradas_acum_ant.cantidad, 0) - COALESCE(salidas_acum_ant.cantidad, 0) 
        + COALESCE(entradas_mes.cantidad, 0) - COALESCE(salidas_mes.cantidad, 0) AS existencias

        FROM dotaciones d

        -- Acumulación de entradas hasta el mes anterior al consultado
        LEFT JOIN (SELECT dr.clave, SUM(dr.cantidad) AS cantidad
        FROM registro_entradas_registradas dr 
        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
        WHERE rd.cancelado = 0 
        AND rd.id_almacen = ? 
        AND (YEAR(rd.fecha_registro) < $anio OR (YEAR(rd.fecha_registro) = $anio AND MONTH(rd.fecha_registro) < $mes))
        GROUP BY dr.clave) entradas_acum_ant ON entradas_acum_ant.clave = d.clave

        -- Acumulación de salidas hasta el mes anterior al consultado
        LEFT JOIN (
        SELECT sr.clave, SUM(sr.cantidad) AS cantidad
        FROM registro_salidas_registradas sr 
        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
        WHERE sd.cancelado = 0 
        AND sd.id_almacen = ? 
        AND (YEAR(sd.fecha_registro) < $anio OR (YEAR(sd.fecha_registro) = $anio AND MONTH(sd.fecha_registro) < $mes))
        GROUP BY sr.clave) salidas_acum_ant ON salidas_acum_ant.clave = d.clave

        -- Entradas del mes consultado
        LEFT JOIN (
        SELECT dr.clave, SUM(dr.cantidad) AS cantidad
        FROM registro_entradas_registradas dr 
        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
        WHERE rd.cancelado = 0 
        AND rd.id_almacen = ? 
        AND MONTH(rd.fecha_registro) = $mes 
        AND YEAR(rd.fecha_registro) = $anio
        GROUP BY dr.clave) entradas_mes ON entradas_mes.clave = d.clave

        -- Salidas del mes consultado
        LEFT JOIN (
        SELECT sr.clave, SUM(sr.cantidad) AS cantidad
        FROM registro_salidas_registradas sr 
        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
        WHERE sd.cancelado = 0 
        AND sd.id_almacen = ? 
        AND MONTH(sd.fecha_registro) = $mes 
        AND YEAR(sd.fecha_registro) = $anio
        GROUP BY sr.clave) salidas_mes ON salidas_mes.clave = d.clave;");

        $query->bind_param("iiii", $almacen, $almacen, $almacen, $almacen);
    } else {
        $query = $conn->prepare("SELECT d.clave, d.programa, d.producto, d.medida,
        
        -- Calculo de existencias acumuladas hasta el mes anterior al consultado
        COALESCE(entradas_acum_ant.cantidad, 0) - COALESCE(salidas_acum_ant.cantidad, 0) AS existencias_ant,
        
        -- Cantidad de entradas durante el mes y año especificado
        COALESCE(entradas_mes.cantidad, 0) AS entradas,
        
        -- Cantidad de salidas durante el mes y año especificado
        COALESCE(salidas_mes.cantidad, 0) AS salidas,
        
        -- Cálculo de existencias
        COALESCE(entradas_acum_ant.cantidad, 0) - COALESCE(salidas_acum_ant.cantidad, 0) 
        + COALESCE(entradas_mes.cantidad, 0) - COALESCE(salidas_mes.cantidad, 0) AS existencias

        FROM dotaciones d

        -- Acumulación de entradas hasta el mes anterior al consultado
        LEFT JOIN (SELECT dr.clave, SUM(dr.cantidad) AS cantidad
        FROM registro_entradas_registradas dr 
        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
        WHERE rd.cancelado = 0 
        AND rd.id_almacen = ? 
        AND (YEAR(rd.fecha_registro) < $anio OR (YEAR(rd.fecha_registro) = $anio AND MONTH(rd.fecha_registro) < $mes))
        GROUP BY dr.clave) entradas_acum_ant ON entradas_acum_ant.clave = d.clave

        -- Acumulación de salidas hasta el mes anterior al consultado
        LEFT JOIN (
        SELECT sr.clave, SUM(sr.cantidad) AS cantidad
        FROM registro_salidas_registradas sr 
        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
        WHERE sd.cancelado = 0 
        AND sd.id_almacen = ? 
        AND (YEAR(sd.fecha_registro) < $anio OR (YEAR(sd.fecha_registro) = $anio AND MONTH(sd.fecha_registro) < $mes))
        GROUP BY sr.clave) salidas_acum_ant ON salidas_acum_ant.clave = d.clave

        -- Entradas del mes consultado
        LEFT JOIN (
        SELECT dr.clave, SUM(dr.cantidad) AS cantidad
        FROM registro_entradas_registradas dr 
        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
        WHERE rd.cancelado = 0 
        AND rd.id_almacen = ? 
        AND MONTH(rd.fecha_registro) = $mes 
        AND YEAR(rd.fecha_registro) = $anio
        GROUP BY dr.clave) entradas_mes ON entradas_mes.clave = d.clave

        -- Salidas del mes consultado
        LEFT JOIN (
        SELECT sr.clave, SUM(sr.cantidad) AS cantidad
        FROM registro_salidas_registradas sr 
        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
        WHERE sd.cancelado = 0 
        AND sd.id_almacen = ?  
        AND MONTH(sd.fecha_registro) = $mes 
        AND YEAR(sd.fecha_registro) = $anio
        GROUP BY sr.clave) salidas_mes ON salidas_mes.clave = d.clave;");
        
        if ($query === false) {
            die('Error en la consulta SQL: ' . $conn->error);
        }
    }
    if ($query->execute()) {
        $query->bind_result($clave, $programa, $producto, $medida, $existencias_ant, $entradas, $salidas, $existencias);
        $query->store_result();
?>
        <table class="tablaHistoricos">
            <tr>
                <th colspan='8' class='ProgramaTitle'>Personas Adultas Mayores</th>
            </tr>
            <tr>
                <th style="width: 15%;">Clave</th>
                <th style="width: 40%;">Producto</th>
                <th style="width: 15%;">Medida</th>
                <!---->
                <th style="width: 10%;">Existencias Anteriores</th>
                <th style="width: 10%;">Entradas</th>
                <th style="width: 10%;">Salidas</th>
                <!---->
                <th style="width: 10%;">Existencias</th>
            </tr>
            <?php
            $previousPrograma = "Personas Adultas Mayores";
            if ($query->num_rows > 0) {
                while ($query->fetch()) {
                    if ($programa != $previousPrograma) {
            ?>
        </table>
        <table class="tablaHistoricos">
            <tr>
                <th colspan='8' class='ProgramaTitle'><?php echo $programa ?></th>
            </tr>
            <tr>
                <th style="width: 15%;">Clave</th>
                <th style="width: 40%;">Producto</th>
                <th style="width: 15%;">Medida</th>
                <!---->
                <th style="width: 10%;">Existencias Anteriores</th>
                <th style="width: 10%;">Entradas</th>
                <th style="width: 10%;">Salidas</th>
                <!---->
                <th style="width: 10%;">Existencias</th>
            </tr>
<?php
                        $previousPrograma = $programa;
                    }
                    echo "<tr>";
                    echo "<td>$clave</td>";
                    echo "<td>$producto</td>";
                    echo "<td>$medida</td>";
                    //
                    echo "<td>$existencias_ant</td>";
                    echo "<td>$entradas</td>";
                    echo "<td>$salidas</td>";
                    //
                    echo "<td>$existencias</td>";
                    echo "</tr>";
                }
            } else {
                echo "<p>No hay existencias historicas</p>";
            }
        } else {
            echo "<p>Error al r las existencias historicas</p>";
        }
?>
        </table>
    <?php
}
