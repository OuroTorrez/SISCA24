<?php
session_start();
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}

if (isset($_SESSION['usuario'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['folio']) ) {
        //Obtener el id de usuario y de almacen para la inserción del registro
        $query = $conn->prepare("SELECT id, id_almacen FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $_SESSION['usuario']);
        if ($query->execute()) {
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            $id_usuario = $row['id'];
            $id_almacen = $row['id_almacen'];
        }
        $query->close();

        //Obtener los datos del post de salidas
        $afavor = $_POST['afavor'];
        $municipio = $_POST['municipio'];
        $salida = $_POST['salida'];
        $recibe = $_POST['recibe'];
        $referencia = $_POST['referencia'];
        $monto = $_POST['monto'];
        $dotacion = $_POST['dotacion'];
        $nota = $_POST['nota'];

        $query = $conn->prepare("call insertar_registro_salidas(?,?,?,?,?,?,?,?,?,?)");
        $query->bind_param("iissississ", $id_usuario, $id_almacen, $afavor, $municipio, $salida, $recibe, $referencia, $monto, $dotacion, $nota);
        if($query->execute()){
            // Obtener el folio del registro realizado
            $result = $query->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $lastInsertedFolio = $row['folio'];

                $query->close();

                // Insertar los productos enviados por post con el folio generado
                $query = $conn->prepare("INSERT INTO registro_salidas_registradas (clave, folio, lote, caducidad, cantidad) VALUES (?, ?, ?, ?, ?)");
                $query->bind_param("isssi", $clave, $lastInsertedFolio, $lote, $caducidad, $cantidad);

                for ($i = 0; $i < count($_POST['clave']); $i++) {
                    $clave = $_POST['clave'][$i];
                    $lote = $_POST['lote'][$i];
                    $caducidad = $_POST['caducidad'][$i];
                    $cantidad = $_POST['cantidad'][$i];
                    $query->execute();
                }
                $query->close();
                generarDocumento($lastInsertedFolio);
            }
        }
    } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['folio'])) {
        generarDocumento($_POST['folio']);
    } else {
        generarDocumento(20014);
    }
}

function generarDocumento($folio)
{
    global $conn;
    $query = $conn->prepare("SELECT
    DATE_FORMAT(sd.fecha_registro, '%d/%m/%Y | %H:%i:%s') AS fecha_registro, sd.dotacion, sd.afavor, sd.recibe, sd.referencia, FORMAT(sd.monto, 2) AS monto, sd.nota, sd.nota, sd.municipio,
    a.almacen, u.nombres, u.apellido_paterno, u.apellido_materno, d.programa, s.tipo
    FROM registro_salidas sd
    INNER JOIN almacenes a ON sd.id_almacen = a.id_almacen
    INNER JOIN usuarios u ON sd.id_usuario = u.id
    INNER JOIN registro_salidas_registradas sr ON sd.folio = sr.folio
    INNER JOIN dotaciones d ON sr.clave = d.clave
    INNER JOIN salidas s ON sd.id_salida = s.id_salida
    WHERE sd.folio = ?");
    $query->bind_param("i", $folio);
    if ($query->execute()) {
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $fecha_registro = $row['fecha_registro'];
        $dotacion = $row['dotacion'];
        $afavor = $row['afavor'];
        $recibe = $row['recibe'];
        $referencia = $row['referencia'];
        $monto = $row['monto'];
        $nota = $row['nota'];
        $municipio = $row['municipio'];
        $almacen = $row['almacen'];
        $nombre = $row['nombres'] . " " . $row['apellido_paterno'] . " " . $row['apellido_materno'];
        $programa = $row['programa'];
        $tipo_entrada = $row['tipo'];
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enviar salidas</title>
        <!-- Styles -->
        <link rel="stylesheet" href="Styles/enviarSalidaDoc.css">
    </head>

    <body>
    <header class='col20'>
            <div class='HeaderImg col4'>
                <img src='Media/logo1.png' alt='Logo Estatal Michoacán' style='aspect-ratio: 115 / 111'>
            </div>
            <div id='HeaderTitle' class='col10'>
                <h1>Salida de almacen</h1>
                <h5>Folio: <span id='folioElement'><?= $folio ?></span></h5>
                <h5>Captura: <span><?= $fecha_registro ?></span></h5>
            </div>
            <div class='HeaderImg col4'>
                <img src='Media/logo2.png' alt='Logo DIF Michoacán' style='aspect-ratio: 321 / 290'>
            </div>
        </header>
        <hr>
        <section class='col20'>
            <div class='DatosContent col6'>
                <div class='DatosTitulo col20'>
                    <h3>Datos de salida</h3>
                </div>
                <div class='DatosDatos col20'>
                    <h5>Almacén: <span><?= $almacen ?></span></h5>
                    <h5>Tipo: <span><?= $tipo_entrada ?></span></h5>
                    <h5>Dotación: <span><?= $dotacion ?></span></h5>
                </div>
            </div>
            <div class='DatosContent col12'>
                <div class='DatosTitulo col20'>
                    <h3>Datos de entrega</h3>
                </div>
                <div class='DatosDatos col20'>
                    <h5>A favor de: <span><?= $afavor ?></span></h5>
                    <h5>Municipio: <span><?= $municipio ?></span></h5>
                    <h5>Banco/Referencia: <span><?= $referencia ?></span></h5>
                    <h5>Monto: <span><?='$' . $monto ?></span></h5>
                </div>
            </div>
        </section>
        <section id='SeccionTabla' class='col20'>
            <div id='TablaTitulo' class='col20'>
                <h3><?= $programa ?></h3>
            </div>
            <table class='col20'>
                <thead>
                    <tr>
                        <th style='width: 10%'>Clave</th>
                        <th style='width: 30%'>Producto</th>
                        <th style='width: 10%'>Unidad</th>
                        <?php if($programa != "Desayunos Escolares Calientes" && $programa != "Espacios de Alimentación") {echo "<th style='width: 5%'>Cuota</th>";}?>
                        <th style='width: 10%'>Cantidad</th>
                        <?php if($programa != "Desayunos Escolares Calientes" && $programa != "Espacios de Alimentación") { echo "<th style='width: 5%;'>Total</th> ";}?>
                        <th style='width: 5%'>Caducidad</th>
                        <th style='width: 25%'>Lote</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $conn->prepare("SELECT
    d.clave, d.producto, d.medida, d.programa, d.cuota,
    sr.lote, DATE_FORMAT(sr.caducidad, '%d/%m/%Y') AS caducidad, sr.cantidad
    FROM registro_salidas_registradas sr
    INNER JOIN dotaciones d ON sr.clave = d.clave
    WHERE sr.folio = ?");
                    $query->bind_param("i", $folio);
                    if ($query->execute()) {
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?= $row['clave'] ?></td>
                                <td><?= $row['producto'] ?></td>
                                <td><?= $row['medida'] ?></td>
                                <?php if($programa != "Desayunos Escolares Calientes" && $programa != "Espacios de Alimentación")  { ?>
                                    <td><?= '$' . $row['cuota'] ?></td>
                                <?php } ?>
                                <td><?= $row['cantidad'] ?></td>
                                <?php if($programa != "Desayunos Escolares Calientes" && $programa != "Espacios de Alimentación")  { ?>
                                    <td><?= '$' . $row['cantidad'] * $row['cuota'] ?></td>
                                <?php } ?>
                                <td><?= $row['caducidad'] ?></td>
                                <td><?= $row['lote'] ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
        <hr>
        <section id='SeccionNota' class='col20'>
            <h5>Nota: <span><?= $nota ?></span></h5>
            <?php
            switch ($programa) {
                case "Personas Adultas Mayores":
                    ?>
                    <h5>Contenido de la caja:</h5>
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
                    <h5>Contenido de la caja:</h5>
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
                    <h5>Contenido de la caja:</h5>
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
                    <h5>Contenido de la caja:</h5>
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
                    <h5>Contenido de la caja:</h5>
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
                    <h5>Contenido de la caja:</h5>
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
        </section>
        <hr>
        <section id='SeccionFirmas' class='col20'>
            <div class='Firma col5'>
                <h5 class='FirmaEspacio'>Entregado</h5>
                <h5 class='FirmaLinea'><?= $nombre ?></h5>
                <h5>Nombre y Firma</h5>
            </div>
            <div class='col5'></div>
            <div class='Firma col5'>
                <h5 class='FirmaEspacio'>Recibe</h5>
                <h5 class='FirmaLinea'><?= $recibe ?></h5>
                <h5>Nombre y Firma</h5>
            </div>
        </section>
    </body>

    </html>
    <?php
}
?>