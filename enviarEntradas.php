<?php
session_start();
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
if (isset($_SESSION['usuario'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['folio']) && !isset($_POST['targetDirectory']) && !isset($_POST['folioDocs'])) {


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

        //Obtener los datos post del registro
        $proveedor = $_POST['proveedor'];
        $entrada = $_POST['entrada'];
        $entrega = $_POST['entrega'];
        $dotacion = $_POST['dotacion'];
        $nota = $_POST['nota'];

        //Hacemos el registro
        $query = $conn->prepare("call insertar_registro_entradas(?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("iiiisss", $id_usuario, $id_almacen, $proveedor, $entrada, $entrega, $dotacion, $nota);
        if ($query->execute()) {
            // Obtener el folio del registro realizado
            $result = $query->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $lastInsertedFolio = $row['folio'];

                $query->close();

                // Insertar los productos enviados por post con el folio generado
                $query = $conn->prepare("INSERT INTO registro_entradas_registradas (clave, folio, lote, caducidad, cantidad) VALUES (?, ?, ?, ?, ?)");
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
    } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['targetDirectory'])) {
        $docs = $_FILES['docs']['tmp_name'];
        $nombreArchivo = $_FILES['docs']['name'];
        $directorio = $_POST['targetDirectory'];
        $folio = $_POST['targetFolio'];
        $docsName = $_POST['docsName'];
        $nombrePersonalizado = $_POST['nombrePersonalizado'];

        // Construye el nombre completo del archivo personalizado
        $rutaCompleta = $directorio . $nombrePersonalizado;

        // Verifica si ya existe un archivo con el mismo nombre en el directorio
        if (file_exists($rutaCompleta)) {
            // Elimina el archivo existente
            unlink($rutaCompleta);
        }

        // Sube el nuevo archivo
        if (move_uploaded_file($docs, $rutaCompleta)) {
            if($directorio == "DocsEntradas/"){
                $query = $conn->prepare("UPDATE registro_entradas SET pdf_docs = ? WHERE folio = ?");
            } else if ($directorio == "DocsSalidas/"){
                $query = $conn->prepare("UPDATE registro_salidas SET pdf_docs = ? WHERE folio = ?");
            } else if  ($directorio == "DocsSalidasCoord/"){
                $query = $conn->prepare("UPDATE registro_salidas SET pdf_docs_coord = ? WHERE folio = ?");
            }
            $query->bind_param("ss", $rutaCompleta, $folio);
            if ($query->execute()) {
                $query->close();
                echo $rutaCompleta;
            } else {
                $query->close();
                echo "";
            }
        } else {
            echo "";
        }


    } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['folioDocs'])) {
        $folio = $_POST['folioDocs'];
        $tipo = $_POST['tipo'];
        if ($tipo == "Entradas") {
            $query = $conn->prepare("SELECT pdf_docs FROM registro_entradas WHERE folio = ?");
        } else if ($tipo == "Salidas") {
            $query = $conn->prepare("SELECT pdf_docs FROM registro_salidas WHERE folio = ?");
        } else if ($tipo == "SalidasCoord") {
            $query = $conn->prepare("SELECT pdf_docs_coord FROM registro_salidas WHERE folio = ?");
        }
        $query->bind_param("i", $folio);
        if ($query->execute()) {
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            if($tipo != "SalidasCoord"){
                echo $row['pdf_docs'];
            } else {
                echo $row['pdf_docs_coord'];
            }
        }
    } else {
        //Entorno de pruebas
        $query = $conn->prepare("SELECT folio FROM registro_entradas ORDER BY folio DESC LIMIT 1");
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $lastInsertedFolio = $row["folio"];
        $query->close();
        generarDocumento($lastInsertedFolio);
    }
}

function generarDocumento($folio)
{
    global $conn;
    $query = $conn->prepare("SELECT 
    DATE_FORMAT(r.fecha_registro, '%d/%m/%Y | %H:%i:%s') AS fecha_registro, r.dotacion, r.nota, r.entrega,
    a.almacen,
    p.nombre_legal, p.direccion, p.rfc, p.telefono,
    u.nombres, u.apellido_paterno, u.apellido_materno,
    d.clave, d.producto, d.medida,
    t.tipo,
    d.programa
    FROM registro_entradas r
    INNER JOIN almacenes a on r.id_almacen = a.id_almacen
    INNER JOIN registro_entradas_registradas dr on r.folio = dr.folio
    INNER JOIN proveedores p ON r.id_proveedor = p.id_proveedor
    INNER JOIN usuarios u ON r.id_usuario = u.id
    INNER JOIN entradas t ON r.id_entrada = t.id_entrada
    INNER JOIN dotaciones d ON dr.clave = d.clave
    WHERE r.folio = ?");
    $query->bind_param("i", $folio);
    if ($query->execute()) {
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $fecha_registro = $row['fecha_registro'];
        $dotacion = $row['dotacion'];
        $nota = $row['nota'];
        $entrega = $row['entrega'];
        $almacen = $row['almacen'];
        $proveedor_legal = $row['nombre_legal'];
        $direccion = $row['direccion'];
        $rfc = $row['rfc'];
        $telefono = $row['telefono'];
        $nombre = $row['nombres'] . " " . $row['apellido_paterno'] . " " . $row['apellido_materno'];
        $tipo_entrada = $row['tipo'];
        $programa = $row['programa'];
    }
    ?>
    <!DOCTYPE html>
    <html lang='es'>

    <head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enviar entradas</title>
        <!-- Styles -->
        <link rel='stylesheet' href='Styles/enviarEntradaDoc.css'>
    </head>

    <body>
        <header class='col20'>
            <div class='HeaderImg col4'>
                <img src='Media/logo1.png' alt='Logo Estatal Michoacán' style='aspect-ratio: 115 / 111'>
            </div>
            <div id='HeaderTitle' class='col10'>
                <h1>Entrada de almacen</h1>
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
                    <h3>Datos de entrada</h3>
                </div>
                <div class='DatosDatos col20'>
                    <h5>Almacén: <span><?= $almacen ?></span></h5>
                    <h5>Tipo: <span><?= $tipo_entrada ?></span></h5>
                    <h5>Dotación: <span><?= $dotacion ?></span></h5>
                </div>
            </div>
            <div class='DatosContent col12'>
                <div class='DatosTitulo col20'>
                    <h3>Datos del proveedor</h3>
                </div>
                <div class='DatosDatos col20'>
                    <h5>Nombre: <span><?= $proveedor_legal ?></span></h5>
                    <h5>Dirección: <span><?= $direccion ?></span></h5>
                    <h5>RFC: <span><?= $rfc ?></span></h5>
                    <h5>Teléfono: <span><?= $telefono ?></span></h5>
                </div>
            </div>
        </section>
        <hr>
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
                        <th style='width: 25%'>Lote</th>
                        <th style='width: 5%'>Caducidad</th>
                        <th style='width: 5%'>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $conn->prepare("SELECT
    d.clave, d.producto, d.medida, d.programa,
    dr.lote, DATE_FORMAT(dr.caducidad, '%d/%m/%Y') AS caducidad, dr.cantidad
    FROM registro_entradas_registradas dr
    INNER JOIN dotaciones d ON dr.clave = d.clave
    WHERE dr.folio = ?");
                    $query->bind_param("i", $folio);
                    if ($query->execute()) {
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?= $row['clave'] ?></td>
                                <td><?= $row['producto'] ?></td>
                                <td><?= $row['medida'] ?></td>
                                <td><?= $row['lote'] ?></td>
                                <td><?= $row['caducidad'] ?></td>
                                <td><?= $row['cantidad'] ?></td>
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
                                                <td>Avena en hojuelas</td>
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
                                case "Personas en Situación de Emergencias o Desastres":
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
                                                <td>Aceite vegetal comestible puro de canola</td>
                                                <td>Botella de 500 ml</td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Arroz pulido calidad extra última cosecha</td>
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
                                                <td>Lata 430 g M.D. 252 g</td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Frijol pinto nacional última cosecha</td>
                                                <td>Bolsa 1 kg</td>
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
                                                <td>Pechuga de pollo deshebrada al alto vacío</td>
                                                <td>Pouch de 120 g</td>
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
                                                <td>Arroz pulido calidad extra última cosecha</td>
                                                <td>Bolsa de 450 g</td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Avena en hojuelas</td>
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
                                                <td>Pasta para sopa (Fideo #1) sémola de trigo</td>
                                                <td>Bolsa de 200 g</td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Pechuga de pollo deshebrada al alto vació</td>
                                                <td>Pouch de 120 g</td>
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
                                                                        <td>Chícharo con zanahoria</td>
                                                                        <td>Lata de 430 g M.D. 252 g</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
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
                                                                        <td>Mix de fruta deshidratada y oleaginosas</td>
                                                                        <td>Bolsa de 30 g</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Pasta para sopa integral (Codito #2)</td>
                                                                        <td>Bolsa de 200 g</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Soya texturizada</td>
                                                                        <td>Bolsa de 330 g</td>
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
                <h5 class='FirmaLinea'><?= $entrega ?></h5>
                <h5>Nombre y Firma</h5>
            </div>
            <div class='col5'></div>
            <div class='Firma col5'>
                <h5 class='FirmaEspacio'>Recibido</h5>
                <h5 class='FirmaLinea'><?= $nombre ?></h5>
                <h5>Nombre y Firma</h5>
            </div>
        </section>
    </body>

    </html>
    <?php
}
?>