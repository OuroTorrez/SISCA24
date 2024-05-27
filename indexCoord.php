<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Inicio - DIF Michoacán</title>
    <!-- Styles -->
</head>
<body>
    <?php include 'header.php'; ?>
    <h1>Bienvenido <?php echo $_SESSION['usuario'];?>.</h1>
    <div id="Noticias">
        <h1>Noticias</h1>
        <div id="NoticiasCont">
            <div class="Noticia">
                <h3>Noticia 1</h3>
                <p>Descripción de la noticia 1</p>
            </div>
        </div>
    </div>
</body>
</html>