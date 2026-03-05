-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql.comunidad.city
-- Generation Time: Oct 03, 2024 at 12:01 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 8.1.2-1ubuntu2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fulltrust_proyectosjuanfernandez`
--

-- --------------------------------------------------------

--
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
CREATE TABLE `actividades` (
  `id` int NOT NULL,
  `proyecto` int DEFAULT NULL,
  `actividad` varchar(200) DEFAULT NULL,
  `ano` int DEFAULT NULL,
  `monto` int DEFAULT NULL,
  `finaciamiento` varchar(200) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `creado` timestamp NULL DEFAULT NULL,
  `actualizado` varchar(20) DEFAULT NULL,
  `monto_final` int DEFAULT NULL,
  `observaciones` text,
  `adjunto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `actividades`
--

INSERT INTO `actividades` (`id`, `proyecto`, `actividad`, `ano`, `monto`, `finaciamiento`, `estado`, `creado`, `actualizado`, `monto_final`, `observaciones`, `adjunto`) VALUES
(1, 140, 'Elaboración de Plan Comunal de Cultura', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 140, 'Elaboración de Plan Comunal de Cultura', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 140, 'Elaboración de Plan Comunal de Cultura', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 140, 'Elaboración de Plan Comunal de Cultura', 2029, 40000, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2028, 140000, 'Fondo Nacional de Desarrollo Regional (FNDR)', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 150, 'Elaboración de un Plan Patrimonial del centro histórico', 2029, 210000, 'Fondo Nacional de Desarrollo Regional (FNDR)', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2024, 10000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2026, 80000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 130, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 2028, 400000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 110, 'Programa de fomento del emprendimiento gastronómico y cultural', 2026, 0, '', '', NULL, '10-05-2024 08:42:23', NULL, '', NULL),
(21, 110, 'Programa de fomento del emprendimiento gastronómico y cultural', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 110, 'Programa de fomento del emprendimiento gastronómico y cultural', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 110, 'Programa de fomento del emprendimiento gastronómico y cultural', 2029, 108600, 'Comité de Desarrollo Productivo Regional', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 120, 'Programa extraescolar de educación patrimonial', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 120, 'Programa extraescolar de educación patrimonial', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 120, 'Programa extraescolar de educación patrimonial', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 120, 'Programa extraescolar de educación patrimonial', 2025, 45000, 'Presupuesto Municipal, SUBDERE, FNDR, Ministerio de las Culturas las Artes y el Patrimonio a través de FONPAT o FONDART', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 120, 'Programa extraescolar de educación patrimonial', 2026, 105000, 'Presupuesto Municipal, SUBDERE, FNDR, Ministerio de las Culturas las Artes y el Patrimonio a través de FONPAT o FONDART', NULL, NULL, NULL, NULL, NULL, NULL),
(29, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2024, 10000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(31, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2024, 20000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(32, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2026, 44000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2027, 88000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(35, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2028, 88000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 160, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 2029, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 170, 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 170, 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', 2027, 10000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(39, 170, 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', 2028, 20000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(40, 170, 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', 2029, 195000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(41, 180, 'Elaboración de Catastro y Plan de Conservación de monumentos en BNUP', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 180, 'Elaboración de Catastro y Plan de Conservación de monumentos en BNUP', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 180, 'Elaboración de Catastro y Plan de Conservación de monumentos en BNUP', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 180, 'Elaboración de Catastro y Plan de Conservación de monumentos en BNUP', 2025, 60000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(45, 710, 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 710, 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 710, 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', 2025, 73000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(48, 710, 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 740, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 740, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 740, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', 2027, 25000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(52, 740, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 740, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', 2029, 150000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(54, 730, 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 730, 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 730, 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', 2025, 65000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(57, 730, 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 720, 'Estudio Salud de Usuarios de Instalaciones Deportivas', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 720, 'Estudio Salud de Usuarios de Instalaciones Deportivas', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 720, 'Estudio Salud de Usuarios de Instalaciones Deportivas', 2028, 29600, 'IND', NULL, NULL, NULL, NULL, NULL, NULL),
(61, 720, 'Estudio Salud de Usuarios de Instalaciones Deportivas', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2025, 150360, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(65, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 410, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2025, 150360, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(71, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 420, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 480, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 480, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 480, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 480, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 480, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', 2026, 324600, 'FNDR, sectorial (MOP).', NULL, NULL, NULL, NULL, NULL, NULL),
(79, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2027, 137520, 'FNDR, Programa de espacios públicos y Quiero mi Barrio MINVU, Programa de Mejoramiento Urbano (PMU) ', NULL, NULL, NULL, NULL, NULL, NULL),
(83, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 440, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2027, 123480, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(89, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 430, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 490, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 490, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 490, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 490, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', 2025, 190320, 'FNDR, Municipalidad de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL),
(96, 490, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2025, 163800, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(100, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 450, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2026, 178320, 'FNDR, Municipalidad de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL),
(109, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 460, 'Estudio de red peatonal en área central ciudad de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2025, 147400, 'FNDR, Municipalidad de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL),
(117, 470, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 610, 'Programa de Convivencia Escolar en Establecimientos Educacionales de Antofagasta (Habilidades para la Vida - HPV)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 610, 'Programa de Convivencia Escolar en Establecimientos Educacionales de Antofagasta (Habilidades para la Vida - HPV)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 610, 'Programa de Convivencia Escolar en Establecimientos Educacionales de Antofagasta (Habilidades para la Vida - HPV)', 2025, 250000, 'JUNAEB / MINEDUC', NULL, NULL, NULL, NULL, NULL, NULL),
(121, 640, 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 640, 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 640, 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', 2025, 200000, 'FNDR / FAEP MINEDUC', NULL, NULL, NULL, NULL, NULL, NULL),
(124, 640, 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 680, 'Construcción Escuela de Párvulos Sector Norte', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 680, 'Construcción Escuela de Párvulos Sector Norte', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 680, 'Construcción Escuela de Párvulos Sector Norte', 2024, 15000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(128, 680, 'Construcción Escuela de Párvulos Sector Norte', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 680, 'Construcción Escuela de Párvulos Sector Norte', 2025, 34715, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(130, 680, 'Construcción Escuela de Párvulos Sector Norte', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 680, 'Construcción Escuela de Párvulos Sector Norte', 2027, 1024681, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(132, 680, 'Construcción Escuela de Párvulos Sector Norte', 2028, 150000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(133, 680, 'Construcción Escuela de Párvulos Sector Norte', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 670, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 670, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 670, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 2024, 50000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(137, 670, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 670, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 2026, 845000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(139, 620, 'Programa de Becas de Normalización de Estudios de Profesionales que ejercen Docencia', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 620, 'Programa de Becas de Normalización de Estudios de Profesionales que ejercen Docencia', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 620, 'Programa de Becas de Normalización de Estudios de Profesionales que ejercen Docencia', 2028, 203000, 'MINEDUC, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(142, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2027, 15000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(145, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2028, 30000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(147, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 650, 'Centro Tecnológico Digital para la Educación y la Innovación', 2029, 216476, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(149, 660, 'Construcción de Liceo Sector La Chimba', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 660, 'Construcción de Liceo Sector La Chimba', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 660, 'Construcción de Liceo Sector La Chimba', 2028, 10000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(152, 660, 'Construcción de Liceo Sector La Chimba', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 660, 'Construcción de Liceo Sector La Chimba', 2029, 20000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(154, 630, 'Programa de Certificación Ambiental de los Establecimientos Educacionales Municipales (Sello sustentable)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 630, 'Programa de Certificación Ambiental de los Establecimientos Educacionales Municipales (Sello sustentable)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 630, 'Programa de Certificación Ambiental de los Establecimientos Educacionales Municipales (Sello sustentable)', 2028, 223000, 'FNDR / FPA MMA', NULL, NULL, NULL, NULL, NULL, NULL),
(157, 340, 'Desarrollo Sistema OMIL ON LINE', 2024, 10000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(158, 340, 'Desarrollo Sistema OMIL ON LINE', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 340, 'Desarrollo Sistema OMIL ON LINE', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 340, 'Desarrollo Sistema OMIL ON LINE', 2025, 80000, 'Presupuesto Municipal, Subsecretaría del Trabajo', NULL, NULL, NULL, NULL, NULL, NULL),
(161, 340, 'Desarrollo Sistema OMIL ON LINE', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 340, 'Desarrollo Sistema OMIL ON LINE', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2026, 50000, 'Comité de Desarrollo Productivo Regional, FNDR, SENCE', NULL, NULL, NULL, NULL, NULL, NULL),
(167, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 350, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 310, 'Centro municipal de emprendimiento colaborativo (cowork)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 310, 'Centro municipal de emprendimiento colaborativo (cowork)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 310, 'Centro municipal de emprendimiento colaborativo (cowork)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 310, 'Centro municipal de emprendimiento colaborativo (cowork)', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 310, 'Centro municipal de emprendimiento colaborativo (cowork)', 2029, 129200, 'Comité de Desarrollo Productivo Regional, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(175, 330, 'Programa de capacitación y financiamiento en Tecnología de la Información', 2026, 8000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(176, 330, 'Programa de capacitación y financiamiento en Tecnología de la Información', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 330, 'Programa de capacitación y financiamiento en Tecnología de la Información', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 330, 'Programa de capacitación y financiamiento en Tecnología de la Información', 2029, 157600, 'Comité de Desarrollo Productivo Regional, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(179, 320, 'Programa para el fomento del microemprendimiento de servicios turísticos', 2026, 8000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(180, 320, 'Programa para el fomento del microemprendimiento de servicios turísticos', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 320, 'Programa para el fomento del microemprendimiento de servicios turísticos', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 320, 'Programa para el fomento del microemprendimiento de servicios turísticos', 2029, 106600, 'Comité de Desarrollo Productivo Regional, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(183, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2024, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2024, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2024, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2025, 120000, 'Comité de Desarrollo Productivo Regional, FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(187, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 360, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', 2026, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 220, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 220, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 220, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 220, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', 2027, 60000, 'Fondo de Protección Ambiental (FPA). Ministerio medio Ambiente', NULL, NULL, NULL, NULL, NULL, NULL),
(193, 220, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2026, 100000, 'Fondo para el reciclaje (FPR), Ministerio del Medio Ambiente', NULL, NULL, NULL, NULL, NULL, NULL),
(198, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 230, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 240, 'Modelo de gestión Clínica Veterinaria Municipal', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 240, 'Modelo de gestión Clínica Veterinaria Municipal', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 240, 'Modelo de gestión Clínica Veterinaria Municipal', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 240, 'Modelo de gestión Clínica Veterinaria Municipal', 2028, 40000, 'Programa Nacional de Tenencia Responsable de Animales de Compañía. División Municipalidades. SUBDERE', NULL, NULL, NULL, NULL, NULL, NULL),
(204, 240, 'Modelo de gestión Clínica Veterinaria Municipal', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2028, 70000, 'Fondo de Protección Ambiental (FPA), Ministerio Medio Ambiente', NULL, NULL, NULL, NULL, NULL, NULL),
(210, 250, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 210, 'Programa extraescolar de educación ambiental y del patrimonio natural', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 210, 'Programa extraescolar de educación ambiental y del patrimonio natural', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 210, 'Programa extraescolar de educación ambiental y del patrimonio natural', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 210, 'Programa extraescolar de educación ambiental y del patrimonio natural', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 210, 'Programa extraescolar de educación ambiental y del patrimonio natural', 2026, 140000, 'Fondo de Protección Ambiental (FPA), Ministerio Medio Ambiente', NULL, NULL, NULL, NULL, NULL, NULL),
(216, 260, 'Actualización Proyecto Vivero Municipal', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(217, 260, 'Actualización Proyecto Vivero Municipal', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 260, 'Actualización Proyecto Vivero Municipal', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(219, 260, 'Actualización Proyecto Vivero Municipal', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 260, 'Actualización Proyecto Vivero Municipal', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 260, 'Actualización Proyecto Vivero Municipal', 2026, 80000, 'Programa Mejoramiento Urbano y equipamiento comunal (PMU), SUBDERE', NULL, NULL, NULL, NULL, NULL, NULL),
(222, 260, 'Actualización Proyecto Vivero Municipal', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(223, 260, 'Actualización Proyecto Vivero Municipal', 2028, 1300000, 'Programa Mejoramiento Urbano y equipamiento comunal (PMU), SUBDERE', NULL, NULL, NULL, NULL, NULL, NULL),
(224, 270, 'Crematorio de Mascotas', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 270, 'Crematorio de Mascotas', 2026, 90000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(226, 270, 'Crematorio de Mascotas', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 270, 'Crematorio de Mascotas', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 820, 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 820, 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 820, 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', 2024, 120000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(231, 820, 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 810, 'Programa de Promoción de Exámenes Médicos Preventivos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 810, 'Programa de Promoción de Exámenes Médicos Preventivos', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 810, 'Programa de Promoción de Exámenes Médicos Preventivos', 2024, 250000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(235, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2024, 10000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(238, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2025, 120000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(240, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2027, 6000000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(242, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2028, 1250000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(243, 830, 'Construcción nuevo CESFAM comuna de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2024, 10000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(247, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(248, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2025, 120000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(249, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2027, 6000000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(251, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2028, 1250000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(252, 840, 'Reposición CESFAM Centro sur de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(255, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2027, 25000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(256, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2028, 40000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(258, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(259, 850, 'Construcción de Laboratorio comunal de Antofagasta', 2029, 450000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(260, 860, 'Proyecto mejoramiento de la infraestructura de la red digital de los centros de salud', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 860, 'Proyecto mejoramiento de la infraestructura de la red digital de los centros de salud', 2028, 400000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(262, 860, 'Proyecto mejoramiento de la infraestructura de la red digital de los centros de salud', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 870, 'Modernización Cementerio General de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 870, 'Modernización Cementerio General de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 870, 'Modernización Cementerio General de Antofagasta', 2024, 60000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(266, 870, 'Modernización Cementerio General de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(267, 870, 'Modernización Cementerio General de Antofagasta', 2024, 100000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(268, 870, 'Modernización Cementerio General de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 870, 'Modernización Cementerio General de Antofagasta', 2026, 1000000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(270, 540, 'Estudio diseño observatorio comunal del delito', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 540, 'Estudio diseño observatorio comunal del delito', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 540, 'Estudio diseño observatorio comunal del delito', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 540, 'Estudio diseño observatorio comunal del delito', 2025, 31000, 'SPD / municipalidad', NULL, NULL, NULL, NULL, NULL, NULL),
(274, 540, 'Estudio diseño observatorio comunal del delito', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(275, 540, 'Estudio diseño observatorio comunal del delito', 2027, 60000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(276, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(277, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2026, 2185000, 'Presupuesto Municipal, Subsecretaría Protección del Delito, FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(282, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2027, 2500000, 'Presupuesto Municipal, Subsecretaría Protección del Delito, FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(283, 510, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', 2028, 5000000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(284, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(285, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(287, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2027, 60000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(288, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2028, 25000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(291, 520, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(293, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2024, 10000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(294, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(296, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(297, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(298, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(299, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2026, 119000, 'Presupuesto Municipal, Subsecretaría Protección del Delito, FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(300, 530, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(301, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(302, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2026, 15000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(303, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(304, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2027, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(306, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(307, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2028, 43000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(308, 560, 'Construcción Centros comunitarios de seguridad en los barrios', 2029, 220000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(309, 910, 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(310, 910, 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 910, 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(312, 910, 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', 2027, 100000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(313, 940, 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 940, 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 940, 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 940, 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', 2027, 42000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(317, 960, 'Oficinas de atención municipal en sectores más alejados de la comuna', 2027, 12000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(318, 960, 'Oficinas de atención municipal en sectores más alejados de la comuna', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 960, 'Oficinas de atención municipal en sectores más alejados de la comuna', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 920, 'Programa Fondo DIDECO para las organizaciones sociales', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(321, 920, 'Programa Fondo DIDECO para las organizaciones sociales', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 920, 'Programa Fondo DIDECO para las organizaciones sociales', 2029, 90000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(323, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2027, 34000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(324, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(328, 950, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', 2029, 19000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(329, 960, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 960, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(331, 960, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(332, 960, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(333, 960, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', 2025, 63500, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(334, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(335, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(336, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(337, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2025, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(338, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(339, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2026, 140000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(341, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(342, 1110, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 2028, 1500000, 'FNDR', NULL, NULL, NULL, NULL, NULL, NULL),
(343, 1160, 'Oficina de Información turística como centro de atención al turista', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(344, 1160, 'Oficina de Información turística como centro de atención al turista', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(345, 1160, 'Oficina de Información turística como centro de atención al turista', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(346, 1160, 'Oficina de Información turística como centro de atención al turista', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(347, 1160, 'Oficina de Información turística como centro de atención al turista', 2025, 400000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(348, 1160, 'Oficina de Información turística como centro de atención al turista', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `actividades` (`id`, `proyecto`, `actividad`, `ano`, `monto`, `finaciamiento`, `estado`, `creado`, `actualizado`, `monto_final`, `observaciones`, `adjunto`) VALUES
(349, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(351, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(352, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(353, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2026, 60000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(354, 1130, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(356, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(357, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(359, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2028, 200000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(360, 1140, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 1150, 'Observatorio Turístico comunal', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(362, 1150, 'Observatorio Turístico comunal', 2027, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(363, 1150, 'Observatorio Turístico comunal', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 1150, 'Observatorio Turístico comunal', 2028, 40000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(365, 1150, 'Observatorio Turístico comunal', 2029, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(366, 1120, 'Diseño y puesta en valor de circuitos turísticos nocturno en contexto con el astroturismo', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 1120, 'Diseño y puesta en valor de circuitos turísticos nocturno en contexto con el astroturismo', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(368, 1120, 'Diseño y puesta en valor de circuitos turísticos nocturno en contexto con el astroturismo', 2027, 40000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(369, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(372, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2025, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(373, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2025, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(374, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(375, 1170, 'Implementación de infraestructura habilitante en playas de administración Municipal', 2026, 400000, 'FNDR, Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(376, 1010, 'Programa de fortalecimiento y mejora de atención al usuario', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(377, 1010, 'Programa de fortalecimiento y mejora de atención al usuario', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(378, 1010, 'Programa de fortalecimiento y mejora de atención al usuario', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 1010, 'Programa de fortalecimiento y mejora de atención al usuario', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(380, 1010, 'Programa de fortalecimiento y mejora de atención al usuario', 2024, 55000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(381, 1030, 'Programa de formación continua de profesionales del área de TI', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 1030, 'Programa de formación continua de profesionales del área de TI', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(383, 1030, 'Programa de formación continua de profesionales del área de TI', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(384, 1030, 'Programa de formación continua de profesionales del área de TI', 2024, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(385, 1030, 'Programa de formación continua de profesionales del área de TI', 2024, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(386, 1050, 'Laboratorio de innovación y proyectos internos en gestión municipal', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(387, 1050, 'Laboratorio de innovación y proyectos internos en gestión municipal', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(388, 1050, 'Laboratorio de innovación y proyectos internos en gestión municipal', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(389, 1050, 'Laboratorio de innovación y proyectos internos en gestión municipal', 2028, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(390, 1050, 'Laboratorio de innovación y proyectos internos en gestión municipal', 2028, 30000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(391, 1020, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(392, 1020, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(393, 1020, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(394, 1020, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(395, 1020, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', 2027, 74000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(396, 1040, 'Programa de instalación de los valores culturales de la IMA', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(397, 1040, 'Programa de instalación de los valores culturales de la IMA', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(398, 1040, 'Programa de instalación de los valores culturales de la IMA', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(399, 1040, 'Programa de instalación de los valores culturales de la IMA', 2026, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(400, 1040, 'Programa de instalación de los valores culturales de la IMA', 2027, 150000, 'Presupuesto Municipal', NULL, NULL, NULL, NULL, NULL, NULL),
(402, 280, 'Creación de equipo técnico a cargo - interno', 2024, 0, '', '', NULL, '28-08-2023 16:41:55', NULL, NULL, ''),
(403, 280, 'Preparación de TTR', 2024, 0, '', '', NULL, '28-08-2023 16:42:24', NULL, NULL, ''),
(404, 280, 'Presentación Concejo Municipal', 2024, 0, '', '', NULL, '28-08-2023 16:42:42', NULL, NULL, ''),
(405, 280, 'Aprobación de financiamiento MUNICIPAL', 2024, 0, '', '', NULL, '28-08-2023 16:42:58', NULL, NULL, ''),
(406, 280, 'Postulación a financiamiento fuentes externas', 2024, 0, '', '', NULL, '28-08-2023 16:43:17', NULL, NULL, ''),
(407, 280, 'Licitación estudio', 2024, 0, '', '', NULL, '28-08-2023 16:43:34', NULL, NULL, ''),
(408, 280, 'Ejecución estudio', 2025, 200000, 'FNDR', '', NULL, '28-08-2023 16:44:02', NULL, NULL, ''),
(409, 280, 'Implementación plan', 2026, 0, '', '', NULL, '28-08-2023 16:44:22', NULL, NULL, ''),
(414, 0, '1', 2, 3, '4', 'Incompleta', '0000-00-00 00:00:00', NULL, 0, '', '--20240906171410-INV271109965.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `id` int NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `codigo`, `area`, `color`) VALUES
(29, '1', 'Cultura y Patrimonio', '#d90429'),
(30, '7', 'Deporte y Recreación', '#e36414'),
(31, '3', 'Desarrollo Productivo', '#ffbd00'),
(32, '4', 'Desarrollo urbano', '#0096c7'),
(33, '6', 'Educación', '#03045e'),
(34, '10', 'Institucional', '#489fb5'),
(35, '2', 'Medio Ambiente', '#90a955'),
(36, '8', 'Salud', '#415a77'),
(37, '5', 'Seguridad vecinal', '#7b2cbf'),
(38, '9', 'Social – Comunitario', '#136f63'),
(39, '11', 'Turismo', '#3f7d20');

-- --------------------------------------------------------

--
-- Table structure for table `area_gestion`
--

DROP TABLE IF EXISTS `area_gestion`;
CREATE TABLE `area_gestion` (
  `id` int NOT NULL,
  `area_gestion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `area_gestion`
--

INSERT INTO `area_gestion` (`id`, `area_gestion`) VALUES
(1, 'Cultura'),
(2, 'Deportes'),
(3, 'DIDECO, Oficina Municipal de Vivienda (por crear)'),
(4, 'Educación'),
(5, 'Educación '),
(6, 'Educación y Patrimonio Cultural'),
(7, 'Espacio público, infraestructura'),
(8, 'Fomento productivo'),
(9, 'Institucional'),
(10, 'Medio Ambiente'),
(11, 'Patrimonio Cultural'),
(12, 'Pesca Artesanal'),
(13, 'Planificación Territorial'),
(14, 'Planificación Urbana'),
(15, 'PROGRAMA DE FORMACIÓN, CAPACITACIÓN Y CONCIENCIA'),
(16, 'PROGRAMA DE GESTIÓN Y SOSTENIBILIDAD TURÍSTICA'),
(17, 'PROGRAMA DE PROYECTOS TURÍSTICOS CLAVE Y MEJORA DE SERVICIOS'),
(18, 'PROGRAMA DE ZONAS DE DESARROLLO Y ATRACCIÓN DE INVERSIONES'),
(19, 'Proyectos infraestructura'),
(20, 'RRHH'),
(21, 'Salud'),
(22, 'SECPLA, Medioambiente, Aseo y Ornato'),
(23, 'Secretaría Municipal'),
(24, 'Seguridad Pública'),
(25, 'Social'),
(26, 'Turismo'),
(27, 'Vivienda'),
(28, 'Vivienda ');

-- --------------------------------------------------------

--
-- Table structure for table `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `id` int NOT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `estados`
--

INSERT INTO `estados` (`id`, `estado`, `descripcion`) VALUES
(7, 'En espera', 'La iniciativa se encuentra a nivel de idea, su proceso de ejecuciÃ³n no se  ha iniciado.'),
(8, 'Formulada', 'La iniciativa ya cuenta con sus tÃ©rminos de referencia para la etapa de estudio, ejecuciÃ³n o diseÃ±o, dependiendo de sus tipo.'),
(9, 'Postulada a financiamiento', 'La iniciativa se encuentra solicitando financiamiento a la fuente respectiva.'),
(10, 'En licitación', 'La iniciativa se estÃ¡ licitando para la etapa de diseÃ±o o ejecuciÃ³n.'),
(11, 'Etapa en ejecución', 'La iniciativa se ha iniciado (diseÃ±o y/o ejecuciÃ³n) y estÃ¡ en ejecuciÃ³n indicando su estado de avance en relaciÃ³n al avance de actividades.'),
(12, 'Detenida por fuerza mayor', 'La iniciativa se iniciÃ³ y se encuentra detenida por razones de fuerza mayor. Se deben describir los motivos de detenciÃ³n paraque se puedan gestionar en las instancias de coordinaciÃ³n y PlanificaciÃ³n sugeridas.'),
(13, 'Sustituida', 'La iniciativa ha sido eliminada por motivos que se han justificado correctamente. Se debe dejar registro de esta instancia y su motivo.'),
(14, 'Eliminada', 'La iniciativa ha sido eliminada por motivos que se han justificado correctamente. Se debe dejar registro de esta instancia y su motivo.');

-- --------------------------------------------------------

--
-- Table structure for table `origen`
--

DROP TABLE IF EXISTS `origen`;
CREATE TABLE `origen` (
  `id` int NOT NULL,
  `origen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `origen`
--

INSERT INTO `origen` (`id`, `origen`) VALUES
(2, 'PLADECO'),
(4, 'PLADETUR');

-- --------------------------------------------------------

--
-- Table structure for table `parametros`
--

DROP TABLE IF EXISTS `parametros`;
CREATE TABLE `parametros` (
  `parametros_id` int NOT NULL,
  `parametros_titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parametros_codproyecto` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parametros`
--

INSERT INTO `parametros` (`parametros_id`, `parametros_titulo`, `parametros_codproyecto`) VALUES
(1, 'Plan de Desarrollo Comunal - 2025 / 2029', '001');

-- --------------------------------------------------------

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE `proyectos` (
  `id` int NOT NULL,
  `cod_area` int DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `subarea` varchar(100) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL,
  `lineamiento` text,
  `objetivos` text,
  `cod_tipo` varchar(20) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `cod_iniciativa` varchar(20) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `p_diseno` int DEFAULT NULL,
  `p_prefactibilidad` int DEFAULT NULL,
  `p_ejecucion` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT '0.00',
  `area_gestion` varchar(100) DEFAULT NULL,
  `plazo` varchar(100) DEFAULT NULL,
  `presupuesto` float DEFAULT NULL,
  `financiamiento` text,
  `unidad` varchar(100) DEFAULT NULL,
  `e` varchar(20) DEFAULT NULL,
  `entidades_relacionadas` varchar(100) DEFAULT NULL,
  `lat` varchar(100) DEFAULT NULL,
  `lng` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `cod_estado` varchar(20) DEFAULT NULL,
  `pin` varchar(100) DEFAULT NULL,
  `proyectos_last_update` varchar(100) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `ano_inicio` varchar(20) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `status` int DEFAULT '0',
  `descripcion` text,
  `politicas` text,
  `problema` text,
  `area_influencia` varchar(100) DEFAULT NULL,
  `deficit` text,
  `solucion` text,
  `beneficios` text,
  `orientaciones` text,
  `etapa` varchar(100) DEFAULT NULL,
  `avance_etapas` decimal(10,2) DEFAULT '0.00',
  `avance_financiero` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proyectos`
--

INSERT INTO `proyectos` (`id`, `cod_area`, `area`, `subarea`, `origen`, `lineamiento`, `objetivos`, `cod_tipo`, `tipo`, `cod_iniciativa`, `nombre`, `p_diseno`, `p_prefactibilidad`, `p_ejecucion`, `total`, `area_gestion`, `plazo`, `presupuesto`, `financiamiento`, `unidad`, `e`, `entidades_relacionadas`, `lat`, `lng`, `estado`, `cod_estado`, `pin`, `proyectos_last_update`, `pdf`, `ano_inicio`, `ubicacion`, `status`, `descripcion`, `politicas`, `problema`, `area_influencia`, `deficit`, `solucion`, `beneficios`, `orientaciones`, `etapa`, `avance_etapas`, `avance_financiero`) VALUES
(110, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Fortalecer la educación y la calidad de vida a través de la cultura', 'Promover la creación artística y el emprendimiento creativo', 'b', 'Programa', '1.1', 'Programa de fomento del emprendimiento gastronómico y cultural', 0, 0, 108600, 108600.00, 'Cultura y Patrimonio', NULL, 108600, 'Comité́ de Desarrollo Productivo Regional', 'Departamento de Fomento Productivo y Empleo', NULL, 'CORFO, SERCOTEC, SERNATUR, MINCAP', '-33.638062416655195', '-78.83599295459608', 'En espera', '1', NULL, '23-08-2024 22:15:13', '1.1.pdf', NULL, '3', 1, 'Programa de fomento del emprendimiento gastronómico y cultural', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(120, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Hacer del patrimonio un valor cultural y económico.', 'Impulsar el desarrollo cultural y el patrimonio en edades tempranas', 'b', 'Programa', '1.2', 'Programa extraescolar de educación patrimonial', NULL, NULL, 150000, 150000.00, 'Cultura y Patrimonio', NULL, 150000, 'FNDR, Municipalidad de Antofagasta, MINEDUC', 'Corporación Municipal de Desarrollo Social', NULL, 'Consejo de Monumentos Nacionales (CMN), Servicio Nacional del Patrimonio Cultural Regional (SERPAT),', '-33.63626593584484', '-78.82784782805784', 'Sustituida', '1', NULL, NULL, '1.2.pdf', NULL, '18', 1, 'Programa extraescolar de educación patrimonial', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Fortalecer la educación y la calidad de vida a través de la cultura', 'Objetivo 1: Generar espacios para la participación y el diálogo multicultural. // Objetivo 2: Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'a', 'Estudio', '1.3', 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', 0, 0, 80000, 80000.00, 'Cultura y Patrimonio', NULL, 80000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Cultura, Arte y Patrimonio', NULL, 'MINCAP, MINVU', '1', '2', 'En espera', '1', NULL, '03-10-2024 19:28:33', '1.3.pdf', NULL, '31', 1, 'Plan Maestro de habilitación espacios públicos para uso cultural en los barrios', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(140, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Fortalecer la educación y la calidad de vida a través de la cultura', 'Objetivo 1: Generar espacios para la participación y el diálogo multicultural. // Objetivo 2: Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'a', 'Estudio', '1.4', 'Elaboración de Plan Comunal de Cultura', NULL, NULL, 40000, 40000.00, 'Cultura y Patrimonio', NULL, 40000, 'Municipalidad de Antofagasta, MINCAP', 'Dirección de Cultura, Arte y Patrimonio', NULL, 'MINCAP, Gobierno Regional', '-33.63854673994556', '-78.83193154827359', 'Detenida por fuerza mayor', '1', NULL, NULL, '1.4.pdf', NULL, '31', 1, 'Elaboración de Plan Comunal de Cultura', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Hacer del patrimonio un valor cultural y económico.', 'Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'a', 'Estudio', '1.5', 'Elaboración de un Plan Patrimonial del centro histórico', NULL, NULL, 350000, 350000.00, 'Cultura y Patrimonio', NULL, 350000, 'FNDR, MINVU', 'Dirección de Cultura, Arte y Patrimonio', NULL, 'Ministerio de Vivienda y Urbanismo (MINVU), Consejo de Monumentos Nacionales (CMN), Unidad de Educac', '-33.641609217343714', '-78.82669964994113', 'En espera', '1', NULL, NULL, '1.5.pdf', NULL, '3', 1, 'Elaboración de un Plan Patrimonial del centro histórico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Hacer del patrimonio un valor cultural y económico.', 'Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'c', 'Proyecto', '1.6', 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', 220000, NULL, 4000000, 4000000.00, 'Cultura y Patrimonio', NULL, 4220000, 'FNDR - PVP', 'SECOPLAN', NULL, 'Dirección de Arquitectura Región de Antofagasta, Ministerio de Obras Públicas (MOP)', '-33.63695716506848', '-78.83395242676416', 'Sustituida', '1', NULL, NULL, '1.6.pdf', NULL, '31', 1, 'Restauración Casa de la Cultura (edificio ex Muni de Antofagasta, Calle Latorre 2535)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Hacer del patrimonio un valor cultural y económico.', 'Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'c', 'Proyecto', '1.7', 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', 195000, NULL, 4000000, 4000000.00, 'Cultura y Patrimonio', NULL, 4195000, 'FNDR - PVP', 'SECOPLAN', NULL, 'Dirección de Arquitectura Región de Antofagasta, Ministerio de Obras Públicas (MOP)', '-33.643185616055575', '-78.83522051173904', 'En espera', '1', NULL, NULL, '1.7.pdf', NULL, '1', 1, 'Restauración, habilitación de Centro Cultural (edificio Sucre 444)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 1, 'Cultura y Patrimonio', NULL, 'PLADECO', 'Hacer del patrimonio un valor cultural y económico.', 'Impulsar una cartera de proyectos de infraestructura cultural y patrimonial.', 'a', 'Estudio', '1.8', 'Elaboración de Catastro y Plan de Conservación de monumentos en Bien Nacional Uso Público (BNUP).', NULL, NULL, 60000, 60000.00, 'Cultura y Patrimonio', NULL, 60000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Operaciones', NULL, 'FNDR, Presupuesto Municipal', '-33.64040188932122', '-78.83192030206827', 'En espera', '1', NULL, NULL, '1.8.pdf', NULL, '33', 1, 'Elaboración de Catastro y Plan de Conservación de monumentos en Bien Nacional Uso Público (BNUP).', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Generar conciencia y acciones sostenibles frente al cambio climático en la comunidad', 'b', 'Programa', '2.1', 'Programa extraescolar de educación ambiental y del patrimonio natural', NULL, NULL, 140000, 140000.00, 'Medio Ambiente', NULL, 140000, 'Ministerio medio Ambiente (FPA), FNDR, SUBDERE', 'Dirección de Medio Ambiente y Ornato /Departamento de Medio Ambiente', NULL, 'SEREMI de Medio Ambiente Región de Antofagasta', '-33.636482249669456', '-78.83460018839368', 'Etapa en ejecución', '1', NULL, NULL, '2.1.pdf', NULL, '8', 1, 'Programa extraescolar de educación ambiental y del patrimonio natural', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Generar conciencia y acciones sostenibles frente al cambio climático en la comunidad', 'a', 'Estudio', '2.2', 'Plan Comunal de Mitigación y Adaptación al cambio Climático', NULL, NULL, 60000, 60000.00, 'Medio Ambiente', NULL, 60000, 'FPA Ministerio de Medio Ambiente, FNDR, Cooperación internacional: GIZ, GEF, BID, Bonos Verdes Ministerio de Hacienda', 'Dirección de Medio Ambiente y Ornato/Departamento Medio Ambiente', NULL, 'SEREMI Medio Ambiente Región Antofagasta, Gobierno Regional', '-33.643330660549566', '-78.82688059255823', 'En espera', '1', NULL, NULL, '2.2.pdf', NULL, '8', 1, 'Plan Comunal de Mitigación y Adaptación al cambio Climático', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Impulsar un sistema de reciclaje con separación en origen a nivel comunal.', 'a', 'Estudio', '2.3', 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', NULL, NULL, 100000, 100000.00, 'Medio Ambiente', NULL, 100000, 'Ministerio Medio Ambiente (FPA, FPR), FRIL, FNDR', 'Dirección de Medio Ambiente y Ornato', NULL, 'SEREMI Medio Ambiente Región Antofagasta, Gobierno Regional', '-33.639492345798175', '-78.8321806140766', 'En licitación', '1', NULL, NULL, '2.3.pdf', NULL, '8', 1, 'Plan de Recolección Selectiva y valorización de Residuos domiciliarios y asimilables', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Promover la tenencia responsable de mascotas.', 'a', 'Estudio', '2.4', 'Modelo de gestión Clínica veterinaria municipal', NULL, NULL, 40000, 40000.00, 'Medio Ambiente', NULL, 40000, 'Municipalidad de Antofagasta, Programa Nacional de Tenencia Responsable de Animales de Compañía SUBDERE', 'Dirección de Medio Ambiente y Ornato/Departamento Medio Ambiente', NULL, 'SEREMI Salud, SEREMI Medio Ambiente', '-33.6403491775283', '-78.83405719109254', 'Detenida por fuerza mayor', '1', NULL, NULL, '2.4.pdf', NULL, '5', 1, 'Modelo de gestión Clínica veterinaria municipal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Promover la gestión sustentable en el municipio.', 'a', 'Estudio', '2.5', 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', NULL, NULL, 70000, 70000.00, 'Medio Ambiente', NULL, 70000, 'Ministerio Medio Ambiente (FPA), FNDR, Servicio de Asistencia Técnica Especializada (SATE), SUBDERE', 'Dirección de Medio Ambiente y Ornato/ Departamento de Medio Ambiente', NULL, 'SEREMI Medio Ambiente Región Antofagasta, Gobierno Regional', '-33.64459781105401', '-78.83316817878666', 'En espera', '1', NULL, NULL, '2.5.pdf', NULL, '8', 1, 'Formulación de observatorio ambiental comunal (mapeo de ruidos, olores, suelos degradados, microbasurales)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(260, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Implementar una política de puesta en valor del paisaje y el patrimonio natural de la comuna.', 'c', 'Proyecto', '2.6', 'Actualización Proyecto Vivero municipal', NULL, 80000, 1300000, 1300000.00, 'Medio Ambiente', NULL, 1380000, 'FNDR, Programa Mejoramiento Urbano y equipamiento comunal (PMU), SUBDERE, Programa Concursable de Espacios Públicos, MINVU', 'Dirección de Medio Ambiente y Ornato', NULL, 'SEREMI Vivienda Región Antofagasta, SEREMI Medio Ambiente Región Antofagasta', '-33.64268812380361', '-78.82658477992194', 'En espera', '1', NULL, NULL, '2.6.pdf', NULL, '4', 1, 'Actualización Proyecto Vivero municipal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Promover la tenencia responsable de mascotas.', 'c', 'Proyecto', '2.7', 'Crematorio de Mascotas', NULL, NULL, 90000, 90000.00, 'Medio Ambiente', NULL, 90000, 'Municipalidad de Antofagasta, FNDR', 'Dirección de Medio Ambiente y Ornato', NULL, 'SEREMI Salud, SEREMI Medio Ambiente ', '-33.64478871758586', '-78.83221387191975', 'Detenida por fuerza mayor', '1', NULL, NULL, '2.7.pdf', NULL, '8', 1, 'Crematorio de Mascotas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 2, 'Medio Ambiente', NULL, 'PLADECO', 'Promover una comunidad consciente y comprometida con el Medio Ambiente', 'Implementar una política de puesta en valor del paisaje y el patrimonio natural de la comuna.', 'a', 'Estudio', '2.8', 'Plan de remediación y recuperación del ex vertedero La Chimba y su entorno.', 0, 0, 200000, 200000.00, 'Medio Ambiente', NULL, 200000, '', 'Dirección de Medio Ambiente y Ornato', NULL, 'SEREMI Salud, SEREMI Medio Ambiente ', '-33.64495077008624', '-78.82656506934966', 'En espera', '1', NULL, '28-08-2023 13:47:01', '2.8.pdf', NULL, '202308281638', 1, '', '', '', NULL, '', '', '', '', NULL, 0.00, 0.00),
(310, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Potenciar el emprendimiento en recursos naturales, ambientales y culturales', 'b', 'Programa', '3.1', 'Centro municipal de emprendimiento colaborativo (cowork)', NULL, NULL, 129200, 129200.00, 'Desarrollo Productivo', NULL, 129200, 'Comité de Desarrollo Productivo, Municipalidad de Antofagasta', 'Departamento de Fomento Productivo y Empleo', NULL, 'SERCOTEC, CORFO, SENCE', '-33.63591418087361', '-78.83103513176131', 'Eliminada', '1', NULL, NULL, '3.1.pdf', NULL, '9', 1, 'Centro municipal de emprendimiento colaborativo (cowork)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Objetivo 1: Potenciar el emprendimiento en recursos naturales, ambientales y culturales, Objetivo 2: Promover la creación un polo de servicios turísticos gastronómicos y recreativos (ZOIT)', 'b', 'Programa', '3.2', 'Programa para el fomento del microemprendimiento de servicios turísticos', NULL, NULL, 106600, 106600.00, 'Desarrollo Productivo', NULL, 106600, 'Comité de Desarrollo Productivo, Municipalidad de Antofagasta', 'Departamento de Fomento Productivo y Empleo', NULL, 'SERCOTEC, CORFO, SERNATUR', '-33.6377264640156', '-78.82872250156699', 'Etapa en ejecución', '1', NULL, NULL, '3.2.pdf', NULL, '9', 1, 'Programa para el fomento del microemprendimiento de servicios turísticos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Generar capacidades de recursos humanos en tecnologías de la información', 'b', 'Programa', '3.3', 'Programa de capacitación y financiamiento en Tecnología de la Información', NULL, NULL, 157600, 157600.00, 'Desarrollo Productivo', NULL, 157600, 'Comité de Desarrollo Productivo', 'Departamento de Fomento Productivo y Empleo', NULL, 'SERCOTEC, CORFO', '-33.64257971925832', '-78.82615760342588', 'Etapa en ejecución', '1', NULL, NULL, '3.3.pdf', NULL, '9', 1, 'Programa de capacitación y financiamiento en Tecnología de la Información', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Creación de oportunidades para el emprendimiento y la empleabilidad con foco en la mujer', 'a', 'Estudio', '3.4', 'Desarrollo Sistema OMIL ON LINE', NULL, NULL, 80000, 80000.00, 'Desarrollo Productivo', NULL, 80000, 'Municipalidad de Antofagasta, Subsecretaría del Trabajo', 'OMIL', NULL, 'SENCE, Dirección del Trabajo', '-33.64464228539135', '-78.82972949151186', 'Postulada a financiamiento', '1', NULL, NULL, '3.4.pdf', NULL, '9', 1, 'Desarrollo Sistema OMIL ON LINE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Creación de oportunidades para el emprendimiento y la empleabilidad con foco en la mujer', 'a', 'Estudio', '3.5', 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', NULL, NULL, 50000, 50000.00, 'Desarrollo Productivo', NULL, 50000, 'Comité de Desarrollo Productivo, FNDR, SENCE', 'OMIL', NULL, 'SENCE, Dirección del Trabajo', '-33.644323472954135', '-78.8328263630795', 'En espera', '1', NULL, NULL, '3.5.pdf', NULL, '9', 1, 'Estudio de definición de perfiles de competencias para la capacitación e intermediación laboral', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(360, 3, 'Desarrollo Productivo', NULL, 'PLADECO', 'Generar oportunidades de desarrollo en actividades productivas emergentes y nuevos negocios ', 'Creación de oportunidades para el emprendimiento y la empleabilidad con foco en la mujer', 'a', 'Estudio', '3.6', 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', NULL, NULL, 120000, 120000.00, 'Desarrollo Productivo', NULL, 120000, 'Comité de Desarrollo Productivo, FNDR', 'Departamento de Fomento Productivo y Empleo', NULL, 'División Fomento e Industria GORE, SERCOTEC, CORFO, SENCE, Dirección del Trabajo, Subsecretaría de P', '-33.64435173670478', '-78.83550743599974', 'En espera', '1', NULL, NULL, '3.6.pdf', NULL, '9', 1, 'Estudio y plan para el comercio ambulante de la comuna de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(410, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una red eficiente de vialidad urbana con un sistema integrado y diversificado de transporte.', 'Dotar a la ciudad de una red vial estratégica longitudinal transversal y de circunvalación.', 'a', 'Estudio', '4.1', 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', NULL, NULL, 150360, 150360.00, 'Desarrollo urbano', NULL, 150360, 'Programa de pavimentos participativos', 'SECOPLAN', NULL, 'MINVU, SERVIU', '-33.640100380302854', '-78.8355614904446', 'En espera', '1', NULL, NULL, '4.1.pdf', NULL, '34', 1, 'Plan de pavimentación de calles en sectores ciudad de Antofagasta ', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(420, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una red eficiente de vialidad urbana con un sistema integrado y diversificado de transporte.', 'Dotar a la ciudad de una red vial estratégica longitudinal transversal y de circunvalación.', 'a', 'Estudio', '4.2', 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', NULL, NULL, 150360, 150360.00, 'Desarrollo urbano', NULL, 150360, 'FNDR, Programa de Mejoramiento Urbano (PMU) ', 'SECOPLAN', NULL, 'MINVU, SERVIU', '-33.63713568447499', '-78.83096198938696', 'En espera', '1', NULL, NULL, '4.2.pdf', NULL, '34', 1, 'Plan de alumbrado público diversos sectores ciudad de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(430, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Mejorar y modernizar la red existente de espacios públicos y áreas verdes con base en la seguridad, la eficiencia y la inclusión social.', 'a', 'Estudio', '4.3', 'Plan de modernización red de espacios públicos ciudad de Antofagasta', NULL, NULL, 123480, 123480.00, 'Desarrollo urbano', NULL, 123480, 'FNDR, Programa de espacios públicos y Quiero mi Barrio MINVU, Programa de Mejoramiento Urbano (PMU) ', 'SECOPLAN', NULL, 'Gobierno Regional, MINVU, SUBDERE, Ministerio de Transporte', '-33.637881051787076', '-78.827511299915', 'Detenida por fuerza mayor', '1', NULL, NULL, '4.3.pdf', NULL, '34', 1, 'Plan de modernización red de espacios públicos ciudad de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(440, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Crear un sistema de espacios públicos de borde cerro para su uso recreativo y activo a lo largo de la ciudad.', 'a', 'Estudio', '4.4', 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', NULL, NULL, 137520, 137520.00, 'Desarrollo urbano', NULL, 137520, 'FNDR, Programa de espacios públicos y Quiero mi Barrio MINVU, Programa de Mejoramiento Urbano (PMU) ', 'SECOPLAN', NULL, 'Gobierno Regional, MINVU', '-33.644748971239025', '-78.82946883213734', 'En espera', '1', NULL, NULL, '4.4.pdf', NULL, '34', 1, 'Plan maestro de espacios públicos de los Cerros de Antofagasta (paseos, plazas, miradores)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(450, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Mejorar y modernizar la red existente de espacios públicos y áreas verdes con base en la seguridad, la eficiencia y la inclusión social.', 'a', 'Estudio', '4.5', 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', NULL, NULL, 163800, 163800.00, 'Desarrollo urbano', NULL, 163800, 'FNDR, Programa de Pavimentación Participativa (MINVU) Programa de Mejoramiento Urbano (PMU) y Programa de Revitalización de Barrios e Infraestructura Patrimonial Emblemática de la Subsecretaría de Desarrollo Regional (SUBDERE).', 'SECOPLAN', NULL, 'Gobierno Regional, MINVU,-SERVIU, SUBDERE', '-33.64307165670707', '-78.8269503857612', 'En espera', '1', NULL, NULL, '4.5.pdf', NULL, '34', 1, 'Plan de mejoramiento y estandarización de aceras ciudad de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(460, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Priorizar la vida peatonal en el área central', 'a', 'Estudio', '4.6', 'Estudio de red peatonal en área central ciudad de Antofagasta', NULL, NULL, 178320, 178320.00, 'Desarrollo urbano', NULL, 178320, 'FNDR, Municipalidad de Antofagasta', 'SECOPLAN', NULL, 'SEREMI de Transportes y Telecomunicaciones, MINVU, Gobierno Regional', '-33.63905763280038', '-78.82732012165745', 'En espera', '1', NULL, NULL, '4.6.pdf', NULL, '34', 1, 'Estudio de red peatonal en área central ciudad de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(470, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Desarrollar núcleos activos distribuidos en el área urbana con intervenciones tácticas en áreas densas', 'a', 'Estudio', '4.7', 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', NULL, NULL, 147400, 147400.00, 'Desarrollo urbano', NULL, 147400, 'FNDR, Municipalidad de Antofagasta', 'SECOPLAN', NULL, 'Gobierno Regional, MOP (Dirección de Arquitectura), MINVU, SERVIU', '-33.64170883680935', '-78.83205485865194', 'En espera', '1', NULL, NULL, '4.7.pdf', NULL, '10', 1, 'Plan maestro nuevo centro cívico y social sector norte de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(480, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una red eficiente de vialidad urbana con un sistema integrado y diversificado de transporte.', 'Avanzar en la electromovilidad', 'a', 'Estudio', '4.8', 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', NULL, NULL, 324600, 324600.00, 'Desarrollo urbano', NULL, 324600, 'FNDR, sectorial (MOP).', 'SECOPLAN', NULL, 'SEREMI de Transportes y Telecomunicaciones, Secretaría de Planificación de Transporte SECTRA, Gobier', '-33.64002471984514', '-78.83324422052725', 'En espera', '1', NULL, NULL, '4.8.pdf', NULL, '34', 1, 'Estudio básico Análisis de alternativas de implementación de modelo de transporte multimodal con electromovilidad, ciudad de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(490, 4, 'Desarrollo urbano', NULL, 'PLADECO', 'Una ciudad equilibrada en la dotación de sus servicios y equipamiento, sustentada en las fortalezas de su paisaje e identidad de cerros y mar', 'Crear un espacio de borde costero de emprendimiento, desarrollo cultural y gastronómico.', 'a', 'Estudio', '4.9', 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', NULL, NULL, 190320, 190320.00, 'Desarrollo urbano', NULL, 190320, 'FNDR, Municipalidad de Antofagasta', 'SECOPLAN', NULL, 'Gobierno Regional, MINVU', '-33.64482021530009', '-78.83360961943849', 'En espera', '1', NULL, NULL, '4.9.pdf', NULL, '12', 1, 'Plan estratégico municipal de intervención en el borde costero de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(510, 5, 'Seguridad vecinal', NULL, 'PLADECO', 'Mejoramiento de la situación de inseguridad de los vecinos y que se sientan protegidos, así como valorar sus necesidades. ', 'Fortalecer la atención a las demandas de seguridad de los vecinos mediante comparecencia oportuna y coordinada', 'b', 'Programa', '5.1', 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', NULL, NULL, 2185000, 2185000.00, 'Seguridad vecinal', NULL, 2185000, 'Delegación presidencial, Subsecretaría Prevención del Delito, Municipalidad de Antofagasta', 'Seguridad ciudadana', NULL, 'Delegación presidencial, Subsecretaría Prevención del Delito', '-33.6411076482594', '-78.83350947266142', 'Postulada a financiamiento', '1', NULL, NULL, '5.1.pdf', NULL, '13', 1, 'Programa de vigilancia y patrullaje vecinal (aumento de vehículos, personal, equipamiento)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(520, 5, 'Seguridad vecinal', NULL, 'PLADECO', 'Mejoramiento de la situación de inseguridad de los vecinos y que se sientan protegidos, así como valorar sus necesidades. ', 'Reforzar la prevención situacional enfocado a disminuir la oportunidad del delito y reduciendo la percepción de la inseguridad', 'b', 'Programa', '5.2', 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', NULL, NULL, 85000, 85000.00, 'Seguridad vecinal', NULL, 85000, 'Delegación presidencial, Subsecretaría Prevención del Delito, Municipalidad de Antofagasta', 'Seguridad pública ', NULL, 'Delegación presidencial, Subsecretaría Prevención del Delito', '-33.64410037795261', '-78.83169590362611', 'Detenida por fuerza mayor', '1', NULL, NULL, '5.2.pdf', NULL, '13', 1, 'Programa de capacitación en seguridad a organizaciones sociales y vecinales', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(530, 5, 'Seguridad vecinal', NULL, 'PLADECO', 'Mejoramiento de la situación de inseguridad de los vecinos y que se sientan protegidos, así como valorar sus necesidades. ', 'Promover la seguridad integral de los barrios abordando las diversas situaciones de riesgo.', 'b', 'Programa', '5.3', 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', NULL, NULL, 119000, 119000.00, 'Seguridad vecinal', NULL, 119000, 'Delegación presidencial, Subsecretaría Prevención del Delito, Municipalidad de Antofagasta', 'Seguridad ciudadana', NULL, 'Delegación presidencial, Subsecretaría Prevención del Delito, Gobierno Regional', '-33.642526486699786', '-78.82742932039612', 'Formulada', '1', NULL, NULL, '5.3.pdf', NULL, '13', 1, 'Programa de implementación tecnológica para la prevención del delito (pórticos, videovigilancia y drones)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(540, 5, 'Seguridad vecinal', NULL, 'PLADECO', 'Mejoramiento de la situación de inseguridad de los vecinos y que se sientan protegidos, así como valorar sus necesidades. ', 'Fortalecer la atención a las demandas de seguridad de los vecinos mediante comparecencia oportuna y coordinada', 'a', 'Estudio', '5.4', 'Estudio diseño observatorio comunal del delito', NULL, NULL, 31400, 31400.00, 'Seguridad vecinal', NULL, 31400, 'FNDR, Delegación presidencial, Subsecretaría Prevención del Delito (SPD), Municipalidad de Antofagasta', 'Seguridad pública ', NULL, 'Delegación presidencial, Subsecretaría Prevención del Delito', '-33.642498120743994', '-78.83524584417566', 'En licitación', '1', NULL, NULL, '5.4.pdf', NULL, '13', 1, 'Estudio diseño observatorio comunal del delito', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(560, 5, 'Seguridad vecinal', NULL, 'PLADECO', 'Mejoramiento de la situación de inseguridad de los vecinos y que se sientan protegidos, así como valorar sus necesidades. ', 'Fortalecer la participación ciudadana con equidad e igualdad de acceso a la seguridad en los diversos barrios de la ciudad.', 'c', 'Proyecto', '5.6', 'Construcción Centros comunitarios de seguridad en los barrios', NULL, NULL, 720000, 720000.00, 'Seguridad vecinal', NULL, 720000, 'FNDR, Delegación presidencial, Subsecretaría Prevención del Delito (SPD), Municipalidad de Antofagasta', 'DOM', NULL, 'Delegación presidencial, Subsecretaría Prevención del Delito', '-33.64469227139882', '-78.83192733671856', 'En espera', '1', NULL, NULL, '5.6.pdf', NULL, '32', 1, 'Construcción Centros comunitarios de seguridad en los barrios', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(610, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Fortalecer la inclusión, la convivencia escolar y la salud mental en la comunidad educativa de los establecimientos educacionales públicos', 'b', 'Programa', '6.1', 'Programa de Convivencia Escolar en Establecimientos Educacionales de Antofagasta (Habilidades para la Vida - HPV)', NULL, NULL, 250000, 250000.00, 'Educación', NULL, 250000, 'Junta Nacional de Auxilio Escolar y Becas (JUNAEB – Programa HPV), Ministerio de Educación', 'Corporación Municipal de Desarrollo Social', NULL, 'Ministerio de Educación, SEREMI Educación de Antofagasta, Dirección Provincial de Educación de Antof', '-33.636033444671384', '-78.8311436627193', 'Eliminada', '1', NULL, NULL, '6.1.pdf', NULL, '14', 1, 'Programa de Convivencia Escolar en Establecimientos Educacionales de Antofagasta (Habilidades para la Vida - HPV)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(620, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Promover y mejorar el rendimiento escolar de los y las estudiantes', 'b', 'Programa', '6.2', 'Programa de Becas de Normalización de Estudios de Profesionales que ejercen Docencia', NULL, NULL, 203000, 203000.00, 'Educación', NULL, 203000, 'Ministerio de Educación, Municipalidad de Antofagasta', 'Corporación Municipal de Desarrollo Social', NULL, 'GORE Antofagasta, Ministerio de Educación, SEREMI de Educación, Universidad de Antofagasta', '-33.635909342721256', '-78.83583028613523', 'Sustituida', '1', NULL, NULL, '6.2.pdf', NULL, '14', 1, 'Programa de Becas de Normalización de Estudios de Profesionales que ejercen Docencia', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(630, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Aportar en la creación de una cultura ambiental escolar', 'b', 'Programa', '6.3', 'Programa de Certificación Ambiental de los Establecimientos Educacionales Municipales (Sello sustentable)', NULL, NULL, 223000, 223000.00, 'Educación', NULL, 223000, 'FNDR y Fondos Regionales 8% FNDR Medio Ambiente, Fondo de Protección Ambiental del Ministerio del Medio Ambiente', 'Corporación Municipal de Desarrollo Social', NULL, 'GORE Antofagasta, Ministerio del Medio Ambiente', '-33.64413680760921', '-78.83298312695678', 'En espera', '1', NULL, NULL, '6.3.pdf', NULL, '14', 1, 'Programa de Certificación Ambiental de los Establecimientos Educacionales Municipales (Sello sustentable)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(640, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Mejorar y mantener adecuadamente las condiciones de infraestructura y equipamiento en los establecimientos educacionales públicos', 'a', 'Estudio', '6.4', 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', NULL, NULL, 200000, 200000.00, 'Educación', NULL, 200000, 'FNDR, Fondo de Apoyo a la Educación Pública (FAEP) Ministerio de Educación; ', 'Corporación Municipal de Desarrollo Social', NULL, 'Ministerio de Educación, GORE Antofagasta, SEREMI Educación de Antofagasta, Dirección Provincial de ', '-33.64167957281766', '-78.8279404446378', 'En espera', '1', NULL, NULL, '6.4.pdf', NULL, '14', 1, 'Estudio Plan Maestro de Infraestructura Escolar (cartera de inversión)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(650, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Promover y mejorar el rendimiento escolar de los y las estudiantes', 'c', 'Proyecto', '6.5', 'Centro Tecnológico Digital para la Educación y la Innovación', NULL, 15000, 220000, 220000.00, 'Educación', NULL, 235000, 'FNDR', 'SECOPLAN, Dirección de Tecnología de la Información (DTI) Municipal', NULL, 'GORE Antofagasta, Ministerio de Educación', '-33.63853929595364', '-78.82731035656144', 'Postulada a financiamiento', '1', NULL, NULL, '6.5.pdf', NULL, '36', 1, 'Centro Tecnológico Digital para la Educación y la Innovación', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(660, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Mejorar y mantener adecuadamente las condiciones de infraestructura y equipamiento en los establecimientos educacionales públicos', 'c', 'Proyecto', '6.6', 'Construcción de Liceo Sector La Chimba', NULL, 10000, 10848000, 10848000.00, 'Educación', NULL, 10858000, 'FNDR, Ministerio de Educación.', 'Dirección de Obras CMDS, Dirección de Educación CMDS y Depto. Planificación CMDS', NULL, 'GORE Antofagasta, Ministerio de Educación, SEREMI Educación de Antofagasta', '-33.64262408168426', '-78.8317814285421', 'Formulada', '1', NULL, NULL, '6.6.pdf', NULL, '15', 1, 'Construcción de Liceo Sector La Chimba', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(670, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Mejorar y mantener adecuadamente las condiciones de infraestructura y equipamiento en los establecimientos educacionales públicos', 'c', 'Proyecto', '6.7', 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', 50000, NULL, 845000, 845000.00, 'Educación', NULL, 895000, 'FNDR, Ministerio de Educación, Municipalidad de Antofagasta', 'Dirección de Obras CMDS; Departamento de Planificación CMDS', NULL, 'GORE Antofagasta, Ministerio de Educación', '-33.63590352116722', '-78.833314354216', 'Etapa en ejecución', '1', NULL, NULL, '6.7.pdf', NULL, '14', 1, 'Programa de conservación, normalización y mejoramiento de Servicios Higiénicos (SSHH) de establecimientos educativos', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(680, 6, 'Educación', NULL, 'PLADECO', 'Mejora del rendimiento escolar, la inclusión, la convivencia escolar y la salud mental', 'Mejorar y mantener adecuadamente las condiciones de infraestructura y equipamiento en los establecimientos educacionales públicos', 'c', 'Proyecto', '6.8', 'Construcción escuela de párvulos sector norte', 15000, NULL, 1024681, 1024681.00, 'Educación', NULL, 1039680, 'FNDR, Ministerio de Educación, Subsecretaría de Educación Parvularia, Municipalidad de Antofagasta', 'Dirección de Obras CMDS; Departamento de Planificación CMDS', NULL, 'GORE Antofagasta, Ministerio de Educación, JUNJI', '-33.643813786706815', '-78.83299679506334', 'Eliminada', '1', NULL, NULL, '6.8.pdf', NULL, '10', 1, 'Construcción escuela de párvulos sector norte', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(710, 7, 'Deporte y Recreación', NULL, 'PLADECO', 'Promoción de la actividad física y deportiva en todo el ciclo de vida', 'Promover la actividad física en espacio público y recintos cerrados durante todo el año y para todas las personas.', 'a', 'Estudio', '7.1', 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', NULL, NULL, 73000, 73000.00, 'Deporte y Recreación', NULL, 73000, 'FNDR, Municipalidad de Antofagasta, Ministerio del Deporte', 'Corporación Municipal de Deportes', NULL, 'Ministerio del Deporte, Instituto Nacional de Deporte (IND), Gobierno Regional (GORE) de Antofagasta', '-33.63903535322849', '-78.83191740971682', 'Eliminada', '1', NULL, NULL, '7.1.pdf', NULL, '16', 1, 'Estudio Plan Comunal de Deportes y Recreación de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(720, 7, 'Deporte y Recreación', NULL, 'PLADECO', 'Promoción de la actividad física y deportiva en todo el ciclo de vida', 'Promover la actividad física en espacio público y recintos cerrados durante todo el año y para todas las personas.', 'a', 'Estudio', '7.2', 'Estudio Salud de Usuarios de Instalaciones Deportivas', NULL, NULL, 29600, 29600.00, 'Deporte y Recreación', NULL, 29600, 'FNDR, Municipalidad de Antofagasta, Ministerio del Deporte', 'Corporación Municipal de Deportes', NULL, 'Dirección de Salud de la Corporación Municipal de Desarrollo Social (CMDS) de Antofagasta, IND, GORE', '-33.643630492552155', '-78.82835294828004', 'Postulada a financiamiento', '1', NULL, NULL, '7.2.pdf', NULL, '16', 1, 'Estudio Salud de Usuarios de Instalaciones Deportivas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(730, 7, 'Deporte y Recreación', NULL, 'PLADECO', 'Promoción de la actividad física y deportiva en todo el ciclo de vida', 'Gestionar de manera eficiente los recintos deportivos de la comuna, apoyándose en el uso de tecnologías de la información y las comunicaciones (TICs).', 'a', 'Estudio', '7.3', 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', NULL, NULL, 65000, 65000.00, 'Deporte y Recreación', NULL, 65000, 'Municipalidad de Antofagasta', 'Corporación Municipal de Deportes', NULL, 'Dirección de Salud de la Corporación Municipal de Desarrollo Social (CMDS) de Antofagasta, IND, GORE', '-33.63732250607915', '-78.83089093159245', 'En espera', '1', NULL, NULL, '7.3.pdf', NULL, '16', 1, 'Estudio Modelo de Gestión para la Administración de Recintos Deportivos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(740, 7, 'Deporte y Recreación', NULL, 'PLADECO', 'Promoción de la actividad física y deportiva en todo el ciclo de vida', 'Disponer de infraestructura adecuada y segura para la práctica deportiva en toda la comuna.', 'c', 'Proyecto', '7.4', 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', NULL, 25000, 5000000, 5000000.00, 'Deporte y Recreación', NULL, 5025000, 'FNDR GORE Antofagasta, Ministerio del Deporte, Instituto Nacional del Deporte (IND)', 'SECOPLAN', NULL, 'Ministerio del Deporte, Instituto Nacional del Deporte (IND), GORE Antofagasta.', '-33.64061617538756', '-78.83433654350927', 'Detenida por fuerza mayor', '1', NULL, NULL, '7.4.pdf', NULL, '10', 1, 'Proyecto Construcción Centro Deportivo de la Zona Norte de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(810, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Promover el cuidado permanente de la salud en todo el ciclo de vida de las personas', 'b', 'Programa', '8.1', 'Programa de promoción de exámenes médicos preventivos', NULL, NULL, 250000, 250000.00, 'Salud', NULL, 250000, 'FNDR', 'Corporación Municipal de Desarrollo Social', NULL, 'Ministerio de Salud, Gobierno Regional', '-33.638593178482125', '-78.82711119186025', 'Sustituida', '1', NULL, NULL, '8.1.pdf', NULL, '35', 1, 'Programa de promoción de exámenes médicos preventivos', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(820, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Ampliar la capacidad de atención de la red y la confortabilidad del usuarios internos y externos', 'a', 'Estudio', '8.2', 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', NULL, NULL, 120000, 120000.00, 'Salud', NULL, 120000, 'Municipalidad de Antofagasta, FNDR', 'Corporación Municipal de Desarrollo Social', NULL, 'Ministerio de Salud, Gobierno Regional', '-33.644945355493356', '-78.828184194181', 'En espera', '1', NULL, NULL, '8.2.pdf', NULL, '35', 1, 'Estudio de red de salud comunal y formulación de cartera de proyectos de inversión', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(830, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Ampliar la capacidad de atención de la red y la confortabilidad del usuarios internos y externos', 'c', 'Proyecto', '8.3', 'Construcción nuevo CESFAM comuna de Antofagasta', 10000, NULL, 6000000, 6000000.00, 'Salud', NULL, 6010000, 'FNDR, Ministerio de Salud', 'Dirección de Salud, Dirección de Planificación y Obras de la CMDS Antofagasta', NULL, 'Ministerio de Salud, Gobierno Regional de Antofagasta, Ministerio de Desarrollo Social y Familia.', '-33.64288692564041', '-78.83330717566702', 'En espera', '1', NULL, NULL, '8.3.pdf', NULL, '19', 1, 'Construcción nuevo CESFAM comuna de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(840, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Ampliar la capacidad de atención de la red y la confortabilidad del usuarios internos y externos', 'c', 'Proyecto', '8.4', 'Reposición CESFAM Centro sur de Antofagasta', 10000, NULL, 6000000, 6000000.00, 'Salud', NULL, 6010000, 'FNDR, Ministerio de Salud', 'Dirección de Salud, Dirección de Planificación y Obras de la CMDS Antofagasta', NULL, 'Ministerio de Salud, Gobierno Regional de Antofagasta, Ministerio de Desarrollo Social y Familia.', '-33.642775652660674', '-78.83400923592843', 'Detenida por fuerza mayor', '1', NULL, NULL, '8.4.pdf', NULL, '19', 1, 'Reposición CESFAM Centro sur de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(850, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Ampliar la capacidad de atención de la red y la confortabilidad del usuarios internos y externos // Brindar una atención de calidad y oportuna al usuario.', 'c', 'Proyecto', '8.5', 'Construcción de Laboratorio comunal de Antofagasta', NULL, 25000, 850000, 850000.00, 'Salud', NULL, 875000, 'FNDR, Ministerio de Salud', 'Dirección de Salud, Dirección de Planificación y Obras de la CMDS Antofagasta', NULL, 'Ministerio de Salud, Gobierno Regional de Antofagasta, Ministerio de Desarrollo Social y Familia.', '-33.635767650975225', '-78.83031483455764', 'Detenida por fuerza mayor', '1', NULL, NULL, '8.5.pdf', NULL, '19', 1, 'Construcción de Laboratorio comunal de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(860, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', ' Implementar sistema de mejora continua en procesos de atención apoyado por tecnologías adecuadas.', 'c', 'Proyecto', '8.6', 'Proyecto mejoramiento de la infraestructura de la red digital de los centros de salud', NULL, NULL, 746000, 746000.00, 'Salud', NULL, 746000, 'FNDR', 'Dirección de Salud, Dirección de Planificación y Obras de la CMDS Antofagasta', NULL, 'Ministerio de Transporte y telecomunicaciones, Gobierno Regional de Antofagasta, Ministerio de Desar', '-33.64336015571401', '-78.82755671923549', 'Sustituida', '1', NULL, NULL, '8.6.pdf', NULL, '19', 1, 'Proyecto mejoramiento de la infraestructura de la red digital de los centros de salud', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(870, 8, 'Salud', NULL, 'PLADECO', 'Mejor acceso a la salud municipal', 'Ampliar la capacidad de atención de la red y la confortabilidad del usuarios internos y externos', 'c', 'Proyecto', '8.7', 'Modernización Cementerio General de Antofagasta', 100000, 60000, 1000000, 1000000.00, 'Salud', NULL, 1160000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Servicios Traspasados', NULL, 'Ministerio de Salud, Gobierno Regional de Antofagasta', '-33.64253091735995', '-78.82921339007883', 'Formulada', '1', NULL, '20-09-2023 11:11:12', '8.7.pdf', NULL, '26', 1, 'Modernización Cementerio General de Antofagasta', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(910, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Fortalecer las capacidades de los grupos prioritarios para su inserción social', 'Modernizar el acceso a los servicios sociales.', 'b', 'Programa', '9.1', 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', NULL, NULL, 100000, 100000.00, 'Social – Comunitario', NULL, 100000, 'FNDR, Municipalidad de Antofagasta', 'DIDECO', NULL, 'Ministerio de Desarrollo Social y Familia', '-33.64232537734174', '-78.83092054446524', 'Detenida por fuerza mayor', '1', NULL, NULL, '9.1.pdf', NULL, '21', 1, 'Programa de modernización de los sistemas de automatización en la atención de los servicios sociales y municipales', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(920, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Poner en valor el capital social como factor de desarrollo social.', 'Impulsar la participación de la comunidad en la implementación de iniciativas de desarrollo comunal.', 'b', 'Programa', '9.2', 'Programa Fondo DIDECO para las organizaciones sociales', NULL, NULL, 180000, 180000.00, 'Social – Comunitario', NULL, 180000, 'Municipalidad de Antofagasta', 'DIDECO', NULL, 'División de Organizaciones Sociales del Ministerio Secretaría General de Gobierno.', '-33.63587811382516', '-78.82835868224043', 'En espera', '1', NULL, NULL, '9.2.pdf', NULL, '21', 1, 'Programa Fondo DIDECO para las organizaciones sociales', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(930, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Poner en valor el capital social como factor de desarrollo social.', ' Fortalecer la presencia de los líderes sociales en la gobernanza e instancias de decisión comunal.', 'b', 'Programa', '9.3', 'Programa de  capacitación a dirigentes sociales, roles y funciones de un líder.', NULL, NULL, 38000, 38000.00, 'Social – Comunitario', NULL, 38000, 'FNDR, División de Organizaciones Sociales DOS', 'DIDECO', NULL, 'División de Organizaciones Sociales del Ministerio Secretaría General de Gobierno.', '-33.63583084506959', '-78.82983742567218', 'En espera', '1', NULL, NULL, '9.3.pdf', NULL, '21', 1, 'Programa de  capacitación a dirigentes sociales, roles y funciones de un líder.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(940, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Fortalecer las capacidades de los grupos prioritarios para su inserción social', 'Modernizar el acceso a los servicios sociales.', 'a', 'Estudio', '9.4', 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', NULL, NULL, 42000, 42000.00, 'Social – Comunitario', NULL, 42000, 'FNDR, Municipalidad de Antofagasta', 'DIDECO', NULL, 'Ministerio de Desarrollo Social y Familia', '-33.63738893654193', '-78.83453758908944', 'En espera', '1', NULL, NULL, '9.4.pdf', NULL, '21', 1, 'Estudio evaluativo de resultados de las ayudas sociales en la población más vulnerables de la comuna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(950, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Poner en valor el capital social como factor de desarrollo social.', 'Impulsar la participación de la comunidad en la implementación de iniciativas de desarrollo comunal.', 'a', 'Estudio', '9.5', 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', NULL, NULL, 34000, 34000.00, 'Social – Comunitario', NULL, 34000, 'FNDR, Municipalidad de Antofagasta', 'DIDECO', NULL, 'Subsecretaría de Desarrollo Regional y Administrativo (SUBDERE).', '-33.64375922961554', '-78.82628319707429', 'Eliminada', '1', NULL, NULL, '9.5.pdf', NULL, '21', 1, 'Estudio Plan de inversión de actualización, conservación, mejoramiento y construcción de sedes comunitarias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(960, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Fortalecer las capacidades de los grupos prioritarios para su inserción social', 'Modernizar el acceso a los servicios sociales.', 'c', 'Proyecto', '9.6', 'Oficinas de atención municipal en sectores más alejados de la comuna', 11500, NULL, 300000, 300000.00, 'Social – Comunitario', NULL, 311500, 'Municipalidad de Antofagasta', 'DIDECO', NULL, 'Ministerio de Desarrollo Social y Familia, Gobierno Regional de Antofagasta', '-33.64159040035552', '-78.82890177109724', 'Postulada a financiamiento', '1', NULL, NULL, '9.6.pdf', NULL, '21', 1, 'Oficinas de atención municipal en sectores más alejados de la comuna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(970, 9, 'Social – Comunitario', NULL, 'PLADECO', 'Fortalecer las capacidades de los grupos prioritarios para su inserción social', 'Promover la convivencia social y cultural, respetuosa de la multiculturalidad, en el espacio público con intervención urbana en la ciudad de Antofagasta', 'b', 'Programa', '9.7', 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', NULL, NULL, 63500, 63500.00, 'Social – Comunitario', NULL, 63500, 'Municipalidad de Antofagasta', 'DIDECO', NULL, 'Ministerio de las Culturas, las Artes y el Patrimonio', '-33.63534219190316', '-78.82859218725106', 'En licitación', '1', NULL, NULL, '9.7.pdf', NULL, '21', 1, 'Programa de recuperación de espacios públicos con intervenciones culturales y sociales. ', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `proyectos` (`id`, `cod_area`, `area`, `subarea`, `origen`, `lineamiento`, `objetivos`, `cod_tipo`, `tipo`, `cod_iniciativa`, `nombre`, `p_diseno`, `p_prefactibilidad`, `p_ejecucion`, `total`, `area_gestion`, `plazo`, `presupuesto`, `financiamiento`, `unidad`, `e`, `entidades_relacionadas`, `lat`, `lng`, `estado`, `cod_estado`, `pin`, `proyectos_last_update`, `pdf`, `ano_inicio`, `ubicacion`, `status`, `descripcion`, `politicas`, `problema`, `area_influencia`, `deficit`, `solucion`, `beneficios`, `orientaciones`, `etapa`, `avance_etapas`, `avance_financiero`) VALUES
(1010, 10, 'Institucional', NULL, 'PLADECO', 'Rediseño continuo de servicios municipales cercanos, oportunos, inclusivos y sustentables', 'Mejorar la satisfacción ciudadana respecto de los servicios Municipales. // Ampliar la capacidad de atención del Municipio // Mejorar la gestión de usuarios y usuarias de la Municipalidad en encuesta PMG, SUBDERE', 'b', 'Programa', '10.1', 'Programa de fortalecimiento y mejora de atención al usuario', NULL, NULL, 55000, 55000.00, 'Institucional', NULL, 55000, 'SUBDERE, Municipalidad de Antofagasta', 'Dirección de Gestión de Personas', NULL, NULL, '-33.64427266247572', '-78.8317732840601', 'En licitación', '1', NULL, NULL, '10.1.pdf', NULL, '29', 1, 'Programa de fortalecimiento y mejora de atención al usuario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1020, 10, 'Institucional', NULL, 'PLADECO', 'Fortalecimiento del liderazgo institucional local', ' Mejorar el nivel de acuerdo encuesta hogares sobre que la muestra liderazgo en los temas que afectan a la comuna // Mejorar la percepción interna de Liderazgo Municipal.', 'b', 'Programa', '10.2', 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', NULL, NULL, 74000, 74000.00, 'Institucional', NULL, 74000, 'Municipalidad de Antofagasta', 'Dirección de Gestión de Personas', NULL, NULL, '-33.64365573215296', '-78.83282024629877', 'En espera', '1', NULL, NULL, '10.2.pdf', NULL, '29', 1, 'Programa de liderazgo para Jefaturas con foco en la cultura y valores institucionales y el proceso de modernización', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1030, 10, 'Institucional', NULL, 'PLADECO', 'Modernización institucional con fortalecimiento en los nuevos valores culturales', 'Implementación de la Ley de Transformación Digital', 'b', 'Programa', '10.3', 'Programa de formación continua de profesionales del área de TI', NULL, NULL, 30000, 30000.00, 'Institucional', NULL, 30000, 'Municipalidad de Antofagasta', 'Dirección de Gestión de Personas', NULL, NULL, '-33.63658689393376', '-78.83188926204456', 'En licitación', '1', NULL, NULL, '10.3.pdf', NULL, '29', 1, 'Programa de formación continua de profesionales del área de TI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1040, 10, 'Institucional', NULL, 'PLADECO', 'Modernización institucional con fortalecimiento en los nuevos valores culturales', 'Implementación de la Ley de Transformación Digital // Avanzar en la instalación de los nuevos Valores culturales de la IMA // Aumentar la eficiencia operacional ', 'b', 'Programa', '10.4', 'Programa de instalación de los valores culturales de la IMA', NULL, NULL, 150000, 150000.00, 'Institucional', NULL, 150000, 'SUBDERE, Municipalidad de Antofagasta', 'Dirección de Gestión de Personas', NULL, NULL, '-33.64142547517759', '-78.83047505985736', 'En espera', '1', NULL, NULL, '10.4.pdf', NULL, '29', 1, 'Programa de instalación de los valores culturales de la IMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1050, 10, 'Institucional', NULL, 'PLADECO', 'Modernización institucional con fortalecimiento en los nuevos valores culturales', 'Implementación de la Ley de Transformación Digital // Avanzar en la instalación de los nuevos Valores culturales de la IMA // Aumentar la eficiencia operacional ', 'b', 'Programa', '10.5', 'Laboratorio de innovación y proyectos internos en gestión municipal', NULL, NULL, 30000, 30000.00, 'Institucional', NULL, 30000, 'CORFO, Municipalidad de Antofagasta', 'Secretaría municipal', NULL, NULL, '-33.637393942586556', '-78.82835813544257', 'Etapa en ejecución', '1', NULL, NULL, '10.5.pdf', NULL, '30', 1, 'Laboratorio de innovación y proyectos internos en gestión municipal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1110, 11, 'Turismo', NULL, 'PLADETUR', 'Desarrollo de productos.', NULL, 'c', 'Proyecto', '11.1', 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', 40000, NULL, 1000000, 1000000.00, 'Turismo', NULL, 1040000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Turismo', NULL, 'SERNATUR, Gobierno Regional', '-33.64273846200219', '-78.83491390533972', 'Sustituida', '1', NULL, NULL, '11.1.pdf', NULL, '24', 1, 'Mejoramiento de accesibilidad y estado de los atractivos de jerarquía 1 y 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1120, 11, 'Turismo', NULL, 'PLADETUR', 'Desarrollo de productos.', NULL, 'a', 'Estudio', '11.2', 'Diseño y puesta en valor de circuitos turísticos nocturno en contexto con el astroturismo', NULL, NULL, 40000, 40000.00, 'Turismo', NULL, 40000, 'FNDR', 'Dirección de Turismo', NULL, 'SERNATUR', '-33.64267107487576', '-78.8315427748394', 'Etapa en ejecución', '1', NULL, NULL, '11.2.pdf', NULL, '24', 1, 'Diseño y puesta en valor de circuitos turísticos nocturno en contexto con el astroturismo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1130, 11, 'Turismo', NULL, 'PLADETUR', 'Desarrollo de productos.', NULL, 'a', 'Estudio', '11.3', 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', NULL, NULL, 120000, 120000.00, 'Turismo', NULL, 120000, 'FNDR', 'Dirección de Turismo', NULL, 'SERNATUR, Gobierno Regional, Ministerio del Deporte, Instituto Nacional del Deporte', '-33.63935332766406', '-78.82987435576955', 'En espera', '1', NULL, NULL, '11.3.pdf', NULL, '24', 1, 'Plan de fortalecimiento del turismo Deportivo y de Aventura en la comuna de Antofagasta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1140, 11, 'Turismo', NULL, 'PLADETUR', 'Desarrollo de productos.', NULL, 'b', 'Programa', '11.4', 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', NULL, NULL, 300000, 300000.00, 'Turismo', NULL, 300000, 'FNDR', 'Dirección de Turismo', NULL, 'SERNATUR', '-33.643584370991164', '-78.83512583343042', 'En espera', '1', NULL, NULL, '11.4.pdf', NULL, '24', 1, 'Implementación de circuitos turísticos en buses por zonas urbanas de la comuna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1150, 11, 'Turismo', NULL, 'PLADETUR', 'Promoción e Inteligencia Turística', NULL, 'a', 'Estudio', '11.5', 'Observatorio Turístico comunal', NULL, NULL, 30000, 30000.00, 'Turismo', NULL, 30000, 'Municipalidad de Antofagasta', 'Dirección de Turismo', NULL, 'SERNATUR, Gobierno Regional', '-33.63823093161854', '-78.82662269535531', 'Eliminada', '1', NULL, NULL, '11.5.pdf', NULL, '24', 1, 'Observatorio Turístico comunal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1160, 11, 'Turismo', NULL, 'PLADETUR', 'Promoción e Inteligencia Turística', NULL, 'c', 'Proyecto', '11.6', 'Oficina de Información turística como centro de atención al turista', NULL, NULL, 120000, 120000.00, 'Turismo', NULL, 120000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Turismo', NULL, 'SERNATUR, Gobierno Regional', '-33.638920190670845', '-78.83172531217129', 'En espera', '1', NULL, NULL, '11.6.pdf', NULL, '24', 1, 'Oficina de Información turística como centro de atención al turista', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1170, 11, 'Turismo', NULL, 'PLADETUR', 'Desarrollo de productos.', '', 'c', 'Proyecto', '11.7', 'Implementación de infraestructura habilitante en playas de administración Municipal', 30000, NULL, 400000, 400000.00, 'Turismo', NULL, 430000, 'FNDR, Municipalidad de Antofagasta', 'Dirección de Turismo', NULL, 'SERNATUR, Gobierno Regional', '-33.639520170883024', '-78.82787578537176', 'En espera', '1', NULL, NULL, '11.7.pdf', NULL, '24', 1, 'Implementación de infraestructura habilitante en playas de administración Municipal', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1176, 6, 'Educación', NULL, NULL, '', '', 'b', 'Programa', NULL, 'Proyecto de prueba', 0, 0, 0, 0.00, NULL, NULL, NULL, '', '', NULL, '', '-33.64066195509605', '-78.83476101423857', 'Postulada a financiamiento', NULL, NULL, '04-09-2024 19:33:49', '-20240904183752-INV271109965.pdf', NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
  `id` int NOT NULL,
  `visitas` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `visitas`) VALUES
(1, 367);

-- --------------------------------------------------------

--
-- Table structure for table `subareas`
--

DROP TABLE IF EXISTS `subareas`;
CREATE TABLE `subareas` (
  `id` int NOT NULL,
  `subarea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subareas`
--

INSERT INTO `subareas` (`id`, `subarea`) VALUES
(13, NULL),
(14, 'Aseo y ornato'),
(15, 'Cambio climático'),
(16, 'Ciclovía'),
(17, 'Deportes'),
(18, 'Digitalización'),
(19, 'Economía circular'),
(20, 'Edificación pública'),
(21, 'Educación ambiental'),
(22, 'Educación en seguridad'),
(23, 'Educación patrimonio cultural'),
(24, 'Educación TP'),
(25, 'Electromovilidad'),
(26, 'Empleabilidad'),
(27, 'Equipamiento de seguridad'),
(28, 'Equipamiento seguridad'),
(29, 'Equipamiento seguridad\r\n'),
(30, 'Espacio público'),
(31, 'Fiscalización ambiental'),
(32, 'Formación, capacitación y conciencia'),
(33, 'Género'),
(34, 'Gestión de residuos'),
(35, 'Gestión del patrimonio'),
(36, 'Gestión urbana'),
(37, 'Gestión y sostenibilidad turística'),
(38, 'Inclusión'),
(39, 'Infraestructura conectividad\r\n'),
(40, 'Infraestructura cultural'),
(41, 'Infraestructura de seguridad'),
(42, 'Infraestructura educacional'),
(43, 'Infraestructura sanitaria'),
(44, 'Infraestructura sanitaria\r\n'),
(45, 'Infraestructura social'),
(46, 'Infraestructura tecnológica'),
(47, 'Infraestructura urbana'),
(48, 'Inmigración'),
(49, 'Inversión patrimonio inmueble'),
(50, 'Multiculturalidad'),
(51, 'Organizaciones sociales'),
(52, 'Participación cultural'),
(53, 'Patrimonio ambiental'),
(54, 'Pesca Artesanal'),
(55, 'Planificación educativa'),
(56, 'Planificación territorial'),
(57, 'Prevención'),
(58, 'Proyectos turísticos clave y mejora de servicios'),
(59, 'Recursos Humanos'),
(60, 'RRHH'),
(61, 'Salud'),
(62, 'Salud\r\n'),
(63, 'Seguridad pública'),
(64, 'Seguridad vial'),
(65, 'Turismo'),
(66, 'Urbanización'),
(67, 'Vivienda'),
(68, 'Vivienda '),
(69, 'Vivienda, Espacio público'),
(70, 'Zonas de desarrollo y atracción de inversiones');

-- --------------------------------------------------------

--
-- Table structure for table `tabla`
--

DROP TABLE IF EXISTS `tabla`;
CREATE TABLE `tabla` (
  `id` int NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `e1l` varchar(100) DEFAULT NULL,
  `e1v` decimal(10,2) DEFAULT '0.00',
  `e1p` decimal(10,2) DEFAULT '0.00',
  `e2l` varchar(100) DEFAULT NULL,
  `e2v` decimal(10,2) DEFAULT '0.00',
  `e2p` decimal(10,2) DEFAULT '0.00',
  `e3l` varchar(100) DEFAULT NULL,
  `e3v` decimal(10,2) DEFAULT '0.00',
  `e3p` decimal(10,2) DEFAULT '0.00',
  `e4l` varchar(100) DEFAULT NULL,
  `e4v` decimal(10,2) DEFAULT '0.00',
  `e4p` decimal(10,2) DEFAULT '0.00',
  `e5l` varchar(100) DEFAULT NULL,
  `e5v` decimal(10,2) DEFAULT '0.00',
  `e5p` decimal(10,2) DEFAULT '0.00',
  `e6l` varchar(100) DEFAULT NULL,
  `e6v` decimal(10,2) DEFAULT '0.00',
  `e6p` decimal(10,2) DEFAULT '0.00',
  `tt` decimal(10,2) DEFAULT '0.00',
  `tp` decimal(10,2) DEFAULT '0.00',
  `status` int DEFAULT '0',
  `e7v` decimal(10,2) DEFAULT NULL,
  `e7l` varchar(100) DEFAULT NULL,
  `e7p` decimal(10,2) DEFAULT NULL,
  `e8v` decimal(10,2) DEFAULT NULL,
  `e8p` decimal(10,2) DEFAULT NULL,
  `e8l` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tabla`
--

INSERT INTO `tabla` (`id`, `area`, `tipo`, `e1l`, `e1v`, `e1p`, `e2l`, `e2v`, `e2p`, `e3l`, `e3v`, `e3p`, `e4l`, `e4v`, `e4p`, `e5l`, `e5v`, `e5p`, `e6l`, `e6v`, `e6p`, `tt`, `tp`, `status`, `e7v`, `e7l`, `e7p`, `e8v`, `e8p`, `e8l`) VALUES
(5440, 'Cultura y Patrimonio', 'Estudio', 'En espera', 3.00, 75.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 4.00, 75.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5441, 'Cultura y Patrimonio', 'Programa', 'En espera', 1.00, 50.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 2.00, 50.00, 0, 1.00, 'Sustituida', 0.00, NULL, 0.00, NULL),
(5442, 'Cultura y Patrimonio', 'Proyecto', 'En espera', 1.00, 50.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 2.00, 50.00, 0, 1.00, 'Sustituida', 0.00, NULL, 0.00, NULL),
(5443, 'Deporte y Recreación', 'Estudio', 'En espera', 1.00, 33.33, NULL, 0.00, 0.00, 'Postulada a financiamiento', 1.00, 33.33, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 33.33, 3.00, 100.00, 0, NULL, NULL, 33.33, 1.00, 33.33, 'Eliminada'),
(5444, 'Deporte y Recreación', 'Proyecto', NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 1.00, 0.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5445, 'Desarrollo Productivo', 'Estudio', 'En espera', 2.00, 66.67, NULL, 0.00, 0.00, 'Postulada a financiamiento', 1.00, 33.33, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 3.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5446, 'Desarrollo Productivo', 'Programa', NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Etapa en ejecución', 2.00, 66.67, NULL, 0.00, 33.33, 3.00, 100.00, 0, NULL, NULL, 33.33, 1.00, 33.33, 'Eliminada'),
(5447, 'Desarrollo urbano', 'Estudio', 'En espera', 8.00, 88.89, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 9.00, 88.89, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5448, 'Educación', 'Estudio', 'En espera', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5449, 'Educación', 'Programa', 'En espera', 1.00, 33.33, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 33.33, 3.00, 66.67, 0, 1.00, 'Sustituida', 33.33, 1.00, 33.33, 'Eliminada'),
(5450, 'Educación', 'Proyecto', NULL, 0.00, 0.00, 'Formulada', 1.00, 25.00, 'Postulada a financiamiento', 1.00, 25.00, NULL, 0.00, 0.00, 'Etapa en ejecución', 1.00, 25.00, NULL, 0.00, 25.00, 4.00, 100.00, 0, NULL, NULL, 25.00, 1.00, 25.00, 'Eliminada'),
(5451, 'Institucional', 'Programa', 'En espera', 2.00, 40.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'En licitación', 2.00, 40.00, 'Etapa en ejecución', 1.00, 20.00, NULL, 0.00, 0.00, 5.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5452, 'Medio Ambiente', 'Estudio', 'En espera', 3.00, 60.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'En licitación', 1.00, 20.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 5.00, 80.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5453, 'Medio Ambiente', 'Programa', NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Etapa en ejecución', 1.00, 100.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5454, 'Medio Ambiente', 'Proyecto', 'En espera', 1.00, 50.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 2.00, 50.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5455, 'Salud', 'Estudio', 'En espera', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5456, 'Salud', 'Programa', NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 0.00, 0, 1.00, 'Sustituida', 0.00, NULL, 0.00, NULL),
(5457, 'Salud', 'Proyecto', 'En espera', 1.00, 20.00, 'Formulada', 1.00, 20.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 2.00, 0.00, 5.00, 40.00, 0, 1.00, 'Sustituida', 0.00, NULL, 0.00, NULL),
(5458, 'Seguridad vecinal', 'Estudio', NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'En licitación', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5459, 'Seguridad vecinal', 'Programa', NULL, 0.00, 0.00, 'Formulada', 1.00, 33.33, 'Postulada a financiamiento', 1.00, 33.33, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 3.00, 66.67, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5460, 'Seguridad vecinal', 'Proyecto', 'En espera', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5461, 'Social – Comunitario', 'Estudio', 'En espera', 1.00, 50.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 50.00, 2.00, 100.00, 0, NULL, NULL, 50.00, 1.00, 50.00, 'Eliminada'),
(5462, 'Social – Comunitario', 'Programa', 'En espera', 2.00, 50.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'En licitación', 1.00, 25.00, NULL, 0.00, 0.00, 'Detenida por fuerza mayor', 1.00, 0.00, 4.00, 75.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5463, 'Social – Comunitario', 'Proyecto', NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Postulada a financiamiento', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5464, 'Turismo', 'Estudio', 'En espera', 1.00, 33.33, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 'Etapa en ejecución', 1.00, 33.33, NULL, 0.00, 33.33, 3.00, 100.00, 0, NULL, NULL, 33.33, 1.00, 33.33, 'Eliminada'),
(5465, 'Turismo', 'Programa', 'En espera', 1.00, 100.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 1.00, 100.00, 0, NULL, NULL, 0.00, NULL, 0.00, NULL),
(5466, 'Turismo', 'Proyecto', 'En espera', 2.00, 66.67, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, NULL, 0.00, 0.00, 3.00, 66.67, 0, 1.00, 'Sustituida', 0.00, NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

DROP TABLE IF EXISTS `temp`;
CREATE TABLE `temp` (
  `id` int NOT NULL,
  `nombre` varchar(400) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `temp`
--

INSERT INTO `temp` (`id`, `nombre`, `estado`) VALUES
(1, 'Adquisición camiones para recolección de basura y limpieza de diversos sectores de la comuna', 'En ejecución'),
(2, 'Adquisición maquinarias para aseo y ornato diversas plazas y calles', 'En ejecución'),
(3, 'Adquisición Planta Agua Potable Solar, Caleta de Hornitos', 'En ejecución'),
(4, 'Adquisición y reposición contenedores residuos sólidos domiciliarios, comuna de Mejillones', 'En ejecución'),
(5, 'Conservación albergue deportivo municipal, comuna de Mejillones', 'En ejecución'),
(6, 'Conservación cementerio municipal, comuna de Mejillones', 'En ejecución'),
(7, 'Conservación centro cultural (Teatro Municipal), comuna de Mejillones', 'En ejecución'),
(8, 'Conservación centro cultural, comuna de Mejillones', 'En ejecución'),
(9, 'Conservación centro recreacional adulto mayor, Mejillones', 'En ejecución'),
(10, 'Conservación cerco perimetral relleno sanitario, Mejillones', 'En ejecución'),
(11, 'Conservación estadio municipal, comuna de Mejillones', 'En ejecución'),
(12, 'Conservación multicancha estadio municipal, Mejillones', 'En ejecución'),
(13, 'Conservación plaza Playa Blanca', 'En ejecución'),
(14, 'Conservación polideportivo municipal, comuna de Mejillones', 'En ejecución'),
(15, 'Conservación techado deportivo municipal, comuna de Mejillones', 'En ejecución'),
(16, 'Construcción centro veterinario de atención primaria 2020', 'En ejecución'),
(17, 'Construcción ciclovías urbanas Mejillones', 'En ejecución'),
(18, 'Construcción edificio consistorial, comuna de Mejillones', 'En ejecución'),
(19, 'Construcción eje de integración Sargento Gabriel Silva Mejillones', 'En ejecución'),
(20, 'Construcción macrourbanización sector barrio cívico, comuna de Mejillones', 'En ejecución'),
(21, 'Construcción plaza Carol Urzua, barrio Pablo Neruda, Mejillones', 'En ejecución'),
(22, 'Construcción plaza La Sirenita, Mejillones', 'En ejecución'),
(23, 'Construcción Pueblito de los Artesanos, borde costero', 'En ejecución'),
(24, 'Construcción sombreadero bandejón Av. Andalicán', 'En ejecución'),
(25, 'Construcción sombreadero Plaza de Calistenia', 'En ejecución'),
(26, 'Construcción sombreadero plaza Oasis de Chacaya', 'En ejecución'),
(27, 'Diversas iniciativas de inversión en conservación de espacios públicos', 'En ejecución'),
(28, 'Habilitación alumbrado público entre Av. Fertilizantes y módulo de atención primaria, comuna de Mejillones.', 'En ejecución'),
(29, 'Habilitación de alumbrado público entre Av. Fertilizantes y playa Rinconada.', 'En ejecución'),
(30, 'Habilitación de la red de alumbrado público entre calle Serrano y Cementerio.', 'En ejecución'),
(31, 'Instalación de luminarias solares de emergencia para vías de evacuación, sector centro de Mejillones -Av. O’Higgins', 'En ejecución'),
(32, 'Instalación de luminarias solares de emergencia para vías de evacuación, sector poniente de Mejillones -Av. Manuel Montt.', 'En ejecución'),
(33, 'Instalación de luminarias solares de emergencia para vías de evacuación, sector poniente de Mejillones -Av. Manuel Rodríguez.', 'En ejecución'),
(34, 'Instalación iluminación solar de emergencia para vías de evacuación, sector oriente de Mejillones – Av. Serrano.', 'En ejecución'),
(35, 'Instalación luminarias solares de emergencia para vías de evacuación, sector centro de Mejillones – Av. Riquelme', 'En ejecución'),
(36, 'Mantención y construcción señaléticas y reductores de velocidad', 'En ejecución'),
(37, 'Mejoramiento barrio Casa de Máquina, comuna de Mejillones', 'En ejecución'),
(38, 'Mejoramiento club deportivo estibadores de Génova', 'En ejecución'),
(39, 'Mejoramiento multicancha Luis Adduard, comuna de Mejillones', 'En ejecución'),
(40, 'Mejoramiento plaza de juegos multicancha Salvador Allende', 'En ejecución'),
(41, 'Mejoramiento plaza Granaderos', 'En ejecución'),
(42, 'Mejoramiento sede localidad de Michilla', 'En ejecución'),
(43, 'Mejoramiento Sistema de Agua Potable Rural Carolina de Michilla', 'En ejecución'),
(44, 'Plan comunal de emergencias. 2021-2024', 'En ejecución'),
(45, 'Reposición -adquisición maquinarias y equipo para operación vertedero Municipal Mejillones', 'En ejecución'),
(46, 'Saneamiento sanitario de la planta de tratamiento y normalización de la red de alcantarillado existente, localidad de Michilla', 'En ejecución'),
(47, '3 proyectos de puesta en valor de sitios de interés ambiental', 'Idea'),
(48, 'Actualización de catastro comunal de campamentos y vivienda informal', 'Idea'),
(49, 'Actualización estudio de red de salud', 'Idea'),
(50, 'Actualización estudio de red escolar', 'Idea'),
(51, 'Caletas turísticas', 'Idea'),
(52, 'Calidad y sostenibilidad en la oferta turística', 'Idea'),
(53, 'Centro comunitario de seguridad (móvil) multipropósito', 'Idea'),
(54, 'Centro de acopio, selección y pretratamiento (no incluye terreno)', 'Idea'),
(55, 'Centro de Interpretación Playa Punta Itata', 'Idea'),
(56, 'Centro de operaciones de seguridad y emergencia', 'Idea'),
(57, 'Centro de operaciones de seguridad y emergencia - Etapa perfil', 'Idea'),
(58, 'Centro de valorización de residuos (no incluye terreno)', 'Idea'),
(59, 'Ciclovía recreativa de 3,5 km para recorrer las antiguas instalaciones del FCAB', 'Idea'),
(60, 'Construcción Centro Cultural de Mejillones', 'Idea'),
(61, 'Construcción Centro Cultural de Mejillones - Etapa anteproyecto', 'Idea'),
(62, 'Construcción Centro Cultural de Mejillones - Etapa perfil', 'Idea'),
(63, 'Construcción de 2 jardines infantiles o sala cuna para 300 niños y niñas', 'Idea'),
(64, 'Construcción de la Casa de la Mujer de Mejillones', 'Idea'),
(65, 'Construcción de la Casa de la Mujer de Mejillones - Etapa Perfil', 'Idea'),
(66, 'Construcción de Piscina temperada municipal', 'Idea'),
(67, 'Construcción de Piscina temperada municipal - Etapa Anteproyecto ', 'Idea'),
(68, 'Construcción de Piscina temperada municipal - Etapa Prefactibilidad ', 'Idea'),
(69, 'Construcción servicio de atención en zona rural', 'Idea'),
(70, 'Consultoría fortalecimiento organizacional de la Corporación Municipal de Deportes', 'Idea'),
(71, 'Diseño de un plan de fiscalización ambiental preventivo', 'Idea'),
(72, 'Estrategia de adaptación al cambio climático a nivel comunal, vinculado a estrategia regional.', 'Idea'),
(73, 'Estudio Actualización de catastro comunal de predios públicos y privados', 'Idea'),
(74, 'Estudio actualización proyecto educativo', 'Idea'),
(75, 'Estudio caracterización de población inmigrante.', 'Idea'),
(76, 'Estudio condiciones de radicación de trabajadores en la comuna de Mejillones', 'Idea'),
(77, 'Estudio de geolocalización de riesgos (Mapas de amenazas y riesgos)', 'Idea'),
(78, 'Estudio de perfil de usuarios hospital', 'Idea'),
(79, 'Estudio de población en situación de discapacidad.', 'Idea'),
(80, 'Estudio de redes de agua y redes eléctricas - Etapa perfil', 'Idea'),
(81, 'Estudio desarrollo de alternativas habitacionales sustentables en altura media de transición modalidad arriendo', 'Idea'),
(82, 'Estudio desarrollo de alternativas habitacionales sustentables unifamiliares en baja altura', 'Idea'),
(83, 'Estudio fortalecimiento educación TP', 'Idea'),
(84, 'Estudio Plan maestro para el desarrollo inmobiliario de terrenos del FCAB', 'Idea'),
(85, 'Estudio Plan maestro y de gestión para la recuperación del borde costero urbano en el área consolidada de Mejillones', 'Idea'),
(86, 'Estudios de desarrollo organizacional para instalar una cultura digital y ágil ', 'Idea'),
(87, 'Hitos turísticos', 'Idea'),
(88, 'Implementación de mejoras', 'Idea'),
(89, 'Incubadora de emprendimientos de economía circular', 'Idea'),
(90, 'Iniciativas del plan de puesta en valor de atractivos y sitios de interés turístico', 'Idea'),
(91, 'Instalación Servicio Sanitario Rural (SSR) Caleta Hornitos', 'Idea'),
(92, 'Instalación Servicio Sanitario Rural (SSR) Caleta Hornitos – Etapa Prefactibilidad', 'Idea'),
(93, 'Instalación Sistema de Alcantarillado Caleta Hornitos', 'Idea'),
(94, 'Instalación Sistema de Alcantarillado Caleta Hornitos – Etapa Prefactibilidad', 'Idea'),
(95, 'Inversión en equipamiento e infraestructura tecnológica', 'Idea'),
(96, 'Mejoramiento conectividad digital para Michilla', 'Idea'),
(97, 'Mejoramiento conectividad digital para Michilla - Etapa Perfil', 'Idea'),
(98, 'Mejoramiento de sistema de señalética vial y peatonal', 'Idea'),
(99, 'Mejoramiento del Alumbrado Público de Mejillones mediante implementación de sistema Smart o telegestión.', 'Idea'),
(100, 'Modelo de Gestión del Museo Municipal de Mejillones (en ejecución)', 'Idea'),
(101, 'Museo de Sitio de las Guaneras de Mejillones (Centro de Visitantes y habilitación de recorridos).', 'Idea'),
(102, 'Normalización de sistemas comunales de redes de agua y redes eléctricas', 'Idea'),
(103, 'Oficinas de información', 'Idea'),
(104, 'Paradores turísticos', 'Idea'),
(105, 'Plan comunal de Cultura', 'Idea'),
(106, 'Plan comunal de deportes', 'Idea'),
(107, 'Plan Comunal de Gestión Integrada de Residuos ', 'Idea'),
(108, 'Plan comunal de seguridad pública de Mejillones (2023-2028)', 'Idea'),
(109, 'Plan de identificación y puesta en valor de sitios de interés ambiental', 'Idea'),
(110, 'Plan de Inversiones en Infraestructura de Movilidad y Espacio Público comuna de Mejillones (PIIMEP)', 'Idea'),
(111, 'Plan de ordenamiento turístico', 'Idea'),
(112, 'Plan de puesta en valor de atractivos y sitios de interés turístico', 'Idea'),
(113, 'Plan de uso de borde costero con fines turísticos', 'Idea'),
(114, 'Plan Maestro Patrimonial de Mejillones', 'Idea'),
(115, 'Programa de apoyo legal para la regularización migratoria', 'Idea'),
(116, 'Programa de capacitación y certificación cívica población con débil inserción social', 'Idea'),
(117, 'Programa de capacitación y mejoramiento de las capacidades de gestión de la comunidad y sus organizaciones', 'Idea'),
(118, 'Programa de conservación y mantenimiento de infraestructura deportiva', 'Idea'),
(119, 'Programa de educación ambiental', 'Idea'),
(120, 'Programa de Educación Patrimonial', 'Idea'),
(121, 'Programa de empleabilidad de la mujer', 'Idea'),
(122, 'Programa de emprendimiento para la reconversión de la Pesca artesanal', 'Idea'),
(123, 'Programa de fortalecimiento de la integración social y la multiculturalidad', 'Idea'),
(124, 'Programa de fortalecimiento de organizaciones sociedad civil', 'Idea'),
(125, 'Programa de implementación de medios de vigilancia y alerta', 'Idea'),
(126, 'Programa de inserción laboral población con discapacidad', 'Idea'),
(127, 'Programa de inversión en el mejoramiento de la accesibilidad a espacios públicos y al borde costero', 'Idea'),
(128, 'Programa de inversión en el mejoramiento de la accesibilidad a espacios públicos y al borde costero – Etapa Perfil', 'Idea'),
(129, 'Programa de inversión en equipamiento comunitario', 'Idea'),
(130, 'Programa de inversión en la red de equipamiento comunitario comunal', 'Idea'),
(131, 'Programa de inversión en mejoramiento viviendas para la accesibilidad universal', 'Idea'),
(132, 'Programa de inversión en mejoramiento viviendas para la accesibilidad universal – Etapa Perfil', 'Idea'),
(133, 'Programa de prevención de consumo de droga', 'Idea'),
(134, 'Programa de promoción del emprendimiento turístico comunal', 'Idea'),
(135, 'Programa de selección y capacitación de patrulleros', 'Idea'),
(136, 'Programa desarrollo de una cultura digital para transitar de municipio análogo a digital.', 'Idea'),
(137, 'Programa fortalecimiento servicio de salud en zona rural', 'Idea'),
(138, 'Programa para la digitalización de la labor educativa', 'Idea'),
(139, 'Programa piloto de electromovilidad ', 'Idea'),
(140, 'Programa promoción mujer jefa de hogar', 'Idea'),
(141, 'Programa sello municipal de vida saludable', 'Idea'),
(142, 'Programa vinculación con la historia y el turismo de la comuna', 'Idea'),
(143, 'Proyecto de mejoramiento de iluminación pública', 'Idea'),
(144, 'Proyecto de mejoramiento de iluminación pública – Etapa Perfil', 'Idea'),
(145, 'Proyecto de Rehabilitación de Inmueble Correos de Chile - ICH (Calle Ongolmo 601).', 'Idea'),
(146, 'Proyecto de Rehabilitación del edificio Ilustre Municipalidad de Mejillones- ICH (Calle Francisco Antonio Pinto 200).', 'Idea'),
(147, 'Proyecto PIIMEP 20 ciclo refugios distribuidos en la ciudad', 'Idea'),
(148, 'Proyecto PIIMEP 20 plazas existentes intervenidas con accesibilidad universal.', 'Idea'),
(149, 'Proyecto red pública de monitoreo de la bahía de Mejillones', 'Idea'),
(150, 'Proyecto red pública de monitoreo de la bahía de Mejillones - Etapa Prefactibilidad', 'Idea'),
(151, 'Recuperación del borde costero urbano en el área consolidada de Mejillones', 'Idea'),
(152, 'Recuperación patrimonial de una de las antiguas construcciones existentes FCAB y rehabilitación de espacio público', 'Idea'),
(153, 'Regulación y adecuación', 'Idea'),
(154, 'Reposición de 1 escuela', 'Idea'),
(155, 'Road Map digitalización e instalación de capacidades digitales', 'Idea'),
(156, 'Señalética Balneario de Hornitos', 'Idea'),
(157, 'Señalética turística', 'Idea'),
(158, 'Sendero interpretativo en Mirador de Punta Angamos', 'Idea'),
(159, 'Senderos turísticos', 'Idea'),
(160, 'Sistema de comunicación autónomo para emergencias', 'Idea'),
(161, 'Sistema de recolección diferenciado de residuos', 'Idea'),
(162, 'Sistema de registro, actualización y análisis de información ambiental de interés comunal.', 'Idea'),
(163, 'Vehículo para puesto de comando móvil', 'Idea');

-- --------------------------------------------------------

--
-- Table structure for table `tipo`
--

DROP TABLE IF EXISTS `tipo`;
CREATE TABLE `tipo` (
  `id` int NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tipo`
--

INSERT INTO `tipo` (`id`, `codigo`, `tipo`) VALUES
(1, 'a', 'Estudio'),
(2, 'b', 'Programa'),
(3, 'c', 'Proyecto');

-- --------------------------------------------------------

--
-- Table structure for table `ubicaciones`
--

DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE `ubicaciones` (
  `id` int NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `lat` varchar(100) DEFAULT NULL,
  `lng` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `stroke` varchar(20) DEFAULT NULL,
  `ubicaciones_last_update` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `codigo`, `ubicacion`, `lat`, `lng`, `color`, `stroke`, `ubicaciones_last_update`) VALUES
(1, '1', 'Sucre 44', '-23.6469581', '-70.397108', '', '', NULL),
(3, '3', 'Centro histórico de Antofagasta', '-23.646396450554658', '-70.39802491664888', NULL, NULL, NULL),
(4, '4', 'Vivero municipal de Antofagasta', '-23.625893586702777', '-70.39444684982301', NULL, NULL, NULL),
(5, '5', 'PET Municipal', '-23.5553159', '-70.3849721', NULL, NULL, NULL),
(8, '8', 'Dirección de Medio Ambiente y Ornato', '-23.639206', '-70.396196', NULL, NULL, NULL),
(9, '9', 'Fomento productivo', '-23.6395098', '-70.3964773', NULL, NULL, NULL),
(10, '10', 'Sector norte', '-23.496745', '-70.409153', NULL, NULL, NULL),
(12, '12', 'Borde costero de Antofagasta', '-23.584996', '-70.393335', NULL, NULL, NULL),
(13, '13', 'Seguridad ciudadana', '-23.639237', '-70.395928', NULL, NULL, NULL),
(14, '14', 'Corporación Municipal de Desarrollo Social', '-23.660994', '-70.396522', NULL, NULL, NULL),
(15, '15', 'La Chimba', '-23.552244', '-70.391361', NULL, NULL, NULL),
(16, '16', 'Corporación Municipal de Deportes', '-23.669134', '-70.405133', NULL, NULL, NULL),
(18, '18', 'Corporación Municipal de Desarrollo Social', '-23.660791', '-70.396452', NULL, NULL, NULL),
(19, '19', 'Dirección de Salud', '-23.646807', '-70.395314\r\n\r\n', NULL, NULL, NULL),
(21, '21', 'DIDECO Antofagasta', '-23.639401', '-70.395966', NULL, NULL, NULL),
(24, '24', 'Dirección de Turismo', '-23.646024', '-70.397532', NULL, NULL, NULL),
(26, '26', 'Cementerio General de Antofagasta', '-23.646721', '-70.386878', NULL, NULL, NULL),
(27, '29', 'Dirección de Gestión de Personas', '-23.6457649', '-70.3996022', NULL, NULL, NULL),
(28, '30', 'Secretaría municipal', '-23.639455', '-70.396141', NULL, NULL, NULL),
(29, '31', 'Dirección de Cultura, Arte y Patrimonio', '-23.648351', '-70.397812', NULL, NULL, NULL),
(30, '32', 'Dirección de Obras Municipales', '-23.63938888177454', '-70.39654433727266', NULL, NULL, NULL),
(31, '33', 'Dirección de Operaciones', '-23.595308', '-70.380978', NULL, NULL, NULL),
(32, '34', 'Dirección Secretaría Comunal de Planificación', '-23.639656', '-70.396262', NULL, NULL, NULL),
(35, '35', 'Corporación Municipal de Desarrollo Social', '-23.661206444536017 ', '-70.39659798145296', NULL, NULL, NULL),
(36, '36', 'Dirección de las Tecnologías de Información', '-23.639182482055176', '-70.39652824401857', NULL, NULL, NULL),
(37, '202308281638', 'Ex vertedero La Chimba', '-23.548413', '-70.384366', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `usuarios_id` int NOT NULL,
  `usuarios_userid` varchar(100) DEFAULT NULL,
  `usuarios_password` varchar(100) DEFAULT NULL,
  `usuarios_nombre` varchar(100) DEFAULT NULL,
  `usuarios_email` varchar(100) DEFAULT NULL,
  `usuarios_profile` varchar(20) DEFAULT 'INVITADO',
  `usuarios_updated` varchar(100) DEFAULT NULL,
  `last_login` varchar(100) DEFAULT NULL,
  `usuarios_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`usuarios_id`, `usuarios_userid`, `usuarios_password`, `usuarios_nombre`, `usuarios_email`, `usuarios_profile`, `usuarios_updated`, `last_login`, `usuarios_foto`) VALUES
(1, 'marjim', '14573551495e839dc422ebc281e92c3c', 'Marcelo Jiménez S.', 'jimenez@fulldsi.com', 'ADMINISTRADOR', '03-10-2024 19:13:07', '2024-10-03 14:11:15', '../../profiles/1-20241003191307-iso.fw.png'),
(9, 'invitado', 'a6ae8a143d440ab8c006d799f682d48d', 'Invitado', 'mj@dsi.cl', 'INVITADO', '02-07-2023 00:43:23', '2023-09-20 12:57:18', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_gestion`
--
ALTER TABLE `area_gestion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`parametros_id`);

--
-- Indexes for table `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subareas`
--
ALTER TABLE `subareas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tabla`
--
ALTER TABLE `tabla`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuarios_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=416;

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `area_gestion`
--
ALTER TABLE `area_gestion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `origen`
--
ALTER TABLE `origen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `parametros`
--
ALTER TABLE `parametros`
  MODIFY `parametros_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1177;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subareas`
--
ALTER TABLE `subareas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `tabla`
--
ALTER TABLE `tabla`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5467;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuarios_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
