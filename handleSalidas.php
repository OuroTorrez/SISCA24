<?php
if (!isset($_SESSION)) {
    session_start();
}
$municipios = [
    "Acuitzio",
    "Aguililla",
    "Álvaro Obregón",
    "Angamacutiro",
    "Angangueo",
    "Apatzingán",
    "Aporo",
    "Aquila",
    "Ario",
    "Arteaga",
    "Briseñas",
    "Buenavista",
    "Carácuaro",
    "Coahuayana",
    "Coalcomán de Vázquez Pallares",
    "Coeneo",
    "Contepec",
    "Copándaro",
    "Cotija",
    "Cuitzeo",
    "Charapan",
    "Charo",
    "Chavinda",
    "Cherán",
    "Chilchota",
    "Chinicuila",
    "Chucándiro",
    "Churintzio",
    "Churumuco",
    "Ecuandureo",
    "Epitacio Huerta",
    "Erongarícuaro",
    "Gabriel Zamora",
    "Hidalgo",
    "La Huacana",
    "Huandacareo",
    "Huaniqueo",
    "Huetamo",
    "Huiramba",
    "Indaparapeo",
    "Irimbo",
    "Ixtlán",
    "Jacona",
    "Jiménez",
    "Jiquilpan",
    "Juárez",
    "Jungapeo",
    "Lagunillas",
    "Madero",
    "Maravatío",
    "Marcos Castellanos",
    "Lázaro Cárdenas",
    "Morelia",
    "Morelos",
    "Múgica",
    "Nahuatzen",
    "Nocupétaro",
    "Nuevo Parangaricutiro",
    "Nuevo Urecho",
    "Numarán",
    "Ocampo",
    "Pajacuarán",
    "Panindícuaro",
    "Parácuaro",
    "Paracho",
    "Pátzcuaro",
    "Penjamillo",
    "Peribán",
    "La Piedad",
    "Purépero",
    "Puruándiro",
    "Queréndaro",
    "Quiroga",
    "Cojumatlán de Régules",
    "Los Reyes",
    "Sahuayo",
    "San Lucas",
    "Santa Ana Maya",
    "Salvador Escalante",
    "Senguio",
    "Susupuato",
    "Tacámbaro",
    "Tancítaro",
    "Tangamandapio",
    "Tangancícuaro",
    "Tanhuato",
    "Taretan",
    "Tarímbaro",
    "Tepalcatepec",
    "Tingambato",
    "Tingüindín",
    "Tiquicheo de Nicolás Romero",
    "Tlalpujahua",
    "Tlazazalca",
    "Tocumbo",
    "Tumbiscatío",
    "Turicato",
    "Tuxpan",
    "Tuzantla",
    "Tzintzuntzan",
    "Tzitzio",
    "Uruapan",
    "Venustiano Carranza",
    "Villamar",
    "Vista Hermosa",
    "Yurécuaro",
    "Zacapu",
    "Zamora",
    "Zináparo",
    "Zinapécuaro",
    "Ziracuaretiro",
    "Zitácuaro",
    "José Sixto Verduzco"
];

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
<!-- Formulario de registro de salidas -->
<form id="salidasDotaciones" method="post" action="">
    <input type="hidden" name="ejercicio" value="<?php echo $_POST['ejercicio']; ?>">
    <div id="salidasDotacionesInputs">
        <div class="FormData">
            <label for="afavor" class="req">A favor:</label>
            <input type="text" name="afavor" id="afavor" required>
        </div>
        <div class="FormData">
            <label for="municipio" class="req">municipio:</label>
            <select name="municipio" id="municipio" required>
                <?php
                foreach ($municipios as $municipio) {
                    echo "<option value='$municipio'>$municipio</option>";
                }
                ?>
            </select>
        </div>
        <div class="FormData">
            <label for="salida" class="req">Tipo de salida:</label>
            <select name="salida" id="salida" required>
                <?php
                // Muestra los tipos de salida posibles en un select
                $sql = "SELECT * FROM salidas";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // Output options as select dropdown
                    while ($row = $result->fetch_assoc()) {
                        $id = $row["id_salida"];
                        $tipo_salida = $row["tipo"];
                        echo "<option value='$id'>$id - $tipo_salida</option>";
                    }
                } else {
                    echo "<option value=''>No se encontraron</option>";
                }
                ?>
            </select>
        </div>
        <div class="FormData">
            <label for="recibe" class="req">Persona que recibe:</label>
            <input type="text" name="recibe" id="recibe" required>
        </div>
        <div class="FormData">
            <label for="referencia" class="req">Referencia:</label>
            <input type="text" name="referencia" id="referencia" required>
        </div>
        <div class="FormData">
            <label for="monto" class="req">Monto ($ MXN):</label>
            <input type="number" name="monto" id="monto" min="0" max="999999" step="any" required>
        </div>
        <div class="FormData">
            <label for="dotacion" class="req">Dotación:</label>
            <select name="dotacion" id="dotacion" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <?php if ($_POST['data'] == 'Desayunos Escolares Calientes') { ?>
                    <option value="9">9</option>
                <?php } else { ?>
                    <option value="9 - Ampliación">9 - Ampliación</option>
                <?php } ?>
            </select>
        </div>
        <div class="FormData">
            <label for="nota">Nota:</label>
            <textarea name="nota" id="nota" placeholder="Nota (máximo 255 carácteres)" maxlength="255"></textarea>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div id="tablaProductos">
        <div class="tr">
            <span class="td" style="width: 10%;">Clave</span>
            <span class="td" style="width: 30%;">Artículo</span>
            <span class="td" style="width: 15%;">Unidad</span>
            <span class="td req" style="width: 15%;">Lote</span>
            <span class="td req" style="width: 10%;">Caducidad</span>
            <span class="td req" style="width: 10%;">Cantidad</span>
            <span class="td" style="width: 10%;">Existencias</span>
        </div>
        <?php
        $usuario = $_SESSION['usuario'];
        // Obtener id_almacen del usuario
        $query = $conn->prepare("SELECT id_almacen FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id_almacen);
        $query->store_result();
        $query->fetch();


        $query = $conn->prepare("SELECT d.clave, d.producto, d.medida, 
        COALESCE((SELECT SUM(dr.cantidad) FROM registro_entradas_registradas dr
        INNER JOIN registro_entradas rd ON dr.folio = rd.folio AND dr.id = rd.id WHERE dr.clave = d.clave AND rd.cancelado = 0 AND rd.id_almacen = ?), 0)
        - COALESCE((SELECT SUM(sr.cantidad) FROM registro_salidas_registradas sr
        INNER JOIN registro_salidas sd ON sr.folio = sd.folio AND sr.id = sd.id WHERE sr.clave = d.clave AND sd.cancelado = 0 AND sd.id_almacen = ?), 0)
        AS existencias FROM dotaciones d WHERE d.programa = ? AND LEFT(clave, 4) = ?");


        $query->bind_param("ssss", $id_almacen, $id_almacen, $_POST['data'], $_POST['ejercicio']);
        if ($query->execute()) {
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='tr'>";
                    echo "<span class='td'>";
                    echo "<input type='text' value='" . $row['clave'] . "' disabled>";
                    echo "<input type='text' name='clave[]' id='clave' value='" . $row['clave'] . "' hidden>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='text' name='producto[]' value='" . $row['producto'] . "' disabled>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='text' name='medida[]' value='" . $row['medida'] . "' disabled>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='text' name='lote[]' id='lote' placeholder='Lote'>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='date' name='caducidad[]' id='caducidad'>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='numbrer' name='cantidad[]' id='cantidad' placeholder='Cantidad' min='0' max='999'>";
                    echo "</span>";
                    echo "<span class='td'>";
                    echo "<input type='text' name='existencias[]' value='" . $row['existencias'] . "' disabled>";
                    echo "</span>";
                    echo "</div>";
                }
            }
        } else {
            echo "Error: " . $query . "<br>" . $query->error;
            $error = $query->error;
        }
        $conn->close();
        ?>
        <?php if (isset($error)) { ?>
            <div id="Errores">
                <div id="Error">
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <div id="EntradasFormButtons">
        <button type="submit" id="guardarBtn">
            <i class="bi bi-floppy"></i>
            <span>Guardar</span>
        </button>
    </div>
</form>

<?php include 'ventanaResponse.php'; ?>

<div id="tableContenidosCont">
    <?php
    switch ($_POST['data']) {
        case "Personas Adultas Mayores":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra </td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja </td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa integral (Fideo 2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacío</td>
                                <td>Pouch de 120 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra última cosecha</td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g M.D. 100 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Leche deslactosada descremada en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pasta para sopa integral (Codito #2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pasta para sopa integral (Fideo #2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacío</td>
                                <td>Pouch de 120 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        case "Personas con Discapacidad":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra </td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja </td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa integral (Fideo 2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacío</td>
                                <td>Pouch de 120 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra última cosecha</td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional última cosecha</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa integral (Fideo #2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacío</td>
                                <td>Pouch de 120 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        case "Personas en Situación de Emergencias o Desastres":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas</td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra</td>
                                <td>Bolsa de 900 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con Zanahoria</td>
                                <td>Lata de 430 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamal izada</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Soya texturizada </td>
                                <td>Bolsa de 330 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola</td>
                                <td>Botella de 500 ml</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola</td>
                                <td>Botella de 500 ml</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra</td>
                                <td>Bolsa de 900 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g M.D. 100 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas</td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con Zanahoria</td>
                                <td>Lata de 430 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional última cosecha</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamal izada</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa integral (Codito #2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Polvo para preparar Gelatina con agua sin azúcar sabor fresa</td>
                                <td>Bolsa de 25 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Soya texturizada </td>
                                <td>Bolsa de 330 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        case "Infantes de 2 a 5 años 11 meses":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Leche entera en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuela</td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra</td>
                                <td>Bolsa 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja</td>
                                <td>Bolsa 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa (Letras)</td>
                                <td>Bolsa 200 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata 140 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria</td>
                                <td>Lata 430 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra última cosecha</td>
                                <td>Bolsa 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuela</td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria</td>
                                <td>Lata 430 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional última cosecha</td>
                                <td>Bolsa 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa (Letras) sémola de trigo</td>
                                <td>Bolsa 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacio</td>
                                <td>Pouch 120 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        case "Lactantes de 6 a 24 meses":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra </td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja </td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa (Fideo 1) </td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vació</td>
                                <td>Pouch 120 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra última cosecha</td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional última cosecha</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pasta para sopa (Fideo 1) sémola de trigo</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pechuga de pollo deshebrada al alto vacio</td>
                                <td>Pouch 120 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        case "Mujeres Embarazadas o en Periodo de Lactancia":
            switch ($_POST['ejercicio']) {
                case '2024':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Leche entera en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra </td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Soya texturizada </td>
                                <td>Bolsa de 330 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Espagueti integral</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja </td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Mango deshidratado con cacahuate tostado</td>
                                <td>Bolsa de 30 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case '2025':
                    ?>
                    <h2>Contenido de la caja:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Aceite vegetal comestible puro de canola </td>
                                <td>Botella de 500 ml</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Arroz pulido calidad extra última cosecha</td>
                                <td>Bolsa de 450 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Atún aleta amarilla en agua</td>
                                <td>Lata de 140 g M.D. 100 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Avena en hojuelas </td>
                                <td>Bolsa de 400 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Chícharo con zanahoria </td>
                                <td>Lata de 430 g M.D. 252 g</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Espagueti integral</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Frijol pinto nacional última cosecha</td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Harina de maíz nixtamalizado </td>
                                <td>Bolsa de 1 kg</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Leche descremada en polvo</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Lenteja última cosecha</td>
                                <td>Bolsa de 500 g</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Mix de frutos rojos deshidratados con cacahuates tostados</td>
                                <td>Bolsa de 30 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Pasta para sopa integral (Codito #2)</td>
                                <td>Bolsa de 200 g</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Soya texturizada </td>
                                <td>Bolsa de 330 g</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
            }
            break;
        default:
            // No se genera ninguna tabla para otros programas
            break;
    }
    ?>
</div>

<script>
    // Verificar en tiempo real que no se ingresen más cantidad de salidas que de existencias
    // Obtener los elementos de cantidad de salida y existencias
    var inputsCantidad = document.querySelectorAll('input[name="cantidad[]"]');
    var inputsExistencias = document.querySelectorAll('input[name="existencias[]"]');

    // Iterar sobre los elementos de cantidad de salida
    for (var i = 0; i < inputsCantidad.length; i++) {
        // Agregar un evento de escucha para el evento input
        inputsCantidad[i].addEventListener('input', function () {
            // Obtener el índice del elemento actual
            var index = Array.from(inputsCantidad).indexOf(this);
            // Obtener la cantidad de salida ingresada
            var cantidadSalida = parseInt(this.value);
            // Obtener las existencias correspondientes al mismo índice
            var existencias = parseInt(inputsExistencias[index].value);

            // Verificar si la cantidad de salida es mayor que las existencias
            if (cantidadSalida > existencias) {
                // Si es mayor, mostrar un mensaje de error
                this.setCustomValidity('La cantidad de salida no puede ser mayor que las existencias');
                this.reportValidity();
            } else {
                // Si no es mayor, borrar cualquier mensaje de error previo
                this.setCustomValidity('');
            }
        });
    }


    // Selecciona la tabla para aplicar los estilos de efecto hover
    var table = document.querySelector('#tablaProductos');
    table.addEventListener('mouseover', function (event) {
        // Encuentra la celda más cercana al elemento actual
        var cell = event.target.closest('.td');
        if (cell) {
            var cells = document.querySelectorAll('#tablaProductos .td');
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
        var cell = event.target.closest('.td');
        if (cell) {
            var cells = document.querySelectorAll('#tablaProductos .td');
            cells.forEach(function (otherCell) {
                otherCell.style.backgroundColor = '';
            });
            // Quita el resaltado de la fila
            cell.parentNode.style.backgroundColor = '';
        }
    });

    // Envía los datos del formulario para registrar las entradas
    document.getElementById('guardarBtn').addEventListener('click', function (e) {
        e.preventDefault();


        var form = document.getElementById('salidasDotaciones');
        var dataForm = new FormData(form);

        //Verifica que no haya campos vacios
        var inputs = form.querySelectorAll('input, textarea, select');
        for (var j = 0; j < inputs.length; j++) {
            if (inputs[j].required && !inputs[j].value) {
                inputs[j].reportValidity();
                return;
            }
        }

        // Verificar errores en las cantidades de salida
        var inputsCantidad = document.querySelectorAll('input[name="cantidad[]"]');
        var inputsExistencias = document.querySelectorAll('input[name="existencias[]"]');
        for (var i = 0; i < inputsCantidad.length; i++) {
            var cantidadSalida = parseInt(inputsCantidad[i].value);
            var existencias = parseInt(inputsExistencias[i].value);
            if (cantidadSalida > existencias) {
                inputsCantidad[i].setCustomValidity('La cantidad de salida no puede ser mayor que las existencias');
                inputsCantidad[i].reportValidity();
                return;
            }
        }

        generarSalidasyPDF(dataForm, 'portrait', true);
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>