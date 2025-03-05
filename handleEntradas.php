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
<!-- Formulario de registro de dotaciones -->
<form id="registroDotaciones" method="post" action="">
    <div id="registroDotacionesInputs">
        <div class="FormData">
            <label for="proveedor" class="req">Proveedor:</label>
            <select name="proveedor" id="proveedor" required>
                <?php
                // Muestra los proveedores que pueden surtir el programa de dotaciones seleccionado
                $query = $conn->prepare("SELECT a.id_proveedor, p.nombre FROM proveedores_autorizados a INNER JOIN proveedores p ON a.id_proveedor = p.id_proveedor WHERE a.programa = ? AND a.disponibilidad = 'SI'");
                $query->bind_param("s", $_POST['data']);
                $query->execute();
                $result = $query->get_result();

                if ($result->num_rows > 0) {
                    // Imprime los proveedores en un select
                    while ($row = $result->fetch_assoc()) {
                        $id = $row["id_proveedor"];
                        $nombre = $row["nombre"];
                        echo "<option value='$id'>$nombre</option>";
                    }
                } else {
                    echo "<option value=''>Ningún proveedor puede surtir</option>";
                }
                ?>
            </select>
        </div>
        <div class="FormData">
            <label for="entrada" class="req">Tipo de entrada:</label>
            <select name="entrada" id="entrada" required>
                <?php
                // Muestra los tipos de entrada posibles en un select
                $sql = "SELECT * FROM entradas";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output options as select dropdown
                    while ($row = $result->fetch_assoc()) {
                        $id = $row["id_entrada"];
                        $tipo_entrada = $row["tipo"];
                        echo "<option value='$id'>$id - $tipo_entrada</option>";
                    }
                } else {
                    echo "<option value=''>No options found.</option>";
                }
                ?>
            </select>
        </div>
        <div class="FormData">
            <label for="entrega" class="req">Entrega:</label>
            <input type="text" name="entrega" id="entrega" maxlength="255" required>
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
                <?php } else {?>
                    <option value="9 - Ampliación">9 - Ampliación</option>
                <?php } ?>
            </select>
        </div>
        <div class="FormData">
            <label for="nota">Nota:</label>
            <textarea name="nota" id="nota" placeholder="Nota (máximo 255 carácteres)" maxlength="255"></textarea>
        </div>
    </div>


    <!-- Formulario de registro de entradas -->
    <div id="tablaEntradas">
        <div class="tr">
            <span class="td" style="width: 10%;">Clave</span>
            <span class="td" style="width: 40%;">Artículo</span>
            <span class="td" style="width: 15%;">Unidad</span>
            <span class="td req" style="width: 15%;">Lote</span>
            <span class="td req" style="width: 10%;">Caducidad</span>
            <span class="td req" style="width: 10%;">Cantidad</span>
        </div>
        <?php
        // Muestra los productos correspondientes al programa o dotación seleccionada
        $query = $conn->prepare("SELECT clave, producto, medida FROM dotaciones WHERE programa = ?");
        $query->bind_param("s", $_POST['data']);
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
    
    <?php include 'ventanaResponse.php'; ?>
</form>

<div id="tableContenidosCont">
    <?php
    switch($_POST['data']){
        case "Personas Adultas Mayores":
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
            case "Personas con Discapacidad":
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
            case "Personas en Situación de Emergencias y Desastres":
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
            case "Infantes de 2 a 5 años 11 meses":
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
            case "Lactantes de 6 a 24 meses":
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
            case "Mujeres Embarazadas o en Periodo de Lactancia":
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
            default:
                // No se genera ninguna tabla para otros programas
                break;
        }
    ?>
</div>

<script>
    // Selecciona la tabla para aplicar los estilos de efecto hover
    var table = document.querySelector('#tablaEntradas');
    table.addEventListener('mouseover', function(event) {
        // Encuentra la celda más cercana al elemento actual
        var cell = event.target.closest('.td');
        if (cell) {
            var cells = document.querySelectorAll('#tablaEntradas .td');
            var index = Array.from(cell.parentNode.children).indexOf(cell);
            cells.forEach(function(otherCell) {
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
    table.addEventListener('mouseout', function(event) {
        var cell = event.target.closest('.td');
        if (cell) {
            var cells = document.querySelectorAll('#tablaEntradas .td');
            cells.forEach(function(otherCell) {
                otherCell.style.backgroundColor = '';
            });
            // Quita el resaltado de la fila
            cell.parentNode.style.backgroundColor = '';
        }
    });

    // Envía los datos del formulario para registrar las entradas
    document.getElementById('guardarBtn').addEventListener('click', function(e) {
        e.preventDefault();
        var form = document.getElementById('registroDotaciones');
        var dataForm = new FormData(form);

        //Verifica que no haya campos vacios
        var inputs = form.querySelectorAll('input, textarea, select');
        for (var j = 0; j < inputs.length; j++) {
            if (inputs[j].required && !inputs[j].value) {
                inputs[j].reportValidity();
                return;
            }
        }
        // Inspeccionar los datos de FormData
    console.log("Datos enviados:");
    for (var pair of dataForm.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

        generarEntradasyPDF(dataForm, 'portrait', true);
    });

    if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}
</script>