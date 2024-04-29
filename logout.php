<?php
session_start();
$_SESSION['usuario'] = NULL;
$_SESSION['LoggedIn'] = false;
session_destroy();
header('Location: index.php');
?>