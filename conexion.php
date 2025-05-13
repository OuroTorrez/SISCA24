<?php
function conectar(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sisca25";
    
    try {
      $conn = new mysqli($servername, $username, $password, $dbname);
      return $conn;
    } catch (Exception $e) {
      return $e->getMessage();
    }
}
?>