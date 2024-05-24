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
                    echo "Success";
                } else {
                    echo "Error" . $conn->error;
                }
                break;
        }
    }