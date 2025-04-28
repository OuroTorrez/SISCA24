



<?php
session_start();
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
if (isset($_POST['accion'])) {
    if ($_POST['accion'] == 'Cancelar'){
        if ($_POST['tipo'] == 'Entradas') {
            $query = $conn->prepare("UPDATE registro_entradas SET cancelado = 1, nota_cancelacion = ? WHERE folio = ? ");
        } else if ($_POST['tipo'] == 'Salidas'){
            $query = $conn->prepare("UPDATE registro_salidas SET cancelado = 1, nota_cancelacion = ? WHERE folio = ?");
        }
        $query->bind_param("ss",$_POST['nota'], $_POST['folio']);
        if($query->execute()) {
            //Obtener existencias
            $query = $conn->prepare("SELECT id_almacen FROM registro_entradas WHERE folio = ?");
            $query->bind_param("s", $_POST['folio']);
            $query->execute();
            $query->bind_result($almacen);
            $query->store_result();
            $query->fetch();
            $query->close();

            $query = $conn->prepare("SELECT COALESCE((SELECT SUM(rer.cantidad) FROM registro_entradas_registradas rer 
                                                            INNER JOIN registro_entradas re ON rer.folio = re.folio 
                                                            WHERE rer.clave = d.clave AND re.cancelado = 0 AND re.id_almacen = ?), 0) 
                                                - COALESCE((SELECT SUM(rsr.cantidad) FROM registro_salidas_registradas rsr 
                                                            INNER JOIN registro_salidas rs ON rsr.folio = rs.folio 
                                                            WHERE rsr.clave = d.clave AND rs.cancelado = 0 AND rs.id_almacen = ?), 0) AS existencias
                                                FROM dotaciones d");
            $query->bind_param("ii", $almacen, $almacen);
            $query->execute();
            $query->bind_result($existencias);
            $query->store_result();
            while ($query->fetch()) {
                if ($existencias < 0) {
                    if ($_POST['tipo'] == 'Entradas') {
                        $query = $conn->prepare("UPDATE registro_entradas SET cancelado = 0, nota_cancelacion = NULL WHERE folio = ? ");
                    } else if ($_POST['tipo'] == 'Salidas'){
                        $query = $conn->prepare("UPDATE registro_salidas SET cancelado = 0, nota_cancelacion = NULL WHERE folio = ?");
                    }
                    $query->bind_param("s", $_POST['folio']);
                    $query->execute();
                    echo "Error: Cancelar este registro dejaría las existencias en un número negativo.";
                    return;
                }
            }
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    else if ($_POST['accion'] == 'Verificar'){
        if($_POST['tipo'] == 'Entradas'){
            $query = $conn->prepare("UPDATE registro_entradas SET verificado = 1 WHERE folio = ? ");
        } else if($_POST['tipo'] == 'Salidas'){
            $query = $conn->prepare("UPDATE registro_salidas SET verificado = 1 WHERE folio = ? ");
        }
        $query->bind_param("s", $_POST['folio']);
        if($query->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    else if ($_POST['accion'] == 'FormEntrada') {
        //$query = $conn->prepare("SELECT id_entrada, dotacion, nota, nota_modificacion FROM registro_entradas WHERE folio = ?");
        $query = $conn->prepare("
            SELECT 
                re.id_entrada, 
                re.dotacion, 
                re.nota, 
                re.nota_modificacion,
                d.programa 
            FROM 
                registro_entradas AS re
            JOIN 
                registro_entradas_registradas AS rer 
                ON re.folio = rer.folio
            JOIN 
                dotaciones AS d 
                ON rer.clave = d.clave
            WHERE 
                re.folio = ?
        ");
        $query->bind_param("i", $_POST['folio']);
        $query->execute();
        $query->bind_result($id_entradaBD, $dotacionBD, $notaBD, $nota_modificacionBD, $programa);
        $query->fetch();
        $query->close();
        ?>
        <form id="FormModificar" class="FormModify">
            <input type="hidden" name="folio" id="folio" value="<?php echo $_POST['folio']; ?>">
            <div class="FormData" style="width: 100%">
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
                            if($id == $id_entradaBD){
                                echo "<option value='$id' selected>$id - $tipo_entrada</option>";
                            } else {
                                echo "<option value='$id'>$id - $tipo_entrada</option>";
                            }
                        }
                    } else {
                        echo "<option value=''>No options found.</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="dotacion" class="req">Dotación:</label>
                <select name="dotacion" id="dotacion" required>
                    <?php
                        for ($i = 1 ; $i<=9 ; $i++) {
                            if($i == $dotacionBD){
                                echo "<option value='$i' selected>$i</option>";
                            } else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                    ?>
                    <?php
                    // Generar opciones del 1 al 8
                    for ($i = 1; $i <= 8; $i++) {
                        $selected = ($i == $dotacionBD) ? "selected" : "";
                        echo "<option value='$i' $selected>$i</option>";
                    }

                    // Lógica para la opción 9 o 9 - Ampliación
                    if ($programa == 'Desayunos Escolares Calientes') {
                        $selected = ($dotacionBD == 9) ? "selected" : "";
                        echo "<option value='9' $selected>9</option>";
                    } else {
                        $selected = ($dotacionBD == "9 - Ampliación") ? "selected" : "";
                        echo "<option value='9 - Ampliación' $selected>9 - Ampliación</option>";
                    }
                    ?> 
                </select>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="nota">Nota:</label>
                <textarea name="nota" id="nota" placeholder="Nota (máximo 255 carácteres)" maxlength="255"><?php echo $notaBD; ?></textarea>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="nota_modificacion">Nota de modificación:</label>
                <textarea name="nota_modificacion" id="nota_modificacion" placeholder="Nota de modificación (máximo 500 carácteres)" maxlength="500"><?php echo $nota_modificacionBD; ?></textarea>
            </div>
            <div class="FormData" style="width: 100%">
                <button type="button" class="ResponseVerifyButton" onclick="enviarModificacion('modificarEntrada')">
                    <i class="bi bi-pencil"></i>
                    <span>Modificar entrada</span>
                </button>
            </div>
        </form>
        <?php
    }
    else if ($_POST['accion'] == 'FormSalida') {
        $query = $conn->prepare("
    SELECT 
        rs.id_salida, 
        rs.dotacion, 
        rs.nota, 
        rs.nota_modificacion, 
        rs.recibe, 
        rs.monto, 
        d.programa 
    FROM 
        registro_salidas AS rs
    JOIN 
        registro_salidas_registradas AS rsr 
        ON rs.folio = rsr.folio
    JOIN 
        dotaciones AS d 
        ON rsr.clave = d.clave
    WHERE 
        rs.folio = ?
");

        $query->bind_param("i", $_POST['folio']);
        $query->execute();
        $query->bind_result($id_salidaBD, $dotacionBD, $notaBD, $nota_modificacionBD, $recibe, $monto, $programa);
        $query->fetch();
        $query->close();
        ?>
        <form id="FormModificar" class="FormModify">
            <input type="hidden" name="folio" id="folio" value="<?php echo $_POST['folio']; ?>">
            <div class="FormData" style="width: 100%">
                <label for="salida" class="req">Tipo de salida:</label>
                <select name="salida" id="salida" required>
                    <?php
                    // Muestra los tipos de entrada posibles en un select
                    $sql = "SELECT * FROM salidas";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        // Output options as select dropdown
                        while ($row = $result->fetch_assoc()) {
                            $id = $row["id_salida"];
                            $tipo_salida = $row["tipo"];
                            if($id == $id_salidaBD){
                                echo "<option value='$id' selected>$id - $tipo_salida</option>";
                            } else {
                                echo "<option value='$id'>$id - $tipo_salida</option>";
                            }
                        }
                    } else {
                        echo "<option value=''>No options found.</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="dotacion" class="req">Dotación:</label>
                <select name="dotacion" id="dotacion" required>
                    <?php
                    // Generar opciones del 1 al 8
                    for ($i = 1; $i <= 8; $i++) {
                        $selected = ($i == $dotacionBD) ? "selected" : "";
                        echo "<option value='$i' $selected>$i</option>";
                    }

                    // Lógica para la opción 9 o 9 - Ampliación
                    if ($programa == 'Desayunos Escolares Calientes') {
                        $selected = ($dotacionBD == 9) ? "selected" : "";
                        echo "<option value='9' $selected>9</option>";
                    } else {
                        $selected = ($dotacionBD == "9 - Ampliación") ? "selected" : "";
                        echo "<option value='9 - Ampliación' $selected>9 - Ampliación</option>";
                    }
                    ?> 
                </select>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="nota">Nota:</label>
                <textarea name="nota" id="nota" placeholder="Nota (máximo 255 carácteres)" maxlength="255"><?php echo $notaBD; ?></textarea>
            </div>
            <div class="FormData" style="width: 100%">
                <label for="nota_modificacion">Nota de modificación:</label>
                <textarea name="nota_modificacion" id="nota_modificacion" placeholder="Nota de modificación (máximo 500 carácteres)" maxlength="500"><?php echo $nota_modificacionBD; ?></textarea>
            </div>
            <div class="FormData">
                <label for="recibe" class="req">Persona que recibe:</label>
                <input type="text" name="recibe" id="recibe" value="<?php echo $recibe; ?>" required>
            </div>
            <div class="FormData">
                <label for="monto" class="req">Monto ($ MXN):</label>
                <input type="number" name="monto" id="monto" min="0" max="999999" step="any" value="<?php echo $monto; ?>" required>
            </div>
            <div class="FormData" style="width: 100%">
                <button type="button" class="ResponseVerifyButton" onclick="enviarModificacion('modificarSalida')">
                    <i class="bi bi-pencil"></i>
                    <span>Modificar entrada</span>
                </button>
            </div>
        </form>
        <?php
    }
    else if ($_POST['accion'] == 'modificarEntrada'){
        $folio = $_POST['folio'];
        $entrada = $_POST['entrada'];
        $dotacion = $_POST['dotacion'];
        $nota = $_POST['nota'];
        $nota_modificacion = $_POST['nota_modificacion'];
        $query = $conn->prepare("UPDATE registro_entradas SET id_entrada = ?, dotacion = ?, nota = ?, nota_modificacion = ? WHERE folio = ?");
        $query->bind_param("iissi", $entrada, $dotacion, $nota, $nota_modificacion, $folio);
        if($query->execute()){
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    else if ($_POST['accion'] == 'modificarSalida'){
        $folio = $_POST['folio'];
        $salida = $_POST['salida'];
        $dotacion = $_POST['dotacion'];
        $nota = $_POST['nota'];
        $nota_modificacion = $_POST['nota_modificacion'];
        $recibe = $_POST['recibe'];
        $monto = $_POST['monto'];
        $query = $conn->prepare("UPDATE registro_salidas SET id_salida = ?, dotacion = ?, nota = ?, nota_modificacion = ?, recibe = ?, monto = ? WHERE folio = ?");
        $query->bind_param("iisssii", $salida, $dotacion, $nota, $nota_modificacion, $recibe, $monto, $folio);
        if($query->execute()){
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}