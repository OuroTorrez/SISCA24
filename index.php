<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - DIF Michoacán</title>
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/generalStyles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <h1>Bienvenido <?php echo $_SESSION['usuario']; ?></h1>
    <div id="Noticias">
        <h1>Noticias</h1>
        <div id="NoticiasCont">
            <div class="Noticia">
                <h3>Noticia 1</h3>
                <p>Descripción de la noticia 1</p>
            </div>
            <div class="Noticia">
                <h3>Noticia 2</h3>
                <p>Descripción de la noticia 2</p>
            </div>
            <div class="Noticia">
                <h3>Noticia 3</h3>
                <p>Descripción de la noticia 3</p>
            </div>
        </div>
    </div>
</body>
</html>