<?php
session_start();
$_SESSION['usuario'] = NULL;
$_SESSION['LoggedIn'] = false;
$_SESSION['rol'] = NULL;
session_destroy();
header('Location: index.php');
?>