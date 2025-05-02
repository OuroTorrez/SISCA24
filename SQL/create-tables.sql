-- Active: 1710947015672@@127.0.0.1@3306@dif michoacan informatica

CREATE Table roles(
    id_rol int NOT NULL PRIMARY KEY COMMENT 'ID del rol',
    rol VARCHAR(100) NOT NULL COMMENT 'Nombre del rol',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripcion del rol'
) COMMENT 'Tabla para almacenar los roles de los usuarios';
INSERT INTO roles(id_rol, rol, descripcion) VALUES(1, 'ADMINISTRADOR', 'Todos los permisos de administrador'), (2, 'ALMACENISTA', 'Únicamente funciones de almacenista');
INSERT INTO roles(id_rol, rol, descripcion) VALUES(3, 'Coordinador administrativo', 'Supervisa las operaciones de los almacenes pero no puede realizar capturas');
INSERT INTO roles(id_rol, rol, descripcion) VALUES(4, 'Control de almacenes', 'Supervisa y aprueba o cancela las operaciones de los almacenes pero no puede realizar capturas ni subir documentos');
INSERT INTO roles(id_rol, rol, descripcion) VALUES(5, 'Supervisor', 'Supervisa y aprueba la información de entradas y salidas para marcarlos como verificados');
INSERT INTO roles(id_rol, rol, descripcion) VALUES(6, 'Enlace', 'Permisos de Coordinador administrativo sin poder subir pagos');
INSERT INTO roles(id_rol, rol, descripcion) VALUES (7, 'AFEVEM', 'Permisos de supervisor sin poder marcar entradas y salidas como verificados');

/* Permisos */
/* Crear usuarios */
/* Registrar entradas */
/* Hacer inventario */

CREATE Table almacenes(
    id_almacen int NOT NULL PRIMARY KEY COMMENT 'ID del almacen',
    almacen VARCHAR(100) NOT NULL COMMENT 'Nombre del almacen'
) COMMENT 'Tabla para almacenar los almacenes de la institución';

INSERT INTO almacenes(id_almacen, almacen) VALUES(0, 'INDEFINIDO'),
                                                (1, 'Almacen Morelia'),
                                                (2, 'Almacen Pátzcuaro');

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

INSERT INTO `usuarios` (`id`, `nombres`, `apellido_paterno`, `apellido_materno`, `usuario`, `contrasena`, `id_rol`, `id_almacen`, `fecha_registro`, `estado`) VALUES
(1, 'Adrian', 'Torrez', 'Beltran', 'adrianto', '$2y$10$bzlu8CvGLGF3.oBsiXqLXuZ0aDYxr.iP0yCsvtup0yZIofacs/aCe', 1, 1, '2024-03-22 16:58:42', 'ACTIVO'),
(2, 'AdminDIF', 'Desarrollo Integral', 'de la Familia', 'AdminDIF', '$2y$10$KE5sVMZ1DmFhPqRqbPu2qO012YD191ydp4XALHFI2Ls0B8IVu/7o.', 1, 2, '2024-04-15 15:03:08', 'ACTIVO');

CREATE Table proveedores(
    id_proveedor int NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del proveedor',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del proveedor',
    nombre_legal VARCHAR(255) NOT NULL COMMENT 'Nombre completo legal del proveedor',
    rfc VARCHAR(13) COMMENT 'RFC del proveedor',
    direccion VARCHAR(255) COMMENT 'Direccion del proveedor',
    telefono VARCHAR(20) COMMENT 'Telefono del proveedor'
) COMMENT 'Tabla para almacenar los proveedores con los que colabora la institución';

INSERT into proveedores(id_proveedor, nombre, nombre_legal, rfc, direccion, telefono) 
VALUES (1, 'Empacadora La Merced', 'Empacadora La Merced, S.A. De C.V.', 'EME710129QAA', 'Chope Albarrán No. MZ2, Col. Esfuerzo Nac C.P. 55320', '55 5788 2088'),
         (2, 'Grupo Industrial Vida', 'Grupo Industrial Vida, S.A. De C.V.', 'GIV970203LS1', 'Ejido No. 300, Col. La Venta Del Astillero C.P. 45221, Zapopan Jalisco', '33 1864 4981'),
         (3, 'Technofoods', 'Technofoods, S.A. De C.V.', '', '', ''),
         (4, 'JDG Comercializadores Y Servicios Michoacanos', 'JDG Comercializadores Y Servicios Michoacanos, S.A. De C.V.', 'JCS100629QF9', 'Homero 14 Interior 3 Loc. 3, Residencial Lancaster C.P. 58255, Morelia Michoacán', '443 333 2274');

CREATE Table entradas(
    id_entrada int NOT NULL PRIMARY KEY COMMENT 'ID de la entrada',
    tipo VARCHAR(20) NOT NULL COMMENT 'Tipo de entrada'
) COMMENT 'Tabla para almacenar los tipos de entradas de productos existentes en la institución';
INSERT INTO entradas(id_entrada, tipo) VALUES(1, 'COMPRA'), (2, 'TRASPASO'), (3, 'DONACION'), (4, 'DEVOLUCION'), (5, 'REPOSICION'), (6, 'REMANENTE'), (7, 'MERMA');

CREATE Table salidas(
    id_salida int NOT NULL PRIMARY KEY COMMENT 'ID de la salida',
    tipo VARCHAR(20) NOT NULL COMMENT 'Tipo de salida'
) COMMENT 'Tabla para almacenar las salidas de productos existentes en la institución';

INSERT INTO salidas(id_salida, tipo) VALUES(1, 'CONSUMO'), (2, 'DONACION'), (3, 'DEVOLUCION'), (4, 'REPOSICION'), (5, 'REMANENTE'), (6, 'MERMA');

CREATE Table registro_entradas(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del registro de dotacion',
    folio INT NOT NULL COMMENT 'ID del registro con formato id_almacen + 0n + id_registro',
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
    verificado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotación ha sido verificada',
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
    IN p_nota VARCHAR(255),
    IN p_ejercicio INT
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(folio) INTO v_ultimo_folio FROM registro_entradas WHERE id_almacen = p_id_almacen;

    SELECT MAX(re.folio) INTO v_ultimo_folio FROM registro_entradas re JOIN registro_entradas_registradas rer ON re.folio = rer.folio
    WHERE re.id_almacen = p_id_almacen AND LEFT(rer.clave, 4) = p_ejercicio;

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
    folio INT NOT NULL COMMENT 'ID del registro con formato id_almacen + 0n + id_registro',
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
    pdf_docs VARCHAR(70) DEFAULT NULL COMMENT 'PDF con los documentos que se generan al llegar la dotacion',
    pdf_docs_coord varchar(70) DEFAULT NULL COMMENT 'PDF con los documentos que sube el coordinador administrativo',
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del registro',
    nota_cancelacion VARCHAR(500) COMMENT 'Nota de cancelacion de la dotacion',
    nota_modificacion VARCHAR(500) COMMENT 'Nota de modificación de la dotación',
    cancelado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotacion ha sido cancelada',
    verificado BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si la dotación ha sido verificada',
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
    cuota DECIMAL(10, 2) NOT NULL DEFAULT 0 COMMENT 'Cuota de dotacion',
    PRIMARY KEY (clave)
) COMMENT 'Tabla para almacenar las dotaciones de productos existentes en la institución';

INSERT INTO dotaciones(clave, programa, producto, medida) VALUES
(2024001, 'Personas Adultas Mayores', 'Personas Adultas Mayores', 'Caja'),
(2024002, 'Personas con Discapacidad', 'Personas con Discapacidad', 'Caja'),
(2024003, 'Personas en Situación de Emergencias o Desastres', 'Personas en Situación de Emergencias o Desastres', 'Caja'),
(2024004, 'Infantes de 2 a 5 años 11 meses', 'Infantes de 2 a 5 años 11 meses', 'Caja'),
(2024005, 'Lactantes de 6 a 24 meses', 'Lactantes de 6 a 24 meses', 'Caja'),
(2024006, 'Mujeres Embarazadas o en Periodo de Lactancia', 'Mujeres Embarazadas o en Periodo de Lactancia', 'Caja'),
(2024007, 'Desayunos Escolares Calientes', 'Aceite vegetal comestible puro de canola', 'Botella de 1L'),
(2024008, 'Desayunos Escolares Calientes', 'Arroz pulido calidad extra', 'Bolsa de 900g'),
(2024009, 'Desayunos Escolares Calientes', 'Atun aleta amarilla en agua', 'Lata de 140g'),
(2024010, 'Desayunos Escolares Calientes', 'Avena en hojuelas', 'Bolsa de 1kg'),
(2024011, 'Desayunos Escolares Calientes', 'Carne de res deshebrada', 'Pouch 1kg'),
(2024012, 'Desayunos Escolares Calientes', 'Chícharo con zanahoria', 'Lata de 430g'),
(2024013, 'Desayunos Escolares Calientes', 'Espagueti integral', 'Bolsa de 200g'),
(2024014, 'Desayunos Escolares Calientes', 'Frijol pinto nacional', 'Bolsa de 1kg'),
(2024015, 'Desayunos Escolares Calientes', 'Harina de maiz nixtamalizado', 'Bolsa de 1kg'),
(2024016, 'Desayunos Escolares Calientes', 'Leche entera en polvo', 'Bolsa de 1kg'),
(2024017, 'Desayunos Escolares Calientes', 'Lenteja última cosecha', 'Bolsa de 1kg'),
(2024018, 'Desayunos Escolares Calientes', 'Manzana amarilla fresca', 'Pieza'),
(2024019, 'Desayunos Escolares Calientes', 'Mix de arandano y manzana con cacahuate tostado', 'Bolsa de 30g'),
(2024020, 'Desayunos Escolares Calientes', 'Mix de arandanos deshidratados con semilla de girasol', 'Bolsa de 30g'),
(2024021, 'Desayunos Escolares Calientes', 'Soya texturizada', 'Bolsa de 330g'),
(2024022, 'Desayunos Escolares Calientes', 'Zanahoria fresca', 'Pieza'),
(2024023, 'Espacios de Alimentación', 'Aceite vegetal comestible puro de canola', 'Botella de 1L'),
(2024024, 'Espacios de Alimentación', 'Arroz pulido calidad extra', 'Botella de 900g'),
(2024025, 'Espacios de Alimentación', 'Atun aleta amarilla en agua', 'Lata de 140g'),
(2024026, 'Espacios de Alimentación', 'Avena en hojuelas', 'Bolsa de 1kg'),
(2024027, 'Espacios de Alimentación', 'Chícharo con zanahoria', 'Lata de 430g'),
(2024028, 'Espacios de Alimentación', 'Frijol pinto nacional', 'Bolsa de 1kg'),
(2024029, 'Espacios de Alimentación', 'Harina de maiz nixtamalizado', 'Bolsa de 1kg'),
(2024030, 'Espacios de Alimentación', 'Leche descremada en polvo', 'Bolsa de 1kg'),
(2024031, 'Espacios de Alimentación', 'Lenteja', 'Bolsa de 1kg'),
(2024032, 'Espacios de Alimentación', 'Pasta para sopa integral (codito 2)', 'Bolsa de 200g'),
(2024033, 'Espacios de Alimentación', 'Soya texturizada', 'Bolsa de 330g');

CREATE TABLE proveedores_autorizados(
    id_proveedor INT NOT NULL COMMENT 'ID del proveedor',
    programa VARCHAR(255) NOT NULL COMMENT 'Programa al que pertenece la dotacion',
    disponibilidad ENUM('SI', 'NO') NOT NULL COMMENT 'Disponibilidad del proveedor',
    ejercicio INT NOT NULL DEFAULT YEAR(CURDATE()) COMMENT 'Ejercicio del año al que pertenecen las dotaciones',
    PRIMARY KEY (id_proveedor, programa, ejercicio),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
) COMMENT 'Tabla para almacenar los proveedores autorizados por dotacion';

INSERT INTO proveedores_autorizados(id_proveedor, programa, disponibilidad, ejercicio) VALUES 
(1, 'Personas Adultas Mayores', 'SI', 2024),
(1, 'Personas con Discapacidad', 'SI', 2024),
(1, 'Personas en Situación de Emergencias o Desastres', 'SI', 2024),
(2, 'Infantes de 2 a 5 años 11 meses', 'SI', 2024),
(3, 'Lactantes de 6 a 24 meses', 'SI', 2024),
(3, 'Mujeres Embarazadas o en Periodo de Lactancia', 'SI', 2024),
(4, 'Desayunos Escolares Calientes', 'SI', 2024),
(3, 'Espacios de Alimentación', 'SI', 2024);

CREATE TABLE registro_entradas_registradas(
    id INT NOT NULL COMMENT 'ID del registro de entrada',
    clave INT NOT NULL COMMENT 'Clave de la dotacion',
    folio INT NOT NULL COMMENT 'Folio del registro',
    lote VARCHAR(100) NOT NULL COMMENT 'Lote del articulo',
    caducidad DATE NOT NULL COMMENT 'Fecha de caducidad del articulo',
    cantidad INT NOT NULL COMMENT 'Cantidad de articulos',
    PRIMARY KEY (id, clave, folio),
    FOREIGN KEY (clave) REFERENCES dotaciones(clave),
    FOREIGN KEY (id) REFERENCES registro_entradas(id)
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
(2025019, 'Desayunos Escolares Calientes', 'Mix de manzana deshidratada y cacahuates tostados', 'Bolsa de 30g', '5.90'),
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
(2025033, 'Desayunos Escolares Calientes', 'Mix de manzana deshidratada y cacahuates tostados', 'Bolsa de 30g', '6.15'),
(2025034, 'Espacios de Alimentación', 'Pasta para sopa integral (Codito #2)', 'Bolsa de 200g', '9.17'),
(2025035, 'Espacios de Alimentación', 'Soya texturizada', 'Bolsa de 330g', '24.05');

ALTER TABLE proveedores_autorizados ADD COLUMN ejercicio INT NOT NULL DEFAULT YEAR(CURDATE()) COMMENT 'Ejercicio del año al que pertenecen las dotaciones';

INSERT INTO proveedores(nombre, nombre_legal, rfc, direccion, telefono) VALUES ('Proveedor prueba', 'Proveedor prueba 2025 INC.', 'PP20250000000', 'Calle Somewhere 127', '443 3198 737');
INSERT INTO proveedores(nombre, nombre_legal, rfc, direccion) VALUES ('Abastos y Distribuciones Institucionales', 'ABASTOS Y DISTRIBUCIONES INSTITUCIONALES S.A. de C.V.', 'ADI991022KX2', 'Calle 4 Manzana 5 Lote 6 Col. Ejido del Moral, Iztapalapa, Ciudad de México, C.P. 09040');

ALTER TABLE proveedores_autorizados DROP PRIMARY KEY, ADD PRIMARY KEY (id_proveedor, programa, ejercicio);

INSERT INTO proveedores_autorizados (id_proveedor, programa, disponibilidad, ejercicio) VALUES 
(4, 'Desayunos Escolares Calientes', 'SI', 2025),
(4, 'Espacios de Alimentación', 'SI', 2025),
(1, 'Personas Adultas Mayores', 'SI', 2025),
(4, 'Personas con Discapacidad', 'SI', 2025),
(4, 'Personas en Situación de Emergencias o Desastres', 'SI', 2025),
(5, 'Infantes de 2 a 5 años 11 meses', 'SI',2025),
(5, 'Lactantes de 6 a 24 meses', 'SI', 2025),
(1, 'Mujeres Embarazadas o en Periodo de Lactancia', 'SI', 2025);

DELIMITER //
CREATE PROCEDURE insertar_registro_entradas(
    IN p_id_usuario INT,
    IN p_id_almacen INT,
    IN p_id_proveedor INT,
    IN p_id_entrada INT,
    IN p_entrega VARCHAR(255),
    IN p_dotacion ENUM('1', '2', '3', '4', '5', '6', '7', '8', '9', '9 - Ampliación'),
    IN p_nota VARCHAR(255),
    IN p_ejercicio INT
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(re.folio) INTO v_ultimo_folio FROM registro_entradas re JOIN registro_entradas_registradas rer ON re.folio = rer.folio
    WHERE re.id_almacen = p_id_almacen AND LEFT(rer.clave, 4) = p_ejercicio;

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
    SELECT folio, id FROM registro_entradas WHERE id = v_ultimo_id;
END //
DELIMITER ;

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
    IN p_nota VARCHAR(255),
    IN p_ejercicio INT
)
BEGIN
    DECLARE v_ultimo_id INT DEFAULT 0;
    DECLARE v_ultimo_folio INT DEFAULT 0;

    -- Obtener el último folio del almacén especificado
    SELECT MAX(rs.folio) INTO v_ultimo_folio FROM registro_salidas rs JOIN registro_salidas_registradas rsr ON rs.folio = rsr.folio
    WHERE rs.id_almacen = p_id_almacen AND LEFT(rsr.clave, 4) = p_ejercicio;

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
    SELECT folio, id FROM registro_salidas WHERE id = v_ultimo_id;
END
 // DELIMITER ;

ALTER TABLE registro_entradas_registradas DROP PRIMARY KEY;
ALTER TABLE registro_entradas_registradas ADD COLUMN id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID para el registro de las entradas o productos registrados';

ALTER TABLE registro_entradas_registradas DROP PRIMARY KEY;

DELETE FROM registro_entradas_registradas;
ALTER TABLE registro_entradas_registradas AUTO_INCREMENT = 1;

DELETE FROM registro_entradas;
ALTER TABLE registro_entradas AUTO_INCREMENT = 1;

DROP TABLE registro_entradas_registradas;
DROP TABLE registro_entradas;

ALTER TABLE registro_entradas_registradas ADD FOREIGN KEY (id) REFERENCES registro_entradas(id);