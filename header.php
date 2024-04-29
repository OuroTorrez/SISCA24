<?php
    session_start();
    if(!isset($_SESSION['usuario']) || !$_SESSION['LoggedIn']){
        header('Location: login.php');
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Header - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/headerStyles.css">
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