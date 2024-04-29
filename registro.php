<?php
include 'conexion.php';
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        $name = $_POST["name"];
        $firstlastName = $_POST["firstlastName"];
        $secondlastName = $_POST["secondlastName"];
        $almacen = $_POST["almacen"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $rol = $_POST["rol"];

        // Create a new connection to the database
        try {
            $conn = conectar();
            if($conn->connect_error){
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $query = $conn->prepare("INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, usuario, contrasena, id_rol, id_almacen) VALUES (?, ?, ?, ?, ?, ?,?)");
            $query->bind_param("sssssii", $name, $firstlastName, $secondlastName, $username, $hash, $rol, $almacen);

            if($query->execute()){
                $conn->close();
                header('Location: index.php');
            } else {
                $error = "Error: " . $query . "<br>" . $query->error;
            }
        } catch(PDOException $e) {
            // Mensaje de error de conexión
            $conn->close();
            $error = "Fallo en la conexión: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My HTML Page</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/generalStyles.css">
    <link rel="stylesheet" href="Styles/loginStyles.css">
</head>
<body>
    <div id="Login">
    <img id="LoginImage" src="Media/LogoSimpleDIF.png" alt="Logo">
        <form id="LoginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="name">Nombre(s):</label>
            <input type="text" name="name" id="name" placeholder="Nombre(s)" required><br><br>
    
            <label for="lastName">Apellido peterno:</label>
            <input type="text" name="firstlastName" id="lastName" placeholder="Apellido paterno" required><br><br>
    
            <label for="lastName">Apellido materno:</label>
            <input type="text" name="secondlastName" id="lastName" placeholder="Apellido materno" required><br><br>
    
            <label for="almacen">Almacen:</label>
            <select name="almacen" id="almacen">
                <?php
                    $conn = conectar();
                    $sql = "SELECT id_almacen, almacen FROM almacenes";
                    $result = $conn->query($sql);
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo "<option value='" . $row['id_almacen'] . "'>". $row['id_almacen'] . " - " . $row['almacen'] . "</option>";
                        }
                    }
                    $conn->close();
                ?>
            </select><br><br>
    
            <label for="username">Usuario:</label>
            <input type="text" name="username" id="username" placeholder="Usuario (para inicio de sesión)" required><br><br>
    
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" placeholder="Contraseña" required><br><br>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol">
                <?php
                    $conn = conectar();
                    $sql = "SELECT id_rol, rol FROM roles";
                    $result = $conn->query($sql);
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo "<option value='" . $row['id_rol'] . "'>". $row['rol'] . "</option>";
                        }
                    }
                    $conn->close();
                ?>
            </select><br><br><br>
    
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>