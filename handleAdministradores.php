
<?php
session_start();
include 'conexion.php';
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
if ($_POST['accion'] == "showExistencias") {
    if (isset($_POST['almacen'])) {
        $almacen = $_POST['almacen'];
    } else {
        $almacen = 0;
    }
    if ($almacen != 0) {
        $query = $conn->prepare("SELECT d.clave, d.programa, d.producto, d.medida, 
            COALESCE((SELECT SUM(dr.cantidad) FROM registro_entradas_registradas dr 
                        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
                        WHERE dr.clave = d.clave AND rd.cancelado = 0 AND rd.id_almacen = ?), 0) 
            - COALESCE((SELECT SUM(sr.cantidad) FROM registro_salidas_registradas sr 
                        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
                        WHERE sr.clave = d.clave AND sd.cancelado = 0 AND sd.id_almacen = ?), 0) AS existencias
            FROM dotaciones d");
        $query->bind_param("ii", $almacen, $almacen);
    } else {
        $query = $conn->prepare("SELECT d.clave, d.programa, d.producto, d.medida, 
            COALESCE((SELECT SUM(dr.cantidad) FROM registro_entradas_registradas dr 
                        INNER JOIN registro_entradas rd ON dr.folio = rd.folio 
                        WHERE dr.clave = d.clave AND rd.cancelado = 0), 0) 
            - COALESCE((SELECT SUM(sr.cantidad) FROM registro_salidas_registradas sr 
                        INNER JOIN registro_salidas sd ON sr.folio = sd.folio 
                        WHERE sr.clave = d.clave AND sd.cancelado = 0), 0) AS existencias
            FROM dotaciones d");
    }
    if ($query->execute()) {
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
            if ($query->num_rows > 0) {
                while ($query->fetch()) {
                    if ($programa != $previousPrograma) {
                        ?>
                    </table>
                    <table class="tablaExistencias">
                        <tr>
                            <th colspan='5' class='ProgramaTitle'><?php echo $programa ?></th>
                        </tr>
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
            } else {
                echo "<p>No hay existencias registradas</p>";
            }
    } else {
        echo "<p>Error al consultar las existencias</p>";
    }
    ?>
    </table>
    <?php
} else if ($_POST['accion'] == "showEntradas") {
    $id_almacen = $_POST['almacen'];
    if($id_almacen != 0) {
        $query = $conn->prepare("SELECT DISTINCT rd.folio, p.nombre, rd.dotacion, DATE_FORMAT(rd.fecha_registro, '%d/%m/%Y %H:%i:%s') AS fecha_registro, d.programa, rd.pdf_docs, rd.cancelado, rd.nota_cancelacion, rd.verificado
            FROM registro_entradas rd INNER JOIN proveedores p ON rd.id_proveedor = p.id_proveedor 
            INNER JOIN registro_entradas_registradas dr ON rd.folio = dr.folio INNER JOIN dotaciones d ON dr.clave  = d.clave
            WHERE rd.id_almacen = ?");
        $query->bind_param("s", $id_almacen);
    } else {
        $query = $conn->prepare("SELECT DISTINCT rd.folio, p.nombre, rd.dotacion, DATE_FORMAT(rd.fecha_registro, '%d/%m/%Y %H:%i:%s') AS fecha_registro, d.programa, rd.pdf_docs, rd.cancelado, rd.nota_cancelacion, rd.verificado
            FROM registro_entradas rd INNER JOIN proveedores p ON rd.id_proveedor = p.id_proveedor 
            INNER JOIN registro_entradas_registradas dr ON rd.folio = dr.folio INNER JOIN dotaciones d ON dr.clave  = d.clave");
    }
    if ($query->execute()) {
        $query->bind_result($folio, $proveedor, $dotacion, $fecha, $programa, $pdf_docs, $activo, $nota_cancelacion, $verificado);
        $query->store_result();
        if ($query->num_rows > 0) {
            ?>
                <table id="tablaRegistros">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Proveedor</th>
                            <th>Dotación</th>
                            <th>Fecha de registro</th>
                            <th>Entrega</th>
                            <th>Documentos</th>
                        <?php if ($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) { ?>
                            <th>Cancelar</th>
                        <?php } ?>
                        <?php if ($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) { ?>
                            <th>Verificar</th>
                        <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($query->fetch()) {
                            if ($activo == 1) {
                                ?>
                                <tr class="tablaCancelado">
                                <?php
                            } else if ($verificado == 1) {
                                ?>
                                <tr class="tablaVerificado">
                                <?php
                            } else {
                                ?>
                                <tr>
                                <?php
                            }
                            ?>
                                <td class="t-center" data-search="<?php echo $programa, $folio ?>">
                                <?php echo $folio; ?>
                                </td>
                                <td data-tooltip="<?php echo $programa ?>"><?php echo $proveedor; ?></td>
                                <td class="t-center"><?php echo $dotacion; ?></td>
                                <td class="t-center"><?php echo $fecha; ?></td>
                                <td class="t-center"><a data-tooltip="Consultar registro de entrada <?php echo $_SESSION['rol'] ?>"
                                        onclick="consultarPDFEntradas(<?php echo $folio ?>, 'portrait', false)"><i
                                            class="bi bi-file-earmark-text"></i></a></td>
                                <!-- Subir documentos entrada de almacen -->
                                <?php
                                if ($pdf_docs != null && ($activo != 1 && $verificado != 1)) {
                                    ?>
                                    <td class="t-center">
                                        <a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>, 'Entradas', <?php echo $_SESSION['rol'] ?>, true)">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    </td>
                                <?php
                                } else if($pdf_docs != null && ($activo == 1 || $verificado == 1)){
                                    ?>
                                    <td class="t-center"><a data-tooltip="Consultar documentos"
                                            onclick="consultarDoc(<?php echo $folio ?>, 'Entradas', <?php echo $_SESSION['rol'] ?>, false)"><i
                                                class="bi bi-file-earmark-text"></i></a></td>
                                <?php
                                } else if($pdf_docs == null && ($activo == 1 || $verificado == 1)) {
                                    ?>
                                        <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                                <?php
                                } else if ($pdf_docs == null && ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4 && $_SESSION['rol'] != 5)) {
                                    ?>
                                        <td class="t-center"><a data-tooltip="Subir documentos"
                                                onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>, 'Entradas')"><i
                                                    class="bi bi-cloud-upload"></i></a></td>
                                <?php
                                } else if ($pdf_docs == null && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 5)) {
                                    ?>
                                            <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                                <?php
                                }

                                /* Cancelar registro de entradas */
                                if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 1) { 
                                    ?>
                                        <td class="t-center"><a data-tooltip="Cancelar">
                                                <label class="switch">
                                                    <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>" disabled>
                                                    <span class="slider round"></span>
                                                </label>
                                            </a></td>
                                    <?php
                                } else if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 0) {
                                    ?>
                                    <td class="t-center"><a data-tooltip="Cancelar">
                                            <label class="switch">
                                                <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>">
                                                <span class="slider round"></span>
                                            </label>
                                        </a></td>
                                <?php
                                } else if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 1) {
                                    ?>
                                        <td class="t-center"><a data-tooltip="Cancelar"
                                                onclick="WaitDoc('Nota de cancelacion', '<?php echo $nota_cancelacion ?>', 'CloseResponse()');">
                                                <label class="switch">
                                                    <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>" checked
                                                        disabled>
                                                    <span class="slider round"></span>
                                                </label>
                                            </a></td>
                                <?php
                                } 

                                /* Verificar registro de entradas */
                                if(($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 1){
                                    ?>
                                    <td class="t-center"><a data-tooltip="Verificar">
                                        <label class="switch">
                                            <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>" disabled>
                                            <span class="slider round"></span>
                                        </label>
                                    </a></td>
                                    <?php
                                } else if(($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 0){
                                    ?>
                                    <td class="t-center"><a data-tooltip="Verificar">
                                        <label class="switch">
                                            <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>">
                                            <span class="slider round"></span>
                                        </label>
                                    </a></td>
                                    <?php
                                } else if(($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 1){
                                    ?>
                                    <td class="t-center"><a data-tooltip="Verificar">
                                        <label class="switch">
                                            <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>" checked disabled>
                                            <span class="slider round"></span>
                                        </label>
                                    </a></td>
                                    <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
        } else {
            echo "<h3>No hay registros de entradas</h3>";
        }
    } else {
        echo "<h3>No se pudieron obtener los registros de entradas " . $conn->error . "</h3>";
    }
} else if ($_POST['accion'] == "showSalidas") {
    $almacen = $_POST['almacen'];
    if($almacen != 0){
        $query = $conn->prepare("SELECT DISTINCT sd.folio, sd.afavor, sd.municipio, sd.dotacion, sd.fecha_registro, sd.pdf_docs, sd.pdf_docs_coord, d.programa, sd.cancelado, sd.nota_cancelacion, sd.verificado
        FROM registro_salidas sd
        INNER JOIN registro_salidas_registradas sr ON sd.folio = sr.folio
        INNER JOIN dotaciones d ON sr.clave = d.clave
        WHERE sd.id_almacen = ?");
        $query->bind_param("s", $almacen);
    } else {
        $query = $conn->prepare("SELECT DISTINCT sd.folio, sd.afavor, sd.municipio, sd.dotacion, sd.fecha_registro, sd.pdf_docs, sd.pdf_docs_coord, d.programa, sd.cancelado, sd.nota_cancelacion, sd.verificado
        FROM registro_salidas sd
        INNER JOIN registro_salidas_registradas sr ON sd.folio = sr.folio
        INNER JOIN dotaciones d ON sr.clave = d.clave");
    }
    if ($query->execute()) {
        $query->bind_result($folio, $afavor, $municipio, $dotacion, $fecha, $pdf_docs, $pdf_docs_coord, $programa, $activo, $nota_cancelacion, $verificado);
        $query->store_result();
        if ($query->num_rows > 0) {
            ?>
            <table id="tablaRegistros" style="padding-bottom: 50px;">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>A favor de</th>
                        <th>Municipio</th>
                        <th>Dotacion</th>
                        <th>Fecha de registro</th>
                        <th>Salida</th>
                        <th>Documentos</th>
                        <?php if($_SESSION['rol'] == 3 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1){ ?>
                            <th>Documentos</th>
                        <?php } ?>
                        <?php if($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1){ ?>
                            <th>Cancelar</th>
                        <?php } ?>
                        <?php if($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1){ ?>
                            <th>Verificar</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($query->fetch()) {
                        if($activo == 1){
                            ?>
                            <tr class="tablaCancelado">
                            <?php
                        } else if($verificado == 1){
                            ?>
                            <tr class="tablaVerificado">
                            <?php
                        } else {
                            ?>
                            <tr>
                            <?php
                        }
                        ?>
                        <td class="t-center" data-search="<?php echo $programa, $folio ?>"><?php echo $folio ?></td>
                        <td data-tooltip="<?php echo $programa ?>"><?php echo $afavor ?></td>
                        <td class="t-center"><?php echo $municipio ?></td>
                        <td class="t-center"><?php echo $dotacion ?></td>
                        <td class="t-center"><?php echo $fecha ?></td>
                        <td class="t-center"><a data-tooltip="Consultar registro de salida" onclick="consultarPDFSalidas(<?php echo $folio ?>, 'portrait', false)"><i
                                    class="bi bi-file-earmark-text"></i></a></td>

                        <!-- Subir documentos salida de almacen -->
                        <?php
                        if ($pdf_docs != null && ($activo != 1 && $verificado != 1)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'Salidas', <?php echo $_SESSION['rol'] ?>, true)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if ($pdf_docs != null && ($activo == 1 || $verificado == 1)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'Salidas', <?php echo $_SESSION['rol'] ?>, false)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if($pdf_docs == null && ($activo == 1 || $verificado == 1)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                            <?php
                        } else if ($pdf_docs == null && ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4 && $_SESSION['rol'] != 5)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Subir documentos" onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>, 'Salidas')"><i class="bi bi-cloud-upload"></i></a></td>
                            <?php
                        } else if ($pdf_docs == null && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 1 || $_SESSION['rol'] == 5)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                            <?php
                        }

                        /* Subir documentos de coordinador saida de almacen */
                        if($pdf_docs_coord != null && ($activo != 1 && $verificado != 1) && ($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5)){
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'SalidasCoord', <?php echo $_SESSION['rol'] ?>, false)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if($pdf_docs_coord != null && ($activo != 1 && $verificado != 1) && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 1)){
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'SalidasCoord', <?php echo $_SESSION['rol'] ?>, true)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if($pdf_docs_coord != null && ($activo == 1 || $verificado == 1) && ($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5)){
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'SalidasCoord', <?php echo $_SESSION['rol'] ?>, false)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if($pdf_docs_coord != null && ($activo == 1 || $verificado == 1) && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 1)){
                            ?>
                            <td class="t-center"><a data-tooltip="Consultar documentos" onclick="consultarDoc(<?php echo $folio ?>,'SalidasCoord', <?php echo $_SESSION['rol'] ?>, false)"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                            <?php
                        } else if ($pdf_docs_coord == null && ($activo == 1 || $verificado == 1) && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 1 || $_SESSION['rol'] == 4)) {
                            ?>
                            <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                            <?php
                        } else if ($pdf_docs_coord == null && $_SESSION['rol'] == 3 || $_SESSION['rol'] == 1){
                            ?>
                            <td class="t-center"><a data-tooltip="Subir documentos" onclick="UploadDoc('Sube tus documentos', <?php echo $folio ?>, 'SalidasCoord')"><i class="bi bi-cloud-upload"></i></a></td>
                            <?php
                        } else if ($pdf_docs_coord == null && ($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5)){
                            ?>
                            <td class="t-center"><a data-tooltip="Sin documentos"><i class="bi bi-file-earmark-x"></i></a></td>
                            <?php
                        }

                        /* Cancelar registro de salidas */
                        if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 1) {
                            ?>
                            <td class="t-center"><a data-tooltip="Cancelar">
                                <label class="switch">
                                    <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>" disabled>
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        } else if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 0) {
                            ?>
                            <td class="t-center"><a data-tooltip="Cancelar">
                                <label class="switch">
                                    <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>">
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        } else if (($_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 1) {
                            ?>
                            <td class="t-center"><a data-tooltip="Cancelar" onclick="WaitDoc('Nota de cancelacion', '<?php echo $nota_cancelacion ?>', 'CloseResponse()');">
                                <label class="switch">
                                    <input type="checkbox" class="activo" name="activo" value="<?php echo $folio ?>" checked disabled>
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        }

                        /* Verificar registro de salidas */
                        if (($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $activo == 1) {
                            ?>
                            <td class="t-center"><a data-tooltip="Verificar">
                                <label class="switch">
                                    <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>" disabled>
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        } else if (($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 0) {
                            ?>
                            <td class="t-center"><a data-tooltip="Verificar">
                                <label class="switch">
                                    <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>">
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        } else if (($_SESSION['rol'] == 5 || $_SESSION['rol'] == 1) && $verificado == 1) {
                            ?>
                            <td class="t-center"><a data-tooltip="Verificar">
                                <label class="switch">
                                    <input type="checkbox" class="verificado" name="verificado" value="<?php echo $folio ?>" checked disabled>
                                    <span class="slider round"></span>
                                </label>
                            </a></td>
                            <?php
                        }
                    }
                    ?>
                    </tr>
                </tbody>
            </table>
            <?php
        } else {
            echo "<h3>No hay registros de salidas</h3>";
        }
    } else {
        echo "<h3>No se pudieron obtener los registros de salidas ". $conn->error ."</h3>";
    }
}