-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 01-07-2025 a las 21:49:28
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `conductores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dbo_rol`
--

DROP TABLE IF EXISTS `dbo_rol`;
CREATE TABLE IF NOT EXISTS `dbo_rol` (
  `Id_NivelRol` int NOT NULL,
  `NivelUsuario` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`Id_NivelRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `dbo_rol`
--

INSERT INTO `dbo_rol` (`Id_NivelRol`, `NivelUsuario`) VALUES
(1, 'Administrador'),
(2, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_abono`
--

DROP TABLE IF EXISTS `tbl_abono`;
CREATE TABLE IF NOT EXISTS `tbl_abono` (
  `IdAbonos` int NOT NULL AUTO_INCREMENT,
  `id_Escuela` int DEFAULT NULL,
  `ValorAbono` decimal(10,2) DEFAULT NULL,
  `FechaBono` date DEFAULT NULL,
  `Id_Medio_Pago` int DEFAULT NULL,
  PRIMARY KEY (`IdAbonos`),
  KEY `fk_tbl_abono_tbl_medio_pago` (`Id_Medio_Pago`),
  KEY `fk_tbl_abono_tbl_escuela` (`id_Escuela`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_auditoria`
--

DROP TABLE IF EXISTS `tbl_auditoria`;
CREATE TABLE IF NOT EXISTS `tbl_auditoria` (
  `ID_Auditoria` int NOT NULL AUTO_INCREMENT,
  `NroOrden` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `FechaHora` datetime DEFAULT NULL,
  `Accion` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `DatoAnterior` text COLLATE utf8mb4_spanish2_ci,
  `DatoNuevo` text COLLATE utf8mb4_spanish2_ci,
  `TablaAfectada` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`ID_Auditoria`),
  KEY `fk_auditoria_usuario` (`ID_Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_banco`
--

DROP TABLE IF EXISTS `tbl_banco`;
CREATE TABLE IF NOT EXISTS `tbl_banco` (
  `id_Banco` int NOT NULL AUTO_INCREMENT,
  `descBanco` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_Banco`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_banco`
--

INSERT INTO `tbl_banco` (`id_Banco`, `descBanco`) VALUES
(1, 'BANCOLOMBIA S.A.'),
(2, 'BANCO DE BOGOTÁ'),
(3, 'BANCO DAVIVIENDA S.A.'),
(4, 'BANCO BBVA COLOMBIA S.A.'),
(5, 'BANCO DE OCCIDENTE'),
(6, 'BANCO GNB SUDAMERIS S.A.'),
(7, 'BANCO COLPATRIA'),
(8, 'BANCO AGRARIO DE COLOMBIA'),
(9, 'BANCO ITAÚ CORPBANCA'),
(10, 'BANCO POPULAR S.A.'),
(11, 'BANCO CAJA SOCIAL'),
(12, 'BANCO AV VILLAS'),
(13, 'CITIBANK COLOMBIA'),
(14, 'BANCÓLDEX S.A.'),
(15, 'BANCO SANTANDER DE NEGOCIOS'),
(16, 'BANCO FALABELLA'),
(17, 'JPMORGAN CHASE BANK'),
(18, 'BANCOOMEVA'),
(19, 'BANCO FINANDINA'),
(20, 'BANCO SERFINANZA'),
(21, 'BANCO MUNDO MUJER'),
(22, 'BANCO PICHINCHA'),
(23, 'BANCO W'),
(24, 'MIBANCO'),
(25, 'BAN100'),
(26, 'BANCO COOPCENTRAL'),
(27, 'BANCO UNIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cartera`
--

DROP TABLE IF EXISTS `tbl_cartera`;
CREATE TABLE IF NOT EXISTS `tbl_cartera` (
  `Id_Cartera` int NOT NULL AUTO_INCREMENT,
  `Cedula_Cliente` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `NroOrden` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `Fecha_Pago` date DEFAULT NULL,
  `Valor_Pago` decimal(10,2) DEFAULT NULL,
  `Id_Medio_Pago` int DEFAULT NULL,
  `id_Banco` int DEFAULT NULL,
  `Comision` decimal(10,2) DEFAULT NULL,
  `Observaciones` text COLLATE utf8mb4_spanish2_ci,
 
  PRIMARY KEY (`Id_Cartera`),
  KEY `fk_tbl_cartera_tbl_cliente` (`Cedula_Cliente`),
  KEY `fk_tbl_cartera_tbl_banco` (`id_Banco`),
  KEY `fk_tbl_cartera_tbl_medio_pago` (`Id_Medio_Pago`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_cartera`
--

INSERT INTO `tbl_cartera` (`Id_Cartera`, `Cedula_Cliente`, `NroOrden`, `Fecha_Pago`, `Valor_Pago`, `Id_Medio_Pago`, `id_Banco`, `Comision`, `Observaciones`, `id_tipo_tramite`) VALUES
(1, '5555', '33333', '2025-07-01', 35000.00, 1, NULL, 0.00, '', ''),
(2, '33333', '444444444444', '2025-07-01', 400000.00, 1, NULL, 0.00, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_categoria`
--

DROP TABLE IF EXISTS `tbl_categoria`;
CREATE TABLE IF NOT EXISTS `tbl_categoria` (
  `Id_Categoria` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`Id_Categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_categoria`
--

INSERT INTO `tbl_categoria` (`Id_Categoria`, `descripcion`) VALUES
(1, 'Categoría A1'),
(2, 'Categoría A2'),
(3, 'Categoría B1'),
(4, 'Categoría B2'),
(5, 'Categoría B3'),
(6, 'Categoría C1'),
(7, 'Categoría C2'),
(8, 'Categoría C3'),
(9, 'PSICOSENSOMETRICO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cliente`
--

DROP TABLE IF EXISTS `tbl_cliente`;
CREATE TABLE IF NOT EXISTS `tbl_cliente` (
  `Cedula_Cliente` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellidos_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `Telefono` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `FechaRegistro` date NOT NULL,
  `id_Escuela` int NOT NULL,
  PRIMARY KEY (`Cedula_Cliente`),
  KEY `fk_tbl_Cliente_tbl_Escuela` (`id_Escuela`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_cliente`
--

INSERT INTO `tbl_cliente` (`Cedula_Cliente`, `apellidos_nombre`, `Telefono`, `FechaRegistro`, `id_Escuela`) VALUES
('12345678', 'ALMA MARCELA SILVA', '4444444', '2025-06-26', 1),
('22222', 'ELVER GOMEZ TORBA', '44444446', '2025-07-01', 1),
('33333', 'nory navas', '5555555555', '2025-07-01', 1),
('5555', 'juan perez', '6666666', '2025-07-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cliente_categoria`
--

DROP TABLE IF EXISTS `tbl_cliente_categoria`;
CREATE TABLE IF NOT EXISTS `tbl_cliente_categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Cedula_Cliente` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `Id_Categoria` int DEFAULT NULL,
  `FechaAsignacion` date DEFAULT NULL,
  `TipoTramite` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Cedula_Cliente` (`Cedula_Cliente`),
  KEY `Id_Categoria` (`Id_Categoria`),
  KEY `fk_tbl_cliente_categoria_tipotramite` (`TipoTramite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_escuela`
--

DROP TABLE IF EXISTS `tbl_escuela`;
CREATE TABLE IF NOT EXISTS `tbl_escuela` (
  `id_Escuela` int NOT NULL AUTO_INCREMENT,
  `descri_Escuela` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_Escuela`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_escuela`
--

INSERT INTO `tbl_escuela` (`id_Escuela`, `descri_Escuela`) VALUES
(1, 'Particular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_medio_pago`
--

DROP TABLE IF EXISTS `tbl_medio_pago`;
CREATE TABLE IF NOT EXISTS `tbl_medio_pago` (
  `id_Medio_Pago` int NOT NULL AUTO_INCREMENT,
  `desc_Medio_Pago` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_Medio_Pago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_medio_pago`
--

INSERT INTO `tbl_medio_pago` (`id_Medio_Pago`, `desc_Medio_Pago`) VALUES
(1, 'EFECTIVO'),
(2, 'TRANSFERENCIA'),
(3, 'CREDITO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_tipotramite`
--

DROP TABLE IF EXISTS `tbl_tipotramite`;
CREATE TABLE IF NOT EXISTS `tbl_tipotramite` (
  `Id_TipoTramite` int NOT NULL AUTO_INCREMENT,
  `Tramite` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`Id_TipoTramite`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_tipotramite`
--

INSERT INTO `tbl_tipotramite` (`Id_TipoTramite`, `Tramite`) VALUES
(1, 'Inicial'),
(2, 'Ascenso'),
(3, 'Descenso'),
(4, 'Refrendación'),
(5, 'Re-categorización');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuario`
--

DROP TABLE IF EXISTS `tbl_usuario`;
CREATE TABLE IF NOT EXISTS `tbl_usuario` (
  `ID_Usuario` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `Nombre_Usuario` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `Contrasena` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `NivelRol` int DEFAULT NULL,
  PRIMARY KEY (`ID_Usuario`),
  KEY `fk_tbl_usuario_dbo_rol` (`NivelRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tbl_usuario`
--

INSERT INTO `tbl_usuario` (`ID_Usuario`, `email`, `Nombre_Usuario`, `Contrasena`, `NivelRol`) VALUES
(16783903, 'makerii@hotmail.com', 'Dagoberto Murgueitio Avila', '$2y$10$.Hcm3E5IzVplUcLVU28FSudLu3gqoMlh.q83Vgyx9mhGGbj5nhWHO', 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_abono`
--
ALTER TABLE `tbl_abono`
  ADD CONSTRAINT `fk_tbl_abono_tbl_escuela` FOREIGN KEY (`id_Escuela`) REFERENCES `tbl_escuela` (`id_Escuela`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_auditoria`
--
ALTER TABLE `tbl_auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`ID_Usuario`) REFERENCES `tbl_usuario` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_cartera`
--
ALTER TABLE `tbl_cartera`
  ADD CONSTRAINT `fk_tbl_cartera_tbl_banco` FOREIGN KEY (`id_Banco`) REFERENCES `tbl_banco` (`id_Banco`),
  ADD CONSTRAINT `fk_tbl_cartera_tbl_cliente` FOREIGN KEY (`Cedula_Cliente`) REFERENCES `tbl_cliente` (`Cedula_Cliente`),
  ADD CONSTRAINT `fk_tbl_cartera_tbl_medio_pago` FOREIGN KEY (`Id_Medio_Pago`) REFERENCES `tbl_medio_pago` (`id_Medio_Pago`);

--
-- Filtros para la tabla `tbl_cliente`
--
ALTER TABLE `tbl_cliente`
  ADD CONSTRAINT `fk_tbl_Cliente_tbl_Escuela` FOREIGN KEY (`id_Escuela`) REFERENCES `tbl_escuela` (`id_Escuela`);

--
-- Filtros para la tabla `tbl_cliente_categoria`
--
ALTER TABLE `tbl_cliente_categoria`
  ADD CONSTRAINT `fk_tbl_cliente_categoria_tipotramite` FOREIGN KEY (`TipoTramite`) REFERENCES `tbl_tipotramite` (`Id_TipoTramite`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_cliente_categoria_ibfk_1` FOREIGN KEY (`Cedula_Cliente`) REFERENCES `tbl_cliente` (`Cedula_Cliente`),
  ADD CONSTRAINT `tbl_cliente_categoria_ibfk_2` FOREIGN KEY (`Id_Categoria`) REFERENCES `tbl_categoria` (`Id_Categoria`);

--
-- Filtros para la tabla `tbl_usuario`
--
ALTER TABLE `tbl_usuario`
  ADD CONSTRAINT `fk_tbl_usuario_dbo_rol` FOREIGN KEY (`NivelRol`) REFERENCES `dbo_rol` (`Id_NivelRol`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`NivelRol`) REFERENCES `dbo_rol` (`Id_NivelRol`),
  ADD CONSTRAINT `tbl_usuario_ibfk_1` FOREIGN KEY (`NivelRol`) REFERENCES `dbo_rol` (`Id_NivelRol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
