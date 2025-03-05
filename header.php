<?php
// Si no hay una sesión iniciada, iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Si no hay un usuario loggeado, redirigir a la página de login
if (!isset($_SESSION['usuario']) || !$_SESSION['LoggedIn']) {
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
        <!-- Header menu Logo y Menú -->
        <div id="HeaderMenu">
            <!-- Header Logo -->
            <a href="index.php"><img id="HeaderMenuImage" src="Media/LogoSimpleDIFBlanco.png" alt="Logo DIF Michoacán"></a>
            <!-- Header menu de navegación -->
            <ul class="HeaderMenuNav">
                <li class="MenuNavOption"><a href="index.php">Inicio</a></li><!-- Boton de incio -->


                <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 5 || $_SESSION['rol'] == 6 || $_SESSION['rol'] == 7)) { ?><!-- Menu para coordinador administrativo y control de almacenes -->
                    <li class="MenuNavOption"><a href="consultas.php">Entradas</a><!-- Boton de entradas coordinador -->
                        <ul class="HeaderMenuSubNav">
                            <li class="MenuNavOption"><a href="consultas.php">Consulta</a></li>
                        </ul>
                    </li>
                    <li class="MenuNavOption"><a href="consultasSalidas.php">Salidas</a><!-- Boton de salidas coordinador -->
                        <ul class="HeaderMenuSubNav">
                            <li class="MenuNavOption"><a href="consultasSalidas.php">Consulta</a></li>
                            <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 5) ) { ?>
                                                        <hr>
                                                        <li class="MenuNavOption"><a href="consultasSalidasMontos.php">Montos</a></li><!-- Submenu de consulta de salidas con montos -->
                                                        <?php } ?>
                        </ul>
                    </li>


                <?php } else { ?> <!-- Menu para almacenista, usuario común -->
                    <li class="MenuNavOption"><a href="entradas.php">Entradas</a><!-- Boton de entradas -->
                        <ul class="HeaderMenuSubNav">
                            <li class="MenuNavOption"><a href="entradas.php">Captura</a></li><!-- Submenu de captura de entradas -->
                            <hr>
                            <li class="MenuNavOption"><a href="consultas.php">Consulta</a></li><!-- Submenu de consulta de entradas -->
                        </ul>
                    </li>
                    <li class="MenuNavOption"><a href="salidas.php">Salidas</a><!-- Boton de salidas -->
                        <ul class="HeaderMenuSubNav">
                            <li class="MenuNavOption"><a href="salidas.php">Captura</a></li><!-- Submenu de captura de salidas -->
                            <hr>
                            <li class="MenuNavOption"><a href="consultasSalidas.php">Consulta</a></li><!-- Submenu de consulta de salidas -->
                            <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 4 || $_SESSION['rol'] == 5) ) { ?>
                                                        <hr>
                                                        <li class="MenuNavOption"><a href="consultasSalidasMontos.php">Montos</a></li><!-- Submenu de consulta de salidas con montos -->
                                                        <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <li class="MenuNavOption"><a href="existencias.php">Existencias</a><!-- Boton de existencias -->
                    <ul class="HeaderMenuSubNav">
                        <li class="MenuNavOption"><a href="existencias.php">Actuales</a></li><!-- Submenu de existencias -->
                        <hr>
                        <li class="MenuNavOption"><a href="historicos.php">Acta</a></li><!-- Submenu de existencias -->
                    </ul>
                </li>


                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1) { ?> <!-- Opción solo para administradores -->
                    <li class="MenuNavOption"><a href="controlUsuarios.php">Usuarios</a></li> <!-- Boton de registro y modificación de usuarios -->
                <?php } ?>
            </ul>
        </div>
        <!-- Header cuenta Menú de usuario -->
        <div id="HeaderAccount">
            <div id="HeaderAccountButton"><img id="HeaderAccountImage" src="Media/AccountIconBlanco.png" alt="Imagen de perfil"></div>
            <div id="HeaderAccountMenu">
                <span style="text-align: center; text-transform: uppercase; padding: 15px 20px; font-size: larger;"><?php echo $_SESSION['usuario'] ?></span>
                <hr>
                <a href="perfil.php"><i class="bi bi-person-fill"></i> Mi perfil</a>
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