<?php
    session_start();
    if(!isset($_SESSION['usuario']) || !$_SESSION['LoggedIn']){
        header('Location: login.php');
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header - DIF Michoacán</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/headerStyles.css">
    <link rel="stylesheet" href="Styles/generalStyles.css">
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Scripts -->
</head>

<body>
    <div id="Header">
        <div id="HeaderMenu">
            <a href="index.php"><img id="HeaderMenuImage" src="Media/LogoSimpleDIFBlanco.png" alt="Logo DIF Michoacán"></a>
            <ul class="HeaderMenuNav">
                <li class="MenuNavOption"><a href="index.php">Inicio</a></li>
                <li class="MenuNavOption">
                    <a href="entradas.php">Entradas</a>
                    <ul class="HeaderMenuSubNav">
                        <li class="MenuNavOption"><a href="entradas.php">Captura</a></li>
                        <hr>
                        <li class="MenuNavOption"><a href="consultas.php">Consulta</a></li>
                    </ul>
                </li>
                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 1){ ?>
                <li class="MenuNavOption"><a href="registro.php">Usuarios</a></li>
                <?php } ?>
            </ul>
        </div>
        <div id="HeaderAccount">
            <div id="HeaderAccountButton"><img id="HeaderAccountImage" src="Media/AccountIconBlanco.png"
                    alt="Imagen de perfil"></div>
            <div id="HeaderAccountMenu">
                <span style="text-align: center; text-transform: uppercase; padding: 15px 20px; font-size: larger;"><?php echo $_SESSION ['usuario']?></span>
                <hr>
                <a href="#"><i class="bi bi-person-fill"></i> Mi perfil</a>
                <hr>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </div>
        </div>
    </div>
</body>

</html>
<script>
// Boton clickeable de la cuenta
document.getElementById('HeaderAccountButton').addEventListener('click', function() {
    document.getElementById('HeaderAccountMenu').classList.toggle('show');
});
</script>