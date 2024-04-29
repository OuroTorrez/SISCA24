<?php
include 'conexion.php';
// Check if a session is already started
session_start();
    if(isset($_SESSION['usuario']) || isset($_SESSION['LoggedIn'])){
        header('Location: index.php');
    }
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Create a new connection to the database
        $conn = conectar();
        if(empty($conn) || !($conn instanceof mysqli)){
            $error = "⛔Error de conexión: <br>" . $conn;
        } else {
            // Prepare the query (SELECT * FROM usuarios WHERE usuario = ? AND estado = 'activo'

        $query = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND estado = 'activo'");
        $query->bind_param("s", $username);
        if($query->execute()){
            $result = $query->get_result();
            if(mysqli_num_rows($result) > 0){
                // Comprobar contraseña
                $row = $result->fetch_assoc();
                if(password_verify($password, $row['contrasena'])){
                    session_start();
                    $_SESSION['usuario'] = $row['usuario'];
                    $_SESSION['rol'] = $row['id_rol'];
                    $_SESSION['LoggedIn'] = true;
                    $conn->close();
                    header('Location: index.php');
                } else {
                    $error = "⛔Contraseña incorrecta";
                }
            } else {
                $error = "⛔Usuario no encontrado o ha sido deshabilitado";
            }
        } else {
            $error = "⛔Error: " . $query . "<br>" . $query->error;
        }
    }

  
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>LogIn - DIF Michoacán</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="Styles/generalStyles.css">
    <link rel="stylesheet" type="text/css" href="Styles/loginStyles.css">
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <div id="Login">
        <img id="LoginImage" src="Media/LogoSimpleDIF.png" alt="Logo">
        <h1>INICIO DE SESIÓN</h1>
        <?php if (isset($error)) { ?>
            <div id="LoginError">
                <p><?php echo $error; ?></p>
            </div>
        <?php } ?>
        <form id="LoginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username" class="req">Usuario:</label>
            <input type="text" name="username" id="username" placeholder="Usuario" required><br><br>
            <label for="password" class="req">Contraseña:</label>
            <input type="password" name="password" id="password" placeholder="Contraseña" required><br><br>
            <button type="submit">
            <i class="bi bi-box-arrow-in-right"></i>
                <span>Inicia sesión</span>
            </button>
        </form>
    </div>
</body>
</html>
<script>
 if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}
</script>