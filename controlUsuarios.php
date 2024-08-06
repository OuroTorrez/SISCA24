<?php
include 'conexion.php';
$conn = conectar();
if (empty($conn) || !($conn instanceof mysqli)) {
    $error = "⛔Error de conexión: <br>" . $conn;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Usuarios - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/controlUsuariosStyles.css">
</head>

<body>
<?php if (isset($error)) { ?>
    <div id="Errores">
        <div id="Error">
            <p><?php echo $error; ?></p>
        </div>
    </div>
<?php } ?>
<!-- Include header principal -->
<?php include 'header.php'; ?>
<!-- Include ventana de respuesta -->
<?php include 'ventanaResponse.php'; ?>

<!-- Contenido -->
<h1 class="PageTitle">Control de Usuarios</h1>
<div id="ContenidoUsuarios">
    <div id="MenuUsuarios">
        <div class="OpcMenu">
            <a class="OpcMenuButton active" data-target="Registrar" onclick="showConfig('Registrar')">Registro</a>
            <hr>
            <a class="OpcMenuButton" data-target="Modificar" onclick="showConfig('Modificar')">Modificar</a>
        </div>
    </div>
    <div id="ConfiguracionesUsuarios" class="OpcMenuContent">

    </div>
</div>
</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        showConfig('Registrar');
    });

    //Controla la visibilidad de los elementos de configuración
    function showConfig(target) {
        var configs = document.getElementById("ConfiguracionesUsuarios");
        var buttons = document.querySelectorAll(".OpcMenuButton");

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
                case 'Registrar':
                    configs.innerHTML =
                        `
                        <h1>Registro</h1>
                        <h3>Registro de nuevos usuarios</h3>
                        <hr>
                        <form id="FormRegistroUsuario" class="ConfiguracionesUsuariosForm">
                        <div class="FormData" style="width: 33.3%;">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" placeholder="Nombre(s)" required>
                        </div>
                        <div class="FormData" style="width: 33.3%;">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" placeholder="Apellido paterno" required>
                        </div>
                        <div class="FormData" style="width: 33.3%;">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" name="apellido_materno" id="apellido_materno" placeholder="Apellido materno" required>
                        </div>
                        <div class="FormData" style="width: 50%;">
                            <label for="rol">Rol:</label>
                            <select name="rol" id="rol">
                                <?php
                        $sql = "SELECT id_rol, rol FROM roles";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                echo "<option value='" . $row['id_rol'] . "'>" . $row['rol'] . "</option>";
                            }
                        }
                        ?>
                            </select>
                        </div>
                        <div class="FormData" style="width: 50%;">
                            <label for="almacen">Almacen:</label>
                            <select name="almacen" id="almacen">
                                <?php
                        $sql = "SELECT id_almacen, almacen FROM almacenes";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                echo "<option value='" . $row['id_almacen'] . "'>" . $row['almacen'] . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                            </select>
                        </div>
                        <div class="FormData" style="width: 50%;">
                            <label for="usuario">Usuario:</label>
                            <input type="text" name="usuario" id="usuario" placeholder="Usuario (para inicio de sesión)" required>
                        </div>
                        <div class="FormData" style="width: 50%;">
                            <label for="contrasena">Contraseña:</label>
                            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                        </div>
                        <div class="FormData" style="width: 100%;">
                            <button type="submit">
                                <i class="bi bi-floppy"></i>
                                <span>Registrar</span>
                            </button>
                        </div>
                        `;
                    // Enviar el formulario de registro de usuarios
                    $('#FormRegistroUsuario').submit(function (e) {
                        e.preventDefault();
                        var formData = new FormData(this);
                        formData.append('accion', 'registrarUsuario');
                        $.ajax({
                            url: 'handleControlUsuarios.php',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            beforeSend: function () {
                                WaitDoc('Registrando usuario', 'Por favor espere un momento', 'location.reload()');
                            },
                            success: function (response) {
                                console.log(response);
                                if (response === 'Success') {
                                    WaitDoc('Usuario registrado', 'El usuario se registró correctamente', 'location.reload()');
                                } else {
                                    WaitDoc('Error al registrar', 'Ocurrió un error al registrar el usuario: ' + response, 'CloseResponse()');
                                }
                            },
                            error: function () {
                                WaitDoc('Error al registrar', 'Ocurrió un error al enviar los datos para el registro de usuario', 'CloseResponse()');
                            }
                        });
                    });
                    break;
                case 'Modificar':
                    configs.innerHTML =
                        `
                            <h1>Modificar</h1>
                            <h3>Modificar usuarios existentes</h3>
                            <hr>
                            <table id='tablaUsuarios'>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Contraseña</th>
                                        <th>Modificar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $conn = conectar();
                                        $query = $conn->prepare("SELECT id, CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre, usuario FROM usuarios");
                                        $query->execute();
                                        $query->bind_result($id_usuario, $nombre, $usuario);
                                        $query->store_result();
                                        while($query->fetch()){
                                            echo "<tr>";
                                            echo "<td>$id_usuario</td>";
                                            echo "<td>$nombre</td>";
                                            echo "<td>$usuario</td>";
                                            echo "<td><input style='width: 100%;' type='text' placeholder='Nueva contraseña' id='contrasena-$id_usuario' required></td>";
                                            echo "<td><button onclick='ModificarUsuario($id_usuario)'><i class='bi bi-pencil'></i></button></td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                            `;

                    // Inicializar DataTables
                    $('#tablaUsuarios').DataTable({
                        "language": {
                            "lengthMenu": "Mostrar _MENU_ usuarios por página",
                            "zeroRecords": "No se encontraron usuarios",
                            "info": "Mostrando página _PAGE_ de _PAGES_",
                            "infoEmpty": "No se encontraron usuarios con esos criterios",
                            "infoFiltered": "(filtrado de _MAX_ usuarios totales)",
                            "search": "Buscar:",
                            "paginate": {
                                "first": "<i class='bi bi-chevron-double-left'></i>",
                                "last": "<i class='bi bi-chevron-double-right'></i>",
                                "next": "<i class='bi bi-chevron-right'></i>",
                                "previous": "<i class='bi bi-chevron-left'></i>"
                            }
                        },
                        "order": [
                            [0, "asc"]
                        ],
                        "columnDefs": [{
                            "targets": [4],
                            "orderable": false
                        }]
                    });
                    break;
                default:
                    configs.innerHTML = ""; // Limpiar el contenido si no se encuentra el target
                    break;
            }

            configs.classList.remove('hide'); // Muestra el elemento nuevamente
        }, 500); // Espera a que la transición termine
    }

    // Envia la modificación de la contraseña
    function ModificarUsuario(id) {
        var idCampoContrasena = `#contrasena-${id}`;
        var contrasena = document.querySelector(idCampoContrasena).value;

        alert(contrasena);
        console.log(contrasena);
        $.ajax({
            url: 'handleControlUsuarios.php',
            type: 'POST',
            data: {
                accion: 'modificarUsuario',
                id: id,
                contrasena: contrasena
            },
            success: function(response) {
                if (response === 'Success') {
                    WaitDoc('Usuario modificado', 'La contraseña del usuario se modificó correctamente', 'location.reload()');
                } else {
                    WaitDoc('Error al modificar', 'Ocurrió un error al modificar la contraseña del usuario: ' + response, 'CloseResponse()');
                }
            },
            error: function() {
                WaitDoc('Error al modificar', 'Ocurrió un error al enviar los datos para modificar la contraseña del usuario', 'CloseResponse()');
            }
        });
    }

    showConfig('Registrar'); // Mostrar la configuración por defecto al cargar la página
</script>