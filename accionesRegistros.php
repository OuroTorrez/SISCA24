<?php
    session_start();
    include 'conexion.php';
    $conn = conectar();
    if (empty($conn) || !($conn instanceof mysqli)) {
        $error = "⛔Error de conexión: <br>" . $conn;
    }
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'Cancelar':
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
            break;
            case 'Verificar':
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
                break;
        }
    }