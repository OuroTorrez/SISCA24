<?php
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}

$mensajeCambios = "";
$configDefault = "general";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si se está actualizando la información general
    if (isset($_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['usuario'])) {
        session_start();
        $configDefault = "general";
        $nombre = $_POST["nombre"];
        $apellido_paterno = $_POST["apellido_paterno"];
        $apellido_materno = $_POST["apellido_materno"];
        $usuario = $_POST["usuario"];

        // Prepare the query
        $query = $conn->prepare("UPDATE usuarios SET nombres = ?, apellido_paterno = ?, apellido_materno = ?, usuario = ? WHERE usuario = ?");
        $query->bind_param("sssss", $nombre, $apellido_paterno, $apellido_materno, $usuario, $_SESSION['usuario']);
    if ($query->execute()) {
        $query->close();
        $mensajeCambios = "✔️Datos actualizados correctamente.";
        if ($_SESSION['usuario'] != $usuario) {
            $_SESSION['usuario'] = $usuario;
        }
    } else {
        $error = "⛔Error: " . $query . "<br>" . $query->error;
    }
    } 
    // Verifica si se está actualizando la contraseña
    elseif (isset($_POST['actual'], $_POST['nueva'], $_POST['repetir'])) {
        session_start();
        $configDefault = "seguridad";
        // Obtén la contraseña actual del usuario desde la base de datos
        $query = $conn->prepare("SELECT contrasena FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $_SESSION['usuario']);
        $query->execute();
        $query->bind_result($contraseña);
        $query->store_result();
        $query->fetch();
        $query->close();
        // Verifica si la contraseña actual ingresada coincide con la contraseña almacenada
        if (password_verify($_POST['actual'], $contraseña)) {
            // Procesa la actualización de la contraseña
            $nueva = $_POST['nueva'];
            $hash = password_hash($nueva, PASSWORD_BCRYPT);
            $query = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario = ?");
            $query->bind_param("ss", $hash, $_SESSION['usuario']);
            if ($query->execute()) {
                $query->close();
                $mensajeCambios = "✔️Contraseña actualizada correctamente.";
            } else {
                $error = "⛔Error: " . $query . "<br>" . $query->error;
            }
        } else {
            // Muestra un mensaje de error indicando que la contraseña actual es incorrecta
            $error = "⛔Error: La contraseña actual es incorrecta.";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Perfil - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/perfilStyles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php if (isset($error)) { ?>
        <div id="Errores">
            <div id="Error">
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php } ?>
    <h1 class="PageTitle">Información y configuración de perfil</h1>
    <div id="Perfil">
        <!-- Perfil y menú de configuración -->
        <div id="PerfilMenuDatos">
            <img id="PerfilMenuImage" src="Media/AccountIcon.png" alt="Imagen de perfil">
            <?php
                $query = $conn->prepare("SELECT u.nombres, u.apellido_paterno, u.apellido_materno, u.usuario, a.almacen, r.rol AS almacen 
                FROM usuarios u 
                INNER JOIN almacenes a ON u.id_almacen = a.id_almacen 
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.usuario = ?");
                $query->bind_param("s", $_SESSION['usuario']);
                $query->execute();
                $query->bind_result($nombre, $apellido_paterno, $apellido_materno, $usuario, $almacen, $rol);
                $query->store_result();
                $query->fetch();
                $query->close();
            ?>
            <h2><?php echo $rol ?></h2>
            <hr>
            <h2><?php echo $nombre . " " . $apellido_paterno . " " . $apellido_materno ?></h2>
            <span><?php echo $usuario ?></span>
            <hr>
            <h2><?php echo $almacen ?></h2>
            <div id="PerfilMenu">
                <a class="PerfilMenuButton active" data-target="general" onclick="showConfig('general')">General</a>
                <hr>
                <a class="PerfilMenuButton" data-target="seguridad" onclick="showConfig('seguridad')">Seguridad</a>
            </div>
        </div>
        <div id="PerfilConfigs" class="show">

        </div>
    </div>
</body>
</html>
<script>
showConfig('<?php echo $configDefault; ?>'); // Mostrar la configuración por defecto al cargar la página

    //Controla la visibilidad de los elementos de configuración
function showConfig(target) {
    var configs = document.getElementById("PerfilConfigs");
    var buttons = document.querySelectorAll(".PerfilMenuButton");

    configs.classList.add('hide'); // Oculta el elemento actual
    // Iterar sobre todos los botones y ajustar la clase activa
    buttons.forEach(button => {
            if (button.getAttribute('data-target') === target) {
                button.classList.add('active'); // Marcar el botón como activo si coincide con el target
            } else {
                button.classList.remove('active'); // Quitar la clase activa de los otros botones
            }
        });
    setTimeout(() => {
        // Cambiar el contenido según el target
        switch (target) {
            case 'general':
                configs.innerHTML = 
                `
                    <h1>Información general</h1>
                    <?php if (isset($mensajeCambios) && $mensajeCambios != "") { ?>
                            <div id="Avisos">
                                <div id="Aviso">
                                    <p><?php echo $mensajeCambios; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    <form id="formGeneral" action="perfil.php" method="post">
                        <div class="formInput">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" value="<?php echo $nombre ?>" required>
                        </div>
                        <div class="formInput">
                            <label for="apellido_paterno">Apellido paterno:</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" value="<?php echo $apellido_paterno ?>" required>
                        </div>
                        <div class="formInput">
                            <label for="apellido_materno">Apellido materno:</label>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="<?php echo $apellido_materno ?>" required>
                        </div>
                        <div class="formInput">
                            <label for="usuario">Usuario:</label>
                            <input type="text" name="usuario" id="usuario" value="<?php echo $usuario ?>" required>
                        </div>
                        <div class="formInput">
                            <label for="almacen">Almacen:</label>
                            <input type="text" name="almacen" id="almacen" value="<?php echo $almacen ?>" disabled>
                        </div>
                        <hr>
                        <button type="submit">
                            <i class="bi bi-floppy"></i>
                            <span>Actualizar datos</span>
                        </button>
                    </form>
                `;
                break;
            case 'seguridad':
                configs.innerHTML = 
                `
                    <h1>Seguridad</h1>
                    <?php if (isset($mensajeCambios) && $mensajeCambios != "") { ?>
                        <div id="Avisos">
                            <div id="Aviso">
                                <p><?php echo $mensajeCambios; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <form id="formSeguridad" action="perfil.php" method="post">
                        <div class="formInput">
                            <label for="actual">Contraseña actual:</label>
                            <input type="password" name="actual" id="actual" required>
                        </div>
                        <div class="formInput">
                            <label for="nueva">Nueva contraseña:</label>
                            <input type="password" name="nueva" id="nueva" required>
                        </div>
                        <div class="formInput">
                            <label for="repetir">Repetir contraseña:</label>
                            <input type="password" name="repetir" id="repetir" required>
                        </div>
                        <div id="mensajeContraseña" class="mensaje"></div>
                        <hr>
                        <button type="submit">
                            <i class="bi bi-floppy"></i>
                            <span>Actualizar datos</span>
                        </button>
                    </form>
                `;
                break;
            // Puedes agregar más casos según los botones que añadas
            default:
                configs.innerHTML = ""; // Limpiar el contenido si no se encuentra el target
                break;
        }

        configs.classList.remove('hide'); // Muestra el elemento nuevamente
    }, 500); // Espera a que la transición termine
}

document.getElementById("PerfilConfigs").addEventListener("input", function(event) {
        // Verificar si el elemento que disparó el evento es un campo de contraseña
        if (event.target.matches("#nueva, #repetir")) {
            verificarContraseñas();
        }
    });

    // Función para verificar si las contraseñas coinciden
    function verificarContraseñas() {
        var contraseña = document.getElementById("nueva").value;
        var repetirContraseña = document.getElementById("repetir").value;
        var submitButton = document.querySelector('button[type="submit"]');

        // Verificar si las contraseñas coinciden
        if(contraseña != "" && repetirContraseña != ""){
            if (contraseña === repetirContraseña) {
                document.getElementById("mensajeContraseña").style.color = "green";
                document.getElementById("mensajeContraseña").innerText = "✔️ Las contraseñas coinciden";
                document.getElementById("mensajeContraseña").classList.remove("error");
                submitButton.disabled = false; // Habilitar el botón de enviar
            } else {
                document.getElementById("mensajeContraseña").style.color = "red";
                document.getElementById("mensajeContraseña").innerText = "❌ Las contraseñas no coinciden";
                document.getElementById("mensajeContraseña").classList.add("error");
                submitButton.disabled = true; // Deshabilitar el botón de enviar
            }
        } else {
            document.getElementById("mensajeContraseña").innerText = "";
        }
    }

if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}
</script>