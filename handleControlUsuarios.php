<?php
session_start();
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['accion'] == 'registrarUsuario') {
        $nombre = $_POST['nombre'];
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $rol = $_POST['rol'];
        $almacen = $_POST['almacen'];
        $usuario = $_POST['usuario'];
        $password = $_POST['contrasena'];

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = $conn->prepare("INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, id_rol, id_almacen, usuario, contrasena) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("sssiiss", $nombre, $apellido_paterno, $apellido_materno, $rol, $almacen, $usuario, $hash);
        if($query->execute()){
            echo "Success";
        } else {
            echo "Error: " . $query . "<br>" . $query->error;
        }
    }
    else if ($_POST['accion'] == 'modificarUsuario') {
        $id = $_POST['id'];
        $contrasena = $_POST['contrasena'];

        $hash = password_hash($contrasena, PASSWORD_BCRYPT);
        $query = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
        $query->bind_param("si", $hash, $id);
        if($query->execute()){
            echo "Success";
        } else {
            echo "Error: " . $query . "<br>" . $query->error;
        }
    }
}