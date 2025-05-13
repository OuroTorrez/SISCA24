-- Active: 1710947015672@@127.0.0.1@3306@dif michoacan informatica

CREATE Table roles(
    id_rol int NOT NULL PRIMARY KEY COMMENT 'ID del rol',
    rol VARCHAR(100) NOT NULL COMMENT 'Nombre del rol',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripcion del rol'
) COMMENT 'Tabla para almacenar los roles de los usuarios';
INSERT INTO roles(id_rol, rol, descripcion) VALUES
(1, 'Administrador', 'Todos los permisos de administrador'),
(2, 'Almacenista', 'Únicamente funciones de almacenista'),
(3, 'Coordinador administrativo', 'Supervisa las operaciones de los almacenes pero no puede realizar capturas'),
(4, 'Control de almacenes', 'Supervisa y aprueba o cancela las operaciones de los almacenes pero no puede realizar capturas ni subir documentos'),
(5, 'Supervisor', 'Supervisa y aprueba la información de entradas y salidas para marcarlos como verificados'),
(6, 'Enlace', 'Permisos de Coordinador administrativo sin poder subir pagos'),
(7, 'AFEVEM', 'Permisos de supervisor sin poder marcar entradas y salidas como verificados');

/* Permisos */
/* Crear usuarios */
/* Registrar entradas */
/* Hacer inventario */

CREATE Table almacenes(
    id_almacen int NOT NULL PRIMARY KEY COMMENT 'ID del almacen',
    almacen VARCHAR(100) NOT NULL COMMENT 'Nombre del almacen'
) COMMENT 'Tabla para almacenar los almacenes de la institución';

INSERT INTO almacenes(id_almacen, almacen) VALUES
(0, 'Todos los almacenes'),
(1, 'Almacen Morelia'),
(2, 'Almacen Uruapan'),
(3, 'Almacen Zitacuaro'),
(4, 'Almacen Aquila'),
(5, 'Almacen La Piedad'),
(6, 'Almacen Apatzingan'),
(7, 'Almacen Zamora'),
(8, 'Almacen Lázaro Cárdenas'),
(9, 'Almacen Huetamo');

CREATE TABLE usuarios(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'id del usuario',
    nombres varchar(100) NOT NULL COMMENT 'Nombre del usuario',
    apellido_paterno VARCHAR(100) NOT NULL COMMENT 'Apellido paterno del usuario',
    apellido_materno VARCHAR(100) NOT NULL COMMENT 'Apellido materno del usuario',
    usuario VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nombre de usuario',
    contrasena VARCHAR(100) NOT NULL COMMENT 'Contrasena del usuario',
    id_rol int NOT NULL DEFAULT 2 COMMENT 'Rol del usuario',
    id_almacen INT NOT NULL DEFAULT 0 COMMENT 'Almacen al que pertenece usuario',
    fecha_registro TIMESTAMP not NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del usuario',
    estado ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO' COMMENT 'Estado del usuario',
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen)
) COMMENT 'Tabla para almacenar los usuarios de la institución';

INSERT INTO usuarios (id, nombres, apellido_paterno, apellido_materno, usuario, contrasena, id_rol, id_almacen, fecha_registro, estado)
VALUES (1, 'Adrian', 'Torrez', 'Beltran', 'adrianto', '$2y$10$cmQf9PYBsJd7PVehIfrvM.U/tEp4jU0rLYQ/DwlbwWtrWSxMZE6LK', 4,
        0, '2024-03-22 22:58:42', 'ACTIVO'),
       (2, 'AdminDIF', 'Desarrollo Integral', 'de la Familia', 'AdminDIF',
        '$2y$10$KE5sVMZ1DmFhPqRqbPu2qO012YD191ydp4XALHFI2Ls0B8IVu/7o.', 1, 1, '2024-04-15 21:03:08', 'ACTIVO'),
       (3, 'Carlos Alberto', 'Vazquez', 'Duran', 'albertov',
        '$2y$10$h3yPWDZDm6r45GQ2Ix/tWeNbPNk4IKt4rmQR4Gyitr4DpbtTktgty', 2, 1, '2024-04-21 19:38:07', 'ACTIVO'),
       (4, 'Santiago', 'Zaragoza', 'Espinosa', 'santiagoz',
        '$2y$10$0a8RSsr11qPt2s8oqYMcdOUxmttTFzYR9K435r46LtyYQoGXRiz4.', 2, 2, '2024-04-21 19:45:09', 'ACTIVO'),
       (5, 'Soledad', 'Miranda', 'Cuevas', 'soledadm', '$2y$10$CICHkZDwWu8oYW4lxrhXduzK2.pSWDTbC3mAyAPAhqafhystBZ0cu',
        2, 2, '2024-04-21 19:46:24', 'ACTIVO'),
       (6, 'Maria Teresa', 'Palomino', 'Cardenaz', 'mariap',
        '$2y$10$sT7P/8SrCc0NGQFz3kPp2O/ekL60dma17NJUfjTJzRg22C1u7DfrO', 2, 3, '2024-04-21 19:50:47', 'ACTIVO'),
       (8, 'Hector', 'Estrada', 'Soto', 'hectore', '$2y$10$bMz9bQSOX4XCa.NvpMvcZ.W7AH8Z3nOu9ECVuFPLTVp08UzQvW19S', 2, 5,
        '2024-04-21 19:56:35', 'ACTIVO'),
       (9, 'Manuel', 'Ayala', 'Chavez', 'manuelch', '$2y$10$9QqUa3AR2MuW4swXYknv9uwPAVnH07f88v6kJ/j40upJJtwraInO.', 2,
        6, '2024-04-21 19:59:16', 'ACTIVO'),
       (10, 'Renaul', 'Palacios', 'Luna', 'renaulp', '$2y$10$c6W4Zd.m4NgBnH8F.7Ta/ecIwcpRUQ6vzV3ynSIsbLANRckLhjYES', 2,
        6, '2024-04-21 19:59:58', 'ACTIVO'),
       (11, 'Miguel Angel', 'Montejano', 'Menchaca', 'miguelm',
        '$2y$10$RIGInfbF/zB8UZGSqYGIoOh3WWi4wbKYlSzisBNeyfB346mT8GVe2', 2, 7, '2024-04-21 20:01:30', 'ACTIVO'),
       (12, 'Maria Dolores', 'Olguin', 'Trujillo', 'mariao',
        '$2y$10$DdxsyZbX4.AeBfYk3WHkd.j78APRjOMEGGTVXEdRvWrpUmMh4m6OO', 2, 7, '2024-04-21 20:02:13', 'INACTIVO'),
       (13, 'Irery Concepcion', 'Luna', 'Parra', 'ireryl',
        '$2y$10$31HYO.S3OdcBL3oF0fxHHOKPZRm2OI.vCtaNL8zLXCO0vPJdanUXG', 2, 9, '2024-04-21 20:11:41', 'INACTIVO'),
       (14, 'Jose Manuel', 'Ibarra', 'Lopez', 'manueli', '$2y$10$1HYqGYuj9y/1jYG8rZotUex9LX2VDwoTJDR6BIUWTr9HLtbU9F5h6',
        2, 9, '2024-04-21 20:12:43', 'INACTIVO'),
       (16, 'Eduardo', 'Hernández', 'Murillo', 'eduardoh',
        '$2y$10$5X/EwD3w1wAQbJYFxpkHNOo9iDlGl96iTH5Qwxe4oeR/XL5jGgMha', 2, 8, '2024-04-22 17:48:46', 'ACTIVO'),
       (17, 'Ma Luisa', 'Vargas', 'Sanchez', 'luisav', '$2y$10$lLAMz3SSYnMLuF.ACVYH0OEjdsXuEzMfQICiTu2WPF.LOZQBUw8cK',
        2, 4, '2024-04-22 18:16:26', 'ACTIVO'),
       (18, 'FCO', 'FCO', 'FCO', 'FCO', '$2y$10$qh61bntl29p9vTv3h9.AFeQpUwPOh8ifpA1iB9oIhLde7NsZloIim', 1, 0,
        '2024-04-23 00:54:14', 'ACTIVO'),
       (19, 'Claudia', 'Orozco', 'Ceja', 'claudiac', '$2y$10$8EOYEZ6jMvtIdtQNrTnBYuZOtrsPqdxnkMziTEurP9IQcPVPuNqjS', 2,
        5, '2024-04-24 18:39:13', 'INACTIVO'),
       (20, 'jefa', 'jefa', '', '', '$2y$10$uSDEj6vgzpD.9kBs6tTKW.Fc4P08Fjv.mVHiTU1TIYl4/IfBK18ca', 3, 7,
        '2024-05-08 00:05:11', 'ACTIVO'),
       (22, 'supervisor', 'supervisor', 'supervisor', 'supervisor',
        '$2y$10$e/3BRi1x/IyCS2HADMlXkOS2A47e2ZFB81I.7OhiknijNyjZ9s8qG', 5, 0, '2024-05-29 14:40:01', 'ACTIVO'),
       (23, 'JANEIRA', 'AGUILAR', 'MUÑOZ', 'janeiram', '$2y$10$US2CJlFHUJ9lwc/8Qzzf2ukkplsfEOAGe0r7wFJgy1gcO3Y4QFzkm',
        4, 0, '2024-05-29 18:05:59', 'ACTIVO'),
       (24, 'ARTURO GIOVANNI', 'VILLICAÑA', 'CUELLAR', 'arturov',
        '$2y$10$spCMqY8b6C/p4FLFGZ4W2OWpqZOysVunxGSskingf0VMtYBlswQxa', 5, 0, '2024-05-29 18:09:01', 'ACTIVO'),
       (25, 'Marisol', 'Gaona', 'Hernandez', 'marisolg', '$2y$10$aUqep/UtsBC76URKOpj/4OS/d/QaAd5eG78zC8vgnO97w2gC4PwTG',
        3, 1, '2024-06-18 16:04:19', 'ACTIVO'),
       (26, 'Rene', 'Vallejo', 'Dominguez', 'rene', '$2y$10$3lCdgjauF2jsdczcdVzVhOm3bm33MD5fx6pJPJ/CFwvemmhKE6gQi', 3,
        1, '2024-06-18 16:06:30', 'INACTIVO'),
       (27, 'Gabriela', 'Cruz', 'Gonzalez', 'gabrielac', '$2y$10$0v8hYq5BIvzr4qkCVYqMzOnmIReeKK8AlK/6/wM4oY5pXUUo0B9xa',
        3, 3, '2024-06-18 16:07:38', 'ACTIVO'),
       (30, 'Irery Concepcion', 'Luna', 'Parra', 'concepcionl',
        '$2y$10$vPa7mvAbgmSRvNs.TVAH0eGa4YdY5yVmasowYOhMJtMGO82ZXhAhi', 3, 9, '2024-06-18 16:09:05', 'INACTIVO'),
       (32, 'Claudia Angelica', 'Chavez', 'Magallan', 'angelicac',
        '$2y$10$xWmhkpAgDMqBabNLtyF1eOwoNjTAn/tny/.wnoRItwQF0OA1VMN8C', 3, 7, '2024-06-18 16:10:54', 'ACTIVO'),
       (34, 'Soledad', 'Miranda', 'Cuevas', 'soledadc', '$2y$10$ozL8LeUdqZIez2bSPwk/BuSsu2ybRyafkiEKZLY37JKXo8LqPFww.',
        3, 2, '2024-06-18 16:12:31', 'ACTIVO'),
       (35, 'Andres', 'Miranda', 'Cuevas', 'andresm', '$2y$10$vFSckcjGdTKUbDBxLJ6Loe3MzFHxg0lF4clJhhe.Dc4a8we8lppjW', 3,
        6, '2024-06-18 16:13:13', 'ACTIVO'),
       (37, 'Hector', 'Estrada', 'Soto', 'hectors', '$2y$10$t/KQawneDu7oAuUvaGGLnOr4.RHIHGzSEeHwBde6/LbeXuv0R8x/6', 3,
        5, '2024-06-18 16:14:04', 'ACTIVO'),
       (38, 'Maria Luisa', 'Vargas', 'Sanchez', 'luisas',
        '$2y$10$lLAMz3SSYnMLuF.ACVYH0OEjdsXuEzMfQICiTu2WPF.LOZQBUw8cK', 3, 4, '2024-06-18 16:15:05', 'ACTIVO'),
       (39, 'Rosa', 'Gonzalez', 'Tadeo', 'rosag', '$2y$10$8GL1XBvDrhapV6KdKsBnAedxcxP9TYV/UzQ9jPYtYDxs603UtIoau', 3, 8,
        '2024-06-18 16:15:41', 'ACTIVO'),
       (40, 'Jose Alberto', 'Culebro', 'Hernandez', 'albertoc',
        '$2y$10$Yazg0ZGUid9Kl8v5uuz1CucH9zG9VZg66acax.rBpkItb/Xa1yqTm', 7, 0, '2024-06-25 17:58:24', 'ACTIVO'),
       (41, 'Ma. de los Angeles', 'Garcia', 'Martinez', 'mariag',
        '$2y$10$nOP.Im.Fop82bwrzZtOYN.1wBp9sE91y7dCno8gJH27JswSK/7gkG', 5, 0, '2024-06-25 19:22:33', 'ACTIVO'),
       (42, 'Grecia', 'Muñoz', 'Sanchez', 'grecia.huetamo',
        '$2y$10$YHw2o9YpA8mwVcjVUJQdBuC5y0AczbytYL7TGXHCOmjaF/QU0rzlu', 2, 9, '2024-09-11 22:51:44', 'ACTIVO'),
       (43, 'SOFIA', 'BAUTISTA', 'AGUIÑIGA', 'SOFIA', '$2y$10$D2xWo4FxTy7Ew9/ULnpqV.LCqi4q6CjABqgXQsHmaAEC8GtYTW/yy', 4,
        0, '2025-02-20 20:27:17', 'ACTIVO'),
       (44, 'JOSE  SILVESTRE', 'ZENDEJAS', 'RAMOS', 'SILVESTREZ',
        '$2y$10$kMu6HIEYvZJWTFgDsc2qLeY5Y7UweWUYmn1rsvuSv7yeXBIocMGDi', 2, 5, '2025-03-20 17:46:18', 'ACTIVO'),
       (45, 'SARAHI ', 'ESQUIVEL', 'DOMINGUEZ', 'sarahie',
        '$2y$10$6XZ2EOmj3JXxkParyUUtWeB75/Ao.g14o2oPiawV206zss9U8OjUu', 7, 0, '2025-03-25 20:04:16', 'ACTIVO');

CREATE Table proveedores(
    id_proveedor int NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del proveedor',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del proveedor',
    nombre_legal VARCHAR(255) NOT NULL COMMENT 'Nombre completo legal del proveedor',
    rfc VARCHAR(13) COMMENT 'RFC del proveedor',
    direccion VARCHAR(255) COMMENT 'Direccion del proveedor',
    telefono VARCHAR(20) COMMENT 'Telefono del proveedor'
) COMMENT 'Tabla para almacenar los proveedores con los que colabora la institución';

INSERT into proveedores(id_proveedor, nombre, nombre_legal, rfc, direccion, telefono) 
VALUES (1, 'Empacadora La Merced', 'Empacadora La Merced, S.A. De C.V.', 'EME710129QAA', 'Chope Albarrán No. MZ2, Col. Esfuerzo Nacional C.P. 55320, Ecatepec de Morelos, Estado de México', '55 5788 2088'),
         (2, 'Abastos Y Distribuciones Institucionales', 'Abastos Y Distribuciones Institucionales, S.A. De C.V.', 'ADI991022KX2', 'Calle 4 Manzana 4 Lote 6, Ejido del Moral C.P. 09040, Alcaldía Iztapalapa, Ciudad de México', ''),
         (3, 'JDG Comercializadores Y Servicios Michoacanos', 'JDG Comercializadores Y Servicios Michoacanos, S.A. De C.V.', 'JCS100629QF9', 'Homero 14 Int. 3 Loc. 3, Residencial Lancaster C.P. 58255, Morelia, Michoacán de Ocampo', '443 333 2274');

CREATE Table entradas(
    id_entrada int NOT NULL PRIMARY KEY COMMENT 'ID de la entrada',
    tipo VARCHAR(20) NOT NULL COMMENT 'Tipo de entrada'
) COMMENT 'Tabla para almacenar los tipos de entradas de productos existentes en la institución';
INSERT INTO entradas(id_entrada, tipo) VALUES(1, 'COMPRA'), (2, 'TRASPASO'), (3, 'DONACION'), (4, 'DEVOLUCION'), (5, 'REPOSICION'), (6, 'REMANENTE'), (7, 'MERMA');

CREATE Table salidas(
    id_salida int NOT NULL PRIMARY KEY COMMENT 'ID de la salida',
    tipo VARCHAR(20) NOT NULL COMMENT 'Tipo de salida'
) COMMENT 'Tabla para almacenar las salidas de productos existentes en la institución';

INSERT INTO salidas(id_salida, tipo) VALUES(1, 'CUOTA'), (2, 'TRASPASO'), (3, 'DONACION'), (4, 'DEVOLUCION'), (5, 'REPOSICION'), (6, 'MERMA'), (7, 'REMANENTE');

CREATE Table registro_entradas(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del registro de dotacion',
    folio int UNIQUE COMMENT 'ID del registro con formato id_almacen + 0n + id_registro',
    id_usuario INT NOT NULL COMMENT 'Usuario que realiza el registro',
    id_almacen INT NOT NULL COMMENT 'ID del almacen donde se realiza el registro',
    id_proveedor INT NOT NULL COMMENT 'ID del proveedor que realiza la dotacion',
    id_entrada INT NOT NULL COMMENT 'Tipo de entrada',
    entrega VARCHAR(255) NOT NULL COMMENT 'Quien entrega',
    dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación') NOT NULL COMMENT 'Numero de dotacion al año al que pertenece',
    nota VARCHAR(255) COMMENT 'Nota opcional de la dotacion',
    pdf_docs VARCHAR(50) COMMENT 'PDF con los documentos que se generan al llegar la dotacion',
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del registro',
    nota_cancelacion VARCHAR(500) COMMENT 'Nota de cancelacion de la dotacion',
    nota_modificacion VARCHAR(500) COMMENT 'Nota de modificación de la dotación',
    cancelado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotacion ha sido cancelada',
    verificado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotacion ha sido verificada',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_entrada) REFERENCES entradas(id_entrada)
) COMMENT 'Tabla para almacenar los registros de dotaciones';

DELIMITER //
CREATE PROCEDURE insertar_registro_entradas(
    IN p_id_usuario INT,
    IN p_id_almacen INT,
    IN p_id_proveedor INT,
    IN p_id_entrada INT,
    IN p_entrega VARCHAR(255),
    IN p_dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación'),
    IN p_nota VARCHAR(255)
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(folio) INTO v_ultimo_folio FROM registro_entradas WHERE id_almacen = p_id_almacen;

    -- Incrementar el último folio o iniciar desde 1 si es el primer registro para el almacén
    SET v_ultimo_folio = COALESCE(v_ultimo_folio % 10000, 0) + 1;

    -- Insertar el nuevo registro
    INSERT INTO registro_entradas(
        id_usuario,
        id_almacen,
        id_proveedor,
        id_entrada,
        entrega,
        dotacion,
        nota,
        folio
    )
    VALUES(
              p_id_usuario,
              p_id_almacen,
              p_id_proveedor,
              p_id_entrada,
              p_entrega,
              p_dotacion,
              p_nota,
              CONCAT(p_id_almacen, LPAD(v_ultimo_folio, 4, '0'))
          );

    -- Obtener el ID del último registro insertado
    SET v_ultimo_id = LAST_INSERT_ID();

    -- Devolver el folio del último registro insertado
    SELECT folio FROM registro_entradas WHERE id = v_ultimo_id;
END //
DELIMITER ;

CREATE Table registro_salidas(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del registro de dotacion',
    folio int UNIQUE COMMENT 'ID del registro con formato id_almacen + 0n + id_registro',
    id_usuario INT NOT NULL COMMENT 'Usuario que realiza el registro',
    id_almacen INT NOT NULL COMMENT 'ID del almacen donde se realiza el registro',
    afavor VARCHAR(255) NOT NULL COMMENT 'Organizacion, institucion o persona a favor de quien se realiza la salida',
    municipio VARCHAR(255) NOT NULL COMMENT 'Municipio al que se realiza la salida',
    id_salida INT NOT NULL COMMENT 'Tipo de salida',
    recibe VARCHAR(255) NOT NULL COMMENT 'Quien recibe',
    referencia VARCHAR(100) NOT NULL COMMENT 'Referencia bancaria de la salida',
    monto DECIMAL(10, 2) NOT NULL COMMENT 'Monto de la salida',
    dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación') NOT NULL COMMENT 'Numero de dotacion al año al que pertenece',
    nota VARCHAR(255) COMMENT 'Nota opcional de la dotacion',
    pdf_docs VARCHAR(70) DEFAULT NULL COMMENT 'PDF con los documentos que se suben',
    pdf_docs_coord VARCHAR(70) DEFAULT NULL COMMENT 'PDF con los documentos que genera el coordinador',
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del registro',
    nota_cancelacion VARCHAR(500) COMMENT 'Nota de cancelacion de la dotacion',
    nota_modificacion VARCHAR(500) COMMENT 'Nota de modificación de la dotación',
    cancelado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotacion ha sido cancelada',
    verificado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotacion ha sido verificada',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen),
    FOREIGN KEY (id_salida) REFERENCES salidas(id_salida)
) COMMENT 'Tabla para almacenar las salidas de dotaciones';

DELIMITER //
CREATE PROCEDURE insertar_registro_salidas(
    IN p_id_usuario INT,
    IN p_id_almacen INT,
    IN p_afavor VARCHAR(255),
    IN p_municipio VARCHAR(255),
    IN p_id_salida INT,
    IN p_recibe VARCHAR(255),
    IN p_referencia VARCHAR(100),
    IN p_monto DECIMAL(10, 2),
    IN p_dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación'),
    IN p_nota VARCHAR(255)
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(folio) INTO v_ultimo_folio FROM registro_salidas WHERE id_almacen = p_id_almacen;

    -- Incrementar el último folio o iniciar desde 1 si es el primer registro para el almacén
    SET v_ultimo_folio = COALESCE(v_ultimo_folio % 10000, 0) + 1;

    -- Insertar el nuevo registro
    INSERT INTO registro_salidas(
        id_usuario,
        id_almacen,
        afavor,
        municipio,
        id_salida,
        recibe,
        referencia,
        monto,
        dotacion,
        folio,
        nota
    )
    VALUES(
              p_id_usuario,
              p_id_almacen,
              p_afavor,
              p_municipio,
              p_id_salida,
              p_recibe,
              p_referencia,
              p_monto,
              p_dotacion,
              CONCAT(p_id_almacen, LPAD(v_ultimo_folio, 4, '0')),
              p_nota
          );

    -- Obtener el ID del último registro insertado
    SET v_ultimo_id = LAST_INSERT_ID();

    -- Devolver el folio del último registro insertado
    SELECT folio FROM registro_salidas WHERE id = v_ultimo_id;
END //
DELIMITER ;


CREATE Table dotaciones(
    clave INT NOT NULL COMMENT 'ID de la dotacion',
    programa VARCHAR(255) NOT NULL COMMENT 'Programa al que pertenece la dotacion',
    producto VARCHAR(255) NOT NULL COMMENT 'Descripcion o nombre del producto',
    medida VARCHAR(100) NOT NULL COMMENT 'Cantidad de articulos',
    cuota DECIMAL(10, 2) NOT NULL COMMENT 'Cuota de dotacion',
    PRIMARY KEY (clave)
) COMMENT 'Tabla para almacenar las dotaciones de productos existentes en la institución';

INSERT INTO dotaciones(clave, programa, producto, medida,cuota) VALUES
(2025001, 'Personas Adultas Mayores', 'Personas Adultas Mayores', 'Caja', '329.89'),
(2025002, 'Personas con Discapacidad', 'Personas con Discapacidad', 'Caja', '405.18'),
(2025003, 'Personas en Situación de Emergencias o Desastres', 'Personas en Situación de Emergencias o Desastres', 'Caja', '949.57'),
(2025004, 'Infantes de 2 a 5 años 11 meses', 'Infantes de 2 a 5 años 11 meses', 'Caja', '257.11'),
(2025005, 'Lactantes de 6 a 24 meses', 'Lactantes de 6 a 24 meses', 'Caja', '201.36'),
(2025006, 'Mujeres Embarazadas o en Periodo de Lactancia', 'Mujeres Embarazadas o en Periodo de Lactancia', 'Caja', '359.40'),
(2025007, 'Desayunos Escolares Calientes', 'Aceite vegetal comestible puro de canola', 'Botella de 1L', '52.30'),
(2025008, 'Desayunos Escolares Calientes', 'Arroz pulido calidad extra, ultima cosecha', 'Bolsa de 900g', '28.50'),
(2025009, 'Desayunos Escolares Calientes', 'Atun aleta amarilla en agua', 'Pouch de 1.02kg M.D. 1kg', '225.27'),
(2025010, 'Desayunos Escolares Calientes', 'Avena en hojuelas', 'Bolsa de 1kg', '31.40'),
(2025011, 'Desayunos Escolares Calientes', 'Carne de res deshebrada al alto vacio', 'Pouch 1kg', '294.30'),
(2025012, 'Desayunos Escolares Calientes', 'Chícharo con zanahoria', 'Lata de 430g M.D. 252g', '18.80'),
(2025013, 'Desayunos Escolares Calientes', 'Espagueti integral', 'Bolsa de 200g', '8.30'),
(2025014, 'Desayunos Escolares Calientes', 'Frijol pinto nacional, ultima cosecha', 'Bolsa de 1kg', '44.90'),
(2025015, 'Desayunos Escolares Calientes', 'Harina de maiz nixtamalizado', 'Bolsa de 1kg', '21.00'),
(2025016, 'Desayunos Escolares Calientes', 'Leche descremada en polvo', 'Bolsa de 1kg', '114.70'),
(2025017, 'Desayunos Escolares Calientes', 'Lenteja última cosecha', 'Bolsa de 1kg', '52.30'),
(2025018, 'Desayunos Escolares Calientes', 'Manzana amarilla fresca', 'Pieza', '10.90'),
(2025019, 'Desayunos Escolares Calientes', 'Mix de fruta deshidratada y oleaginosas', 'Bolsa de 30g', '5.90'),
(2025020, 'Desayunos Escolares Calientes', 'Pasta para sopa integral (Codito #2)', 'Bolsa de 200g', '8.31'),
(2025021, 'Desayunos Escolares Calientes', 'Pechuga de pollo deshidratada al alto vacio', 'Pouch de 1kg', '240.00'),
(2025022, 'Desayunos Escolares Calientes', 'Soya texturizada', 'Bolsa de 330g', '22.30'),
(2025023, 'Desayunos Escolares Calientes', 'Zanahoria fresca', 'Pieza', '5.29'),
(2025024, 'Espacios de Alimentación', 'Aceite vegetal comestible puro de canola', 'Botella de 1L', '57.30'),
(2025025, 'Espacios de Alimentación', 'Arroz pulido calidad extra ultima cosecha', 'Botella de 900g', '31.14'),
(2025026, 'Espacios de Alimentación', 'Atun aleta amarilla en agua', 'Pouch de 1.02kg M.D. 1kg', '204.08'),
(2025027, 'Espacios de Alimentación', 'Avena en hojuelas', 'Bolsa de 1kg', '34.39'),
(2025028, 'Espacios de Alimentación', 'Chícharo con zanahoria', 'Lata de 430g M.D. 252g', '20.50'),
(2025029, 'Espacios de Alimentación', 'Frijol pinto nacional ultima cosecha', 'Bolsa de 1kg', '49.09'),
(2025030, 'Espacios de Alimentación', 'Harina de maiz nixtamalizado', 'Bolsa de 1kg', '22.30'),
(2025031, 'Espacios de Alimentación', 'Leche descremada en polvo', 'Bolsa de 1kg', '125.00'),
(2025032, 'Espacios de Alimentación', 'Lenteja ultima cosecha', 'Bolsa de 1kg', '57.27'),
(2025033, 'Espacios de Alimentación', 'Mix de fruta deshidratada y oleaginosas', 'Bolsa de 30g', '6.15'),
(2025034, 'Espacios de Alimentación', 'Pasta para sopa integral (Codito #2)', 'Bolsa de 200g', '9.17'),
(2025035, 'Espacios de Alimentación', 'Soya texturizada', 'Bolsa de 330g', '24.05');


CREATE TABLE proveedores_autorizados(
    id_proveedor INT NOT NULL COMMENT 'ID del proveedor',
    programa VARCHAR(255) NOT NULL COMMENT 'Programa al que pertenece la dotacion',
    disponibilidad ENUM('SI', 'NO') NOT NULL COMMENT 'Disponibilidad del proveedor',
    PRIMARY KEY (id_proveedor, programa),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
) COMMENT 'Tabla para almacenar los proveedores autorizados por dotacion';

INSERT INTO proveedores_autorizados(id_proveedor, programa, disponibilidad) VALUES 
(1, 'Personas Adultas Mayores', 'SI'),
(3, 'Personas con Discapacidad', 'SI'),
(3, 'Personas en Situación de Emergencias y Desastres', 'SI'),
(2, 'Infantes de 2 a 5 años 11 meses', 'SI'),
(2, 'Lactantes de 6 a 24 meses', 'SI'),
(1, 'Mujeres Embarazadas o en Periodo de Lactancia', 'SI'),
(3, 'Desayunos Escolares Calientes', 'SI'),
(3, 'Espacios de Alimentación', 'SI');

CREATE TABLE registro_entradas_registradas(
    clave INT NOT NULL COMMENT 'Clave de la dotacion',
    folio INT NOT NULL COMMENT 'Folio del registro',
    lote VARCHAR(100) NOT NULL COMMENT 'Lote del articulo',
    caducidad DATE NOT NULL COMMENT 'Fecha de caducidad del articulo',
    cantidad INT NOT NULL COMMENT 'Cantidad de articulos',
    PRIMARY KEY (clave, folio),
    FOREIGN KEY (clave) REFERENCES dotaciones(clave),
    FOREIGN KEY (folio) REFERENCES registro_entradas(folio)
) COMMENT 'Tabla para indicar las dotaciones registradas en el sistema';

CREATE Table registro_salidas_registradas(
    clave INT NOT NULL COMMENT 'Clave de la dotacion',
    folio INT NOT NULL COMMENT 'Folio del registro',
    lote VARCHAR(100) NOT NULL COMMENT 'Lote del articulo',
    caducidad DATE NOT NULL COMMENT 'Fecha de caducidad del articulo',
    cantidad INT NOT NULL COMMENT 'Cantidad de articulos',
    PRIMARY KEY (clave, folio),
    FOREIGN KEY (clave) REFERENCES dotaciones(clave),
    FOREIGN KEY (folio) REFERENCES registro_salidas(folio)
) COMMENT 'Tabla para almacenar las salidas de dotaciones registradas en el sistema';


/* ############################ SENTENCIAS PARA LA SIGUENTE ACTUALIZACION ############################ */

ALTER TABLE registro_entradas MODIFY dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación');
ALTER TABLE registro_salidas MODIFY dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación');

DROP PROCEDURE insertar_registro_salidas;
CREATE PROCEDURE insertar_registro_salidas(
    IN p_id_usuario INT,
    IN p_id_almacen INT,
    IN p_afavor VARCHAR(255),
    IN p_municipio VARCHAR(255),
    IN p_id_salida INT,
    IN p_recibe VARCHAR(255),
    IN p_referencia VARCHAR(100),
    IN p_monto DECIMAL(10, 2),
    IN p_dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación'),
    IN p_nota VARCHAR(255)
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(folio) INTO v_ultimo_folio FROM registro_salidas WHERE id_almacen = p_id_almacen;

    -- Incrementar el último folio o iniciar desde 1 si es el primer registro para el almacén
    SET v_ultimo_folio = COALESCE(v_ultimo_folio % 10000, 0) + 1;

    -- Insertar el nuevo registro
    INSERT INTO registro_salidas(
        id_usuario,
        id_almacen,
        afavor,
        municipio,
        id_salida,
        recibe,
        referencia,
        monto,
        dotacion,
        folio,
        nota
    )
    VALUES(
        p_id_usuario,
        p_id_almacen,
        p_afavor,
        p_municipio,
        p_id_salida,
        p_recibe,
        p_referencia,
        p_monto,
        p_dotacion,
        CONCAT(p_id_almacen, LPAD(v_ultimo_folio, 4, '0')),
        p_nota
    );

    -- Obtener el ID del último registro insertado
    SET v_ultimo_id = LAST_INSERT_ID();

    -- Devolver el folio del último registro insertado
    SELECT folio FROM registro_salidas WHERE id = v_ultimo_id;
END //
DELIMITER;

DROP PROCEDURE insertar_registro_entradas;
CREATE PROCEDURE insertar_registro_entradas(
    IN p_id_usuario INT, 
    IN p_id_almacen INT, 
    IN p_id_proveedor INT, 
    IN p_id_entrada INT, 
    IN p_entrega VARCHAR(255), 
    IN p_dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación'), 
    IN p_nota VARCHAR(255)
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(folio) INTO v_ultimo_folio FROM registro_entradas WHERE id_almacen = p_id_almacen;

    -- Incrementar el último folio o iniciar desde 1 si es el primer registro para el almacén
    SET v_ultimo_folio = COALESCE(v_ultimo_folio % 10000, 0) + 1;

    -- Insertar el nuevo registro
    INSERT INTO registro_entradas(
        id_usuario, 
        id_almacen, 
        id_proveedor, 
        id_entrada, 
        entrega, 
        dotacion, 
        nota, 
        folio
    ) 
    VALUES(
        p_id_usuario, 
        p_id_almacen, 
        p_id_proveedor, 
        p_id_entrada, 
        p_entrega, 
        p_dotacion, 
        p_nota, 
        CONCAT(p_id_almacen, LPAD(v_ultimo_folio, 4, '0'))
    );

    -- Obtener el ID del último registro insertado
    SET v_ultimo_id = LAST_INSERT_ID();

    -- Devolver el folio del último registro insertado
    SELECT folio FROM registro_entradas WHERE id = v_ultimo_id;
END 