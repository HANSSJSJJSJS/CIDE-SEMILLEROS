-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 10-02-2026 a las 20:47:10
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cide_sena2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_usuario`, `creado_en`, `actualizado_en`) VALUES
(130, '2026-02-10 15:52:11', '2026-02-10 15:52:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ficha` varchar(30) DEFAULT NULL,
  `programa` varchar(160) DEFAULT NULL,
  `nivel_educativo` enum('ARTICULACION_MEDIA_10_11','TECNOACADEMIA_7_9','TECNICO','TECNOLOGO','PROFESIONAL') DEFAULT NULL,
  `vinculado_sena` tinyint(1) NOT NULL DEFAULT 1,
  `institucion` varchar(160) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `nombres` varchar(160) DEFAULT NULL,
  `apellidos` varchar(160) DEFAULT NULL,
  `contacto_nombre` varchar(160) DEFAULT NULL,
  `contacto_celular` varchar(30) DEFAULT NULL,
  `semillero_id` bigint(20) UNSIGNED DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aprendices`
--

INSERT INTO `aprendices` (`id_aprendiz`, `user_id`, `ficha`, `programa`, `nivel_educativo`, `vinculado_sena`, `institucion`, `correo_institucional`, `nombres`, `apellidos`, `contacto_nombre`, `contacto_celular`, `semillero_id`, `creado_en`, `actualizado_en`, `estado`) VALUES
(95, 103, '2835566', 'adsi', 'TECNOLOGO', 1, NULL, 'hu@soy.sena.edu.co', 'Juan', NULL, 'sumu', '3245677888', 9, '2025-12-02 21:38:32', '2025-12-16 18:47:46', 'Activo'),
(96, 111, '2827004', 'adso', 'TECNOLOGO', 1, NULL, 'kis@soy.sena.edu.co', 'Ana', NULL, 'dumam', '3265436789', 9, '2025-12-05 18:15:17', '2025-12-16 18:47:46', 'Activo'),
(97, 112, '763463726', 'adso', 'TECNOLOGO', 1, NULL, 'iva@soy.sena.edu.co', 'Luis', NULL, 'kagada', '3242528122', 9, '2025-12-05 18:22:04', '2025-12-16 18:47:46', 'Activo'),
(98, 119, '43454556', 'adsi', 'TECNOLOGO', 1, NULL, 'lu@soy.sena.edu.co', 'Sara', NULL, 'juaco', '3287373821', 9, '2025-12-10 13:20:58', '2025-12-16 18:47:46', 'Activo'),
(99, 121, '28187111', 'ADSO', 'TECNOLOGO', 1, NULL, 'guz@soy.sena.edu.co', NULL, NULL, 'kamimsima', '323211786', 9, '2025-12-17 15:38:30', '2025-12-17 15:38:30', 'Activo'),
(102, 132, '2827004', 'ADSO', 'TECNOLOGO', 1, NULL, 'hans@soy.sena.edu.co', NULL, NULL, 'milena', '3108557005', 18, '2026-02-10 16:20:51', '2026-02-10 16:20:51', 'Activo'),
(103, 133, '217832', 'ADSO', 'TECNOLOGO', 1, NULL, 'joaquin@soy.sena.edu.co', NULL, NULL, 'Ana Cañon', '3165268736', 18, '2026-02-10 16:34:34', '2026-02-10 16:34:34', 'Activo'),
(104, 134, '823722', 'Animación 3D', 'TECNOLOGO', 1, NULL, 'geral@soy.sena.edu.co', NULL, NULL, 'Maria Fajardo', '3428906789', 18, '2026-02-10 16:36:23', '2026-02-10 16:36:23', 'Activo'),
(105, 135, '2327626', 'ADSO', 'TECNOLOGO', 1, NULL, 'kevin@soy.sena.edu.co', NULL, NULL, 'Carlos Ruiz', '3256789056', 18, '2026-02-10 16:38:05', '2026-02-10 16:38:05', 'Activo'),
(106, 136, '982374', 'ADSO', 'TECNOLOGO', 1, NULL, 'dayana@soy.sena.edu.co', NULL, NULL, 'Carlos Lopez', '3245678909', 18, '2026-02-10 16:39:52', '2026-02-10 16:39:52', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendiz_proyecto`
--

CREATE TABLE `aprendiz_proyecto` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aprendiz_proyecto`
--

INSERT INTO `aprendiz_proyecto` (`id`, `id_aprendiz`, `id_proyecto`) VALUES
(13, 69, 10),
(14, 85, 10),
(15, 83, 10),
(16, 82, 10),
(17, 85, 13),
(18, 90, 12),
(222, 106, 17),
(224, 102, 18),
(226, 102, 19),
(228, 105, 20),
(230, 105, 21),
(232, 105, 22),
(234, 105, 23),
(236, 106, 24),
(239, 103, 16),
(240, 102, 16),
(242, 102, 15),
(243, 104, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('semilleros-cide-sena-cache-holidays:v4:CO:2025', 'a:5:{s:2:\"ok\";b:1;s:6:\"source\";s:8:\"nager.at\";s:7:\"country\";s:2:\"CO\";s:4:\"year\";i:2025;s:5:\"dates\";a:17:{i:0;s:10:\"2025-01-01\";i:1;s:10:\"2025-01-06\";i:2;s:10:\"2025-03-24\";i:3;s:10:\"2025-04-17\";i:4;s:10:\"2025-04-18\";i:5;s:10:\"2025-05-01\";i:6;s:10:\"2025-06-02\";i:7;s:10:\"2025-06-23\";i:8;s:10:\"2025-06-30\";i:9;s:10:\"2025-07-20\";i:10;s:10:\"2025-08-07\";i:11;s:10:\"2025-08-18\";i:12;s:10:\"2025-10-13\";i:13;s:10:\"2025-11-03\";i:14;s:10:\"2025-11-17\";i:15;s:10:\"2025-12-08\";i:16;s:10:\"2025-12-25\";}}', 1770782110),
('semilleros-cide-sena-cache-holidays:v4:CO:2026', 'a:5:{s:2:\"ok\";b:1;s:6:\"source\";s:8:\"nager.at\";s:7:\"country\";s:2:\"CO\";s:4:\"year\";i:2026;s:5:\"dates\";a:18:{i:0;s:10:\"2026-01-01\";i:1;s:10:\"2026-01-12\";i:2;s:10:\"2026-03-23\";i:3;s:10:\"2026-04-02\";i:4;s:10:\"2026-04-03\";i:5;s:10:\"2026-05-01\";i:6;s:10:\"2026-05-18\";i:7;s:10:\"2026-06-08\";i:8;s:10:\"2026-06-15\";i:9;s:10:\"2026-06-29\";i:10;s:10:\"2026-07-20\";i:11;s:10:\"2026-08-07\";i:12;s:10:\"2026-08-17\";i:13;s:10:\"2026-10-12\";i:14;s:10:\"2026-11-02\";i:15;s:10:\"2026-11-16\";i:16;s:10:\"2026-12-08\";i:17;s:10:\"2026-12-25\";}}', 1770782111),
('semilleros-cide-sena-cache-holidays:v4:CO:2027', 'a:5:{s:2:\"ok\";b:1;s:6:\"source\";s:8:\"nager.at\";s:7:\"country\";s:2:\"CO\";s:4:\"year\";i:2027;s:5:\"dates\";a:18:{i:0;s:10:\"2027-01-01\";i:1;s:10:\"2027-01-11\";i:2;s:10:\"2027-03-22\";i:3;s:10:\"2027-03-25\";i:4;s:10:\"2027-03-26\";i:5;s:10:\"2027-05-01\";i:6;s:10:\"2027-05-10\";i:7;s:10:\"2027-05-31\";i:8;s:10:\"2027-06-07\";i:9;s:10:\"2027-07-05\";i:10;s:10:\"2027-07-20\";i:11;s:10:\"2027-08-07\";i:12;s:10:\"2027-08-16\";i:13;s:10:\"2027-10-18\";i:14;s:10:\"2027-11-01\";i:15;s:10:\"2027-11-15\";i:16;s:10:\"2027-12-08\";i:17;s:10:\"2027-12-25\";}}', 1770782103);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id_documento` int(10) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `numero_evidencia` int(11) DEFAULT NULL,
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `titulo_avance` varchar(255) DEFAULT NULL,
  `descripcion_avance` text DEFAULT NULL,
  `documento` varchar(255) DEFAULT NULL,
  `ruta_archivo` varchar(1000) DEFAULT NULL,
  `tipo_archivo` enum('PDF','WORD','PRESENTACION','VIDEO','IMAGEN','ENLACE','OTRO') NOT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `enlace_evidencia` varchar(1000) DEFAULT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `fecha_subido` datetime DEFAULT current_timestamp(),
  `fecha_avance` date DEFAULT NULL,
  `tamanio` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_subida` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `rechazado_en` datetime DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `respuesta_lider` text DEFAULT NULL,
  `respondido_en` datetime DEFAULT NULL,
  `respuesta_leida` datetime DEFAULT NULL,
  `pregunta_aprendiz` text DEFAULT NULL,
  `preguntado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id_documento`, `id_proyecto`, `numero_evidencia`, `id_aprendiz`, `titulo_avance`, `descripcion_avance`, `documento`, `ruta_archivo`, `tipo_archivo`, `tipo_documento`, `enlace_evidencia`, `mime_type`, `fecha_subido`, `fecha_avance`, `tamanio`, `fecha_subida`, `estado`, `rechazado_en`, `fecha_limite`, `descripcion`, `respuesta_lider`, `respondido_en`, `respuesta_leida`, `pregunta_aprendiz`, `preguntado_en`) VALUES
(1, 12, NULL, 90, NULL, NULL, 'ejemplo entrega', 'documentos/1763479448_90_Plantilla_cide2.docx', 'PDF', NULL, NULL, '', '2025-11-18 09:06:26', NULL, 107498, '2025-11-18 10:24:08', 'aprobado', NULL, '2025-11-18', 'manual', NULL, NULL, NULL, NULL, NULL),
(2, 12, NULL, 90, NULL, NULL, 'ejemplo entrega', 'documentos/1763475058_90_manualejemplo2.pdf', 'PDF', NULL, NULL, '', '2025-11-18 09:07:11', NULL, 272439, '2025-11-18 09:10:58', 'aprobado', NULL, '2025-11-29', 'manual  empresarial', NULL, NULL, NULL, NULL, NULL),
(3, 12, NULL, 90, NULL, NULL, 'manual', 'documentos/1763474949_90_manualejemplo2.pdf', 'PDF', NULL, NULL, NULL, '2025-11-18 09:09:09', NULL, 272439, '2025-11-18 09:09:09', 'aprobado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 12, NULL, 90, NULL, NULL, 'ejemplo entrega 1.1.1', 'documentos/1763479325_90_manualejemplo2.pdf', 'PDF', NULL, NULL, '', '2025-11-18 10:10:44', NULL, 272439, '2025-11-18 10:22:05', 'aprobado', NULL, '2025-11-29', 'entrega de proyecto final', NULL, NULL, NULL, NULL, NULL),
(5, 7, NULL, 94, NULL, NULL, 'manual', 'documentos/1763664924_94_Lideres de Semilleros CIDE 2025.pdf', 'PDF', NULL, NULL, '', '2025-11-20 13:52:51', NULL, 602030, '2025-11-20 13:55:24', 'rechazado', NULL, '2025-11-22', 'maluma', NULL, NULL, NULL, NULL, NULL),
(6, 7, NULL, 94, NULL, NULL, 'manual', 'documentos/1763664916_94_constancia_complementaria.pdf', 'PDF', NULL, NULL, '', '2025-11-20 13:53:18', NULL, 36306, '2025-11-20 13:55:16', 'aprobado', NULL, '2025-11-23', 'mau', NULL, NULL, NULL, NULL, NULL),
(7, 9, 1, 95, NULL, NULL, 'fbrrdbre', '', 'PDF', NULL, NULL, '', '2025-12-05 13:31:12', NULL, 0, '2025-12-05 13:31:12', 'pendiente', NULL, '2025-12-07', 'iygyvyub', NULL, NULL, NULL, NULL, NULL),
(8, 8, 1, 95, NULL, NULL, 'gvgvg', '', 'PDF', NULL, NULL, '', '2025-12-05 13:31:37', NULL, 0, '2025-12-05 13:31:37', 'pendiente', NULL, '2025-12-07', 'gvgvyv h', NULL, NULL, NULL, NULL, NULL),
(9, 8, 2, 97, NULL, NULL, 'AVANECE', '', 'PDF', NULL, NULL, '', '2025-12-09 08:31:47', NULL, 0, '2025-12-09 08:31:47', 'pendiente', NULL, '2025-12-20', 'juadajj', NULL, NULL, NULL, NULL, NULL),
(10, 15, 1, 102, NULL, NULL, 'manual de usuario', 'documentos/1770747102_102_manual_instalador.pdf', 'PDF', 'pdf', NULL, '', '2026-02-10 13:09:01', NULL, 184103, '2026-02-10 13:11:42', 'aprobado', NULL, '2026-02-13', 'envia pdf con el manual de instrucciones', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_lider_semi` bigint(20) UNSIGNED DEFAULT NULL,
  `id_admin` bigint(20) UNSIGNED DEFAULT NULL,
  `id_proyecto` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion` int(11) NOT NULL DEFAULT 60,
  `ubicacion` varchar(255) DEFAULT NULL,
  `link_virtual` varchar(255) DEFAULT NULL,
  `codigo_reunion` varchar(255) DEFAULT NULL,
  `recordatorio` varchar(255) NOT NULL DEFAULT 'none',
  `tipo` enum('REUNION','CAPACITACION','SEGUIMIENTO','ENTREGA','OTRO') NOT NULL DEFAULT 'REUNION',
  `creado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `id_lider_semi`, `id_admin`, `id_proyecto`, `titulo`, `descripcion`, `fecha_hora`, `duracion`, `ubicacion`, `link_virtual`, `codigo_reunion`, `recordatorio`, `tipo`, `creado_por`, `created_at`, `updated_at`) VALUES
(72, 109, NULL, NULL, 'fbrrdbre', NULL, '2025-12-17 14:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'ENTREGA', NULL, '2025-12-17 18:34:49', '2025-12-17 18:34:49'),
(73, 109, NULL, 6, 'AVANECE', NULL, '2025-12-17 15:00:00', 90, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'SEGUIMIENTO', NULL, '2025-12-17 18:40:07', '2025-12-17 18:40:07'),
(74, 109, NULL, 6, 'avance', NULL, '2025-12-18 08:00:00', 90, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'REUNION', NULL, '2025-12-17 18:40:57', '2025-12-17 18:42:07'),
(75, 109, NULL, NULL, 'DISEÑO', NULL, '2025-12-18 10:00:00', 30, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'SEGUIMIENTO', NULL, '2025-12-17 18:41:33', '2025-12-17 18:41:33'),
(77, 109, NULL, NULL, 'fbrrdbre', NULL, '2025-12-19 08:00:00', 90, 'virtual', 'https://teams.live.com/meet/9384643488347?p=oddDzjUY8wqa2wF0Tb', NULL, 'none', 'SEGUIMIENTO', NULL, '2025-12-17 21:29:04', '2025-12-18 13:22:23'),
(78, 109, NULL, 9, 'DOCS', NULL, '2025-12-26 10:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'REUNION', NULL, '2025-12-26 14:34:57', '2025-12-26 14:34:57'),
(79, 109, NULL, 7, 'DOCS', NULL, '2026-01-06 10:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'SEGUIMIENTO', NULL, '2026-01-06 14:38:43', '2026-01-06 14:38:43'),
(80, NULL, NULL, 9, 'REUNION', NULL, '2026-01-06 14:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/reunion/695d39e8800bf', NULL, 'none', 'REUNION', NULL, '2026-01-06 16:35:52', '2026-01-06 16:35:52'),
(81, 109, NULL, NULL, 'juas', NULL, '2026-01-06 14:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'SEGUIMIENTO', NULL, '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(82, NULL, NULL, 9, 'REUNION', NULL, '2026-01-07 11:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/reunion/695e76ce01066', NULL, 'none', 'REUNION', NULL, '2026-01-07 15:07:57', '2026-01-07 15:07:58'),
(83, 109, NULL, 8, 'DOCS INFO', 'PRESENTARSE PLIS', '2026-01-07 11:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'SEGUIMIENTO', NULL, '2026-01-07 15:09:14', '2026-01-07 15:09:41'),
(84, 109, NULL, 7, 'DOCUMENTOS DE ENTREGA', 'ENTRAR AL LICK', '2026-01-07 14:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'SEGUIMIENTO', NULL, '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(85, 109, NULL, 8, 'DOCUMENTOS DE ENTREGA', NULL, '2026-01-08 10:00:00', 60, 'virtual', 'https://teams.live.com/meet/9340852124194?p=JPXDI73FbJImRfEbFk', NULL, 'none', 'ENTREGA', NULL, '2026-01-08 14:03:43', '2026-01-08 14:03:43'),
(86, 109, NULL, 8, 'Continuación +queperros', NULL, '2026-02-10 10:00:00', 60, 'presencial', NULL, NULL, '15', 'SEGUIMIENTO', NULL, '2026-02-10 14:20:30', '2026-02-10 14:20:30'),
(87, 131, NULL, 15, 'Continuación +queperros', NULL, '2026-02-10 14:00:00', 60, 'presencial', NULL, NULL, 'none', 'REUNION', NULL, '2026-02-10 16:55:18', '2026-02-10 16:55:18'),
(88, 131, NULL, 16, 'Entrega de proyecto', NULL, '2026-02-10 15:00:00', 60, 'virtual', 'https://meet.google.com/wck-goem-faw', NULL, 'none', 'ENTREGA', NULL, '2026-02-10 16:55:52', '2026-02-10 16:55:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_asignaciones`
--

CREATE TABLE `evento_asignaciones` (
  `id_asignaciones` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_destino` enum('LIDER_SEMILLERO','LIDER_INVESTIGACION','APRENDIZ','SEMILLERO','PROYECTO') NOT NULL,
  `destino_id` bigint(20) UNSIGNED NOT NULL,
  `asignado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evento_asignaciones`
--

INSERT INTO `evento_asignaciones` (`id_asignaciones`, `evento_id`, `tipo_destino`, `destino_id`, `asignado_por`, `created_at`) VALUES
(1, 5, 'LIDER_SEMILLERO', 12, 1, '2025-12-15 15:27:15'),
(2, 8, 'APRENDIZ', 45, 12, '2025-12-15 15:27:31'),
(3, 80, 'LIDER_SEMILLERO', 120, NULL, '2026-01-06 16:35:52'),
(4, 80, 'LIDER_SEMILLERO', 109, NULL, '2026-01-06 16:35:52'),
(5, 82, 'LIDER_SEMILLERO', 109, NULL, '2026-01-07 15:07:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_participantes`
--

CREATE TABLE `evento_participantes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `id_lider_semi` bigint(20) UNSIGNED DEFAULT NULL,
  `asistencia` enum('PENDIENTE','ASISTIO','NO_ASISTIO') DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evento_participantes`
--

INSERT INTO `evento_participantes` (`id`, `id_evento`, `id_aprendiz`, `id_lider_semi`, `asistencia`, `created_at`, `updated_at`) VALUES
(174, 75, 95, NULL, 'PENDIENTE', '2025-12-17 18:41:33', '2025-12-17 18:41:33'),
(175, 75, 96, NULL, 'PENDIENTE', '2025-12-17 18:41:33', '2025-12-17 18:41:33'),
(176, 75, 97, NULL, 'PENDIENTE', '2025-12-17 18:41:33', '2025-12-17 18:41:33'),
(177, 74, 95, NULL, 'PENDIENTE', '2025-12-17 18:42:07', '2025-12-17 18:42:07'),
(178, 74, 96, NULL, 'PENDIENTE', '2025-12-17 18:42:07', '2025-12-17 18:42:07'),
(181, 73, 97, NULL, 'PENDIENTE', '2025-12-17 18:42:47', '2025-12-17 18:42:47'),
(207, 77, 95, NULL, 'PENDIENTE', '2025-12-18 13:22:45', '2025-12-18 13:22:45'),
(208, 77, 96, NULL, 'PENDIENTE', '2025-12-18 13:22:45', '2025-12-18 13:22:45'),
(209, 77, 97, NULL, 'PENDIENTE', '2025-12-18 13:22:45', '2025-12-18 13:22:45'),
(210, 77, 98, NULL, 'PENDIENTE', '2025-12-18 13:22:45', '2025-12-18 13:22:45'),
(211, 77, 99, NULL, 'PENDIENTE', '2025-12-18 13:22:45', '2025-12-18 13:22:45'),
(214, 78, 95, NULL, 'PENDIENTE', '2025-12-26 14:34:57', '2025-12-26 14:34:57'),
(215, 78, 96, NULL, 'PENDIENTE', '2025-12-26 14:34:57', '2025-12-26 14:34:57'),
(216, 78, 97, NULL, 'PENDIENTE', '2025-12-26 14:34:57', '2025-12-26 14:34:57'),
(217, 78, 99, NULL, 'PENDIENTE', '2025-12-26 14:34:57', '2025-12-26 14:34:57'),
(219, 79, 95, NULL, 'ASISTIO', '2026-01-06 14:38:43', '2026-01-06 14:38:43'),
(220, 79, 96, NULL, 'ASISTIO', '2026-01-06 14:38:43', '2026-01-06 14:38:43'),
(221, 79, 97, NULL, 'ASISTIO', '2026-01-06 14:38:43', '2026-01-06 14:38:43'),
(222, 79, 98, NULL, 'ASISTIO', '2026-01-06 14:38:43', '2026-01-06 14:38:43'),
(224, 81, 95, NULL, 'ASISTIO', '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(225, 81, 96, NULL, 'ASISTIO', '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(226, 81, 97, NULL, 'NO_ASISTIO', '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(227, 81, 98, NULL, 'ASISTIO', '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(228, 81, 99, NULL, 'ASISTIO', '2026-01-06 16:36:52', '2026-01-06 16:36:52'),
(234, 83, 97, NULL, 'PENDIENTE', '2026-01-07 15:09:41', '2026-01-07 15:09:41'),
(235, 83, 98, NULL, 'PENDIENTE', '2026-01-07 15:09:41', '2026-01-07 15:09:41'),
(236, 83, 99, NULL, 'PENDIENTE', '2026-01-07 15:09:41', '2026-01-07 15:09:41'),
(237, 84, 95, NULL, 'ASISTIO', '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(238, 84, 96, NULL, 'NO_ASISTIO', '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(239, 84, 97, NULL, 'ASISTIO', '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(240, 84, 98, NULL, 'ASISTIO', '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(241, 84, 99, NULL, 'ASISTIO', '2026-01-07 15:12:02', '2026-01-07 15:12:02'),
(244, 85, 97, NULL, 'NO_ASISTIO', '2026-01-08 14:03:43', '2026-01-08 14:03:43'),
(245, 85, 98, NULL, 'ASISTIO', '2026-01-08 14:03:43', '2026-01-08 14:03:43'),
(246, 85, 99, NULL, 'ASISTIO', '2026-01-08 14:03:43', '2026-01-08 14:03:43'),
(247, 86, 97, NULL, 'PENDIENTE', '2026-02-10 14:20:30', '2026-02-10 14:20:30'),
(248, 86, 98, NULL, 'PENDIENTE', '2026-02-10 14:20:30', '2026-02-10 14:20:30'),
(249, 86, 99, NULL, 'PENDIENTE', '2026-02-10 14:20:30', '2026-02-10 14:20:30'),
(250, 87, 102, NULL, 'PENDIENTE', '2026-02-10 16:55:18', '2026-02-10 16:55:18'),
(251, 88, 103, NULL, 'PENDIENTE', '2026-02-10 16:55:52', '2026-02-10 16:55:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencias`
--

CREATE TABLE `evidencias` (
  `id_evidencia` bigint(20) UNSIGNED NOT NULL,
  `proyecto_id` int(10) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('pendiente','completado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lideres_investigacion`
--

CREATE TABLE `lideres_investigacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tiene_permisos` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lideres_investigacion`
--

INSERT INTO `lideres_investigacion` (`id`, `user_id`, `tiene_permisos`, `created_at`, `updated_at`) VALUES
(4, 110, 0, '2025-12-03 15:57:53', '2025-12-03 15:57:53'),
(5, 118, 0, '2025-12-10 13:19:27', '2025-12-10 13:19:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_lider_semi` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_semillero` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lideres_semillero`
--

INSERT INTO `lideres_semillero` (`id_lider_semi`, `id_usuario`, `correo_institucional`, `creado_en`, `actualizado_en`, `id_semillero`) VALUES
(109, 109, 'ju2@soy.sena.edu.co', '2025-12-03 15:24:42', '2025-12-03 15:24:42', 9),
(117, 117, 'kia@soy.sena.edu.co', '2025-12-10 13:16:16', '2025-12-26 19:01:20', 8),
(127, 127, 'valu2@soy.sena.edu.co', '2025-12-26 19:07:11', '2025-12-26 19:07:34', 11),
(129, 129, 'semi@soy.sena.edu.co', '2026-01-10 00:41:15', '2026-01-10 00:41:15', NULL),
(131, 131, 'wilson@soy.sena.edu.co', '2026-02-10 15:56:51', '2026-02-10 15:57:56', 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_19_103527_create_administradores_table', 0),
(2, '2026_01_19_103527_create_aprendices_table', 0),
(3, '2026_01_19_103527_create_aprendiz_proyecto_table', 0),
(4, '2026_01_19_103527_create_cache_table', 0),
(5, '2026_01_19_103527_create_cache_locks_table', 0),
(6, '2026_01_19_103527_create_documentos_table', 0),
(7, '2026_01_19_103527_create_evento_asignaciones_table', 0),
(8, '2026_01_19_103527_create_evento_participantes_table', 0),
(9, '2026_01_19_103527_create_eventos_table', 0),
(10, '2026_01_19_103527_create_evidencias_table', 0),
(11, '2026_01_19_103527_create_failed_jobs_table', 0),
(12, '2026_01_19_103527_create_job_batches_table', 0),
(13, '2026_01_19_103527_create_lideres_investigacion_table', 0),
(14, '2026_01_19_103527_create_lideres_semillero_table', 0),
(15, '2026_01_19_103527_create_password_reset_tokens_table', 0),
(16, '2026_01_19_103527_create_proyecto_user_table', 0),
(17, '2026_01_19_103527_create_proyectos_table', 0),
(18, '2026_01_19_103527_create_recursos_table', 0),
(19, '2026_01_19_103527_create_roles_table', 0),
(20, '2026_01_19_103527_create_semilleros_table', 0),
(21, '2026_01_19_103527_create_sessions_table', 0),
(22, '2026_01_19_103527_create_user_module_permissions_table', 0),
(23, '2026_01_19_103527_create_user_roles_table', 0),
(24, '2026_01_19_103527_create_users_table', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `id_semillero` int(10) UNSIGNED NOT NULL,
  `nombre_proyecto` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `documentos_requeridos` int(11) NOT NULL DEFAULT 3,
  `estado` enum('EN_FORMULACION','EN_EJECUCION','FINALIZADO','ARCHIVADO') DEFAULT 'EN_FORMULACION',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id_proyecto`, `id_semillero`, `nombre_proyecto`, `descripcion`, `observaciones`, `documentos_requeridos`, `estado`, `fecha_inicio`, `fecha_fin`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'IA para Agricultura', 'Proyecto de optimización de cultivos mediante IA', NULL, 3, 'EN_EJECUCION', '2025-01-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(2, 2, 'App de Aprendizaje SENA', 'Aplicación para gestión de aprendizajes', NULL, 3, 'EN_FORMULACION', '2025-02-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(3, 3, 'Sistema de Energía Solar', 'Investigación en paneles solares', NULL, 3, 'FINALIZADO', '2024-09-01', '2025-02-01', '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(4, 4, 'Monitor de Ciberseguridad', 'Monitoreo de redes locales', NULL, 3, 'EN_EJECUCION', '2025-03-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(5, 5, 'Robot Educativo', 'Robot con sensores para enseñanza STEAM', NULL, 3, 'EN_FORMULACION', '2025-04-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(6, 9, 'sgvevgewb', 'fsdbvdrbre', NULL, 3, 'FINALIZADO', '2025-11-14', '2025-11-30', '2025-11-10 19:12:02', '2025-11-10 19:12:02'),
(7, 9, 'avgerhgber', 'dvwdvd', NULL, 3, 'FINALIZADO', '2026-01-01', '2026-02-20', '2025-11-10 19:12:32', '2025-11-10 19:12:32'),
(8, 9, '+queperros', 'wedvbdebb', NULL, 3, 'EN_FORMULACION', '2026-01-19', '2026-03-14', '2025-11-10 19:12:53', '2025-11-10 19:12:53'),
(9, 9, '+queperros', 'dsv dbdeberb', NULL, 3, 'FINALIZADO', '2026-02-13', '2026-04-03', '2025-11-10 19:13:14', '2025-11-10 19:13:14'),
(10, 7, 'Nombre de Ejemplo de proyecto', 'Descripción  de Ejemplo de proyecto', NULL, 3, 'EN_FORMULACION', '2025-11-12', '2025-11-30', '2025-11-12 14:49:14', '2025-11-12 14:49:14'),
(11, 7, 'Nombre de Ejemplo de proyecto222', 'descripción  de Ejemplo de proyecto2', NULL, 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:49:59', '2025-11-12 15:52:26'),
(12, 7, 'aaa123', 'aaa123', NULL, 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:50:26', '2025-11-12 14:50:26'),
(13, 7, 'Proyecto Alcón', 'abc es', NULL, 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:50:42', '2025-11-12 15:45:25'),
(14, 9, 'semilleross', 'plataforma', '[2025-12-05 15:34] joaquin cañon: el que lo lea es puto', 3, 'EN_EJECUCION', '2025-12-03', '2026-01-03', '2025-12-03 15:49:38', '2025-12-05 20:34:31'),
(15, 18, '+queperros', 'primera fase en desarrollo de pagina web', NULL, 3, 'EN_EJECUCION', '2025-11-21', '2026-02-26', '2026-02-10 16:23:47', '2026-02-10 16:23:47'),
(16, 18, 'preicfes', 'Fase 1 de desarrollo de pagina web', NULL, 3, 'EN_EJECUCION', '2025-12-20', '2026-02-28', '2026-02-10 16:26:14', '2026-02-10 16:26:14'),
(17, 18, 'hija de fuego', 'desarrollo fase 1 de pagina web', NULL, 3, 'EN_EJECUCION', '2025-11-10', '2026-03-10', '2026-02-10 16:26:58', '2026-02-10 16:26:58'),
(18, 18, 'Masa madre', 'desarrollo de videojuego y cartilla fase 1', NULL, 3, 'FINALIZADO', '2025-07-10', '2025-12-30', '2026-02-10 16:30:13', '2026-02-10 16:30:13'),
(19, 18, 'system school', 'desarrollo de pagina web', NULL, 3, 'FINALIZADO', '2025-10-10', '2025-12-30', '2026-02-10 16:31:25', '2026-02-10 16:31:25'),
(20, 18, 'SinditSena', 'Fase 1 de desarollo web', NULL, 3, 'EN_EJECUCION', '2025-10-10', '2026-03-14', '2026-02-10 16:48:28', '2026-02-10 16:48:28'),
(21, 18, 'Repuestos', 'Fase 1 en desarrollo de pagina web', NULL, 3, 'EN_FORMULACION', '2025-12-12', '2026-04-20', '2026-02-10 16:49:04', '2026-02-10 16:49:04'),
(22, 18, 'ParKareas', 'Fase 1 desarrollo de pagina web y aplicación móvil', NULL, 3, 'EN_EJECUCION', '2025-10-13', '2026-03-15', '2026-02-10 16:49:57', '2026-02-10 16:50:19'),
(23, 18, 'laboratorio', 'Fase 1 de desarrollo pagina web', NULL, 3, 'EN_EJECUCION', '2025-11-24', '2025-12-28', '2026-02-10 16:51:00', '2026-02-10 16:51:17'),
(24, 18, 'Zapatillas', 'Fase 1 de desarrollo web', NULL, 3, 'FINALIZADO', '2025-10-19', '2025-12-30', '2026-02-10 16:52:13', '2026-02-10 16:52:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_user`
--

CREATE TABLE `proyecto_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id_recurso` bigint(20) UNSIGNED NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `tipo_recurso` enum('PDF','WORD','EXCEL','PRESENTACION','VIDEO','IMAGEN','ENLACE') NOT NULL DEFAULT 'PDF',
  `categoria` enum('plantillas','manuales','otros') NOT NULL DEFAULT 'otros',
  `tipo_documento` varchar(50) DEFAULT NULL,
  `dirigido_a` enum('todos','aprendices','lideres') NOT NULL DEFAULT 'todos',
  `visible_aprendices` tinyint(1) DEFAULT 0,
  `semillero_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_asignacion` datetime DEFAULT current_timestamp(),
  `fecha_lectura` datetime DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lider_general_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lider_semillero_id` bigint(20) UNSIGNED DEFAULT NULL,
  `proyecto_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `archivo_respuesta` varchar(255) DEFAULT NULL,
  `enlace_respuesta` varchar(255) DEFAULT NULL,
  `respondido_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id_recurso`, `nombre_archivo`, `archivo`, `tipo_recurso`, `categoria`, `tipo_documento`, `dirigido_a`, `visible_aprendices`, `semillero_id`, `estado`, `fecha_asignacion`, `fecha_lectura`, `fecha_vencimiento`, `descripcion`, `comentarios`, `user_id`, `lider_general_id`, `lider_semillero_id`, `proyecto_id`, `created_at`, `updated_at`, `respuesta`, `archivo_respuesta`, `enlace_respuesta`, `respondido_en`) VALUES
(12, 'manual', 'multimedia/jxeo1PfwRiAyYLFes8SCaHzTF6SjtdWJP5OtGJLt.pdf', 'PDF', 'manuales', NULL, 'todos', 0, NULL, 'pendiente', '2026-02-10 14:36:28', NULL, NULL, 'invito a leer el manual de masa madre', NULL, 130, NULL, NULL, NULL, '2026-02-10 19:36:28', '2026-02-10 19:36:28', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_roles` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_roles`, `nombre`) VALUES
(1, 'ADMIN'),
(4, 'APRENDIZ'),
(3, 'LIDER_INVESTIGACION'),
(2, 'LIDER_SEMILLERO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semilleros`
--

CREATE TABLE `semilleros` (
  `id_semillero` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `linea_investigacion` varchar(255) DEFAULT NULL,
  `id_lider_semi` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `semilleros`
--

INSERT INTO `semilleros` (`id_semillero`, `nombre`, `linea_investigacion`, `id_lider_semi`, `created_at`, `updated_at`) VALUES
(6, 'Bioprocesos y Biotecnología Aplicada (BIBA)', 'Ciencias Aplicadas en Desarrollo Ambiental', NULL, '2025-10-27 18:08:12', '2025-12-26 19:01:28'),
(7, 'Administración y Salud, Deportes y Bienestar', 'Administración en Salud, Deportes y Bienestar', NULL, '2025-10-27 18:08:12', '2025-12-09 13:08:39'),
(8, 'Agroindustria Seguridad Alimentaria', 'Seguridad Alimentaria', 117, '2025-10-27 18:08:12', '2025-12-26 19:01:20'),
(9, 'Grupo de Estudio de Desarrollo de Software (GEDS)', 'Telecomunicaciones y Tecnologías Virtuales', 109, '2025-10-27 18:08:12', '2025-11-11 23:18:34'),
(10, 'Investigación de Mercados para las Mipymes (INVERPYMES)', 'Comercio y Servicios para el Desarrollo Empresarial', NULL, '2025-10-27 18:08:12', '2025-12-26 19:07:28'),
(11, 'Materiales, Procesos de Manufactura y Automatización (MAPRA)', 'Diseño, Ingeniería y Mecatrónica', 127, '2025-10-27 18:08:12', '2025-12-26 19:07:34'),
(12, 'Micronanotec', 'Integración de tecnologías convergentes para el mejoramiento de la calidad de vida', NULL, '2025-10-27 18:08:12', '2025-12-26 19:07:42'),
(13, 'Desarrollo de Videojuegos Serios', 'Telecomunicaciones y Tecnologías Virtuales', NULL, '2025-10-27 18:08:12', '2025-12-26 19:03:34'),
(14, 'PICIDE (Pedagogía)', 'Ciencias Sociales y Ciencias de la Educación', NULL, '2025-10-27 18:08:13', '2025-12-26 19:09:00'),
(18, 'TICS INTELIGENCIA ARTIFICIAL', 'tecnologia', 131, '2026-02-10 15:57:56', '2026-02-10 15:57:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('cQCCQxvmXjemtJOCMGa1nJTz97U77zgnqdSz5xvC', 58, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic3JuMHpMMHBJb2RGOGw2d05WTUVYdWNIcW5ZSzhXS0RlZW5YQW5ZWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9saWRlcl9zZW1pL3NlbWlsbGVyb3MiO3M6NToicm91dGUiO3M6MjE6ImxpZGVyX3NlbWkuc2VtaWxsZXJvcyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU4O30=', 1764336386);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `documento` varchar(40) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `genero` enum('HOMBRE','MUJER','NO DEFINIDO') DEFAULT NULL,
  `tipo_rh` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `role` varchar(255) NOT NULL DEFAULT 'APRENDIZ',
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_lc` varchar(255) GENERATED ALWAYS AS (lcase(`email`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `apellidos`, `tipo_documento`, `documento`, `celular`, `genero`, `tipo_rh`, `email`, `email_verified_at`, `password`, `must_change_password`, `role`, `remember_token`, `last_login_at`, `created_at`, `updated_at`) VALUES
(8, 'Joaquin cañon', NULL, NULL, NULL, NULL, NULL, NULL, 'test3@gmail.com', NULL, '$2y$12$CysY7mh6WuCxIc.j4vORxuqAEPzjDJr0lxxqSo.Q.8B0Q9caCicLW', 0, 'APRENDIZ', NULL, '2025-12-26 14:25:20', '2025-10-16 01:50:17', '2025-12-26 14:25:20'),
(83, 'joaquin', 'cañon', 'CC', '1872673162', '3298746855', 'HOMBRE', 'O+', 'admin1@gmail.com', NULL, '$2y$12$utdWRjJRhUNiF2HRDzDBKuWLhdcrlX6bNPuXE//Hh5L2rSNafvdVi', 0, 'ADMIN', NULL, '2026-02-10 14:51:13', '2025-11-18 13:52:56', '2026-02-10 14:51:13'),
(103, 'Juan', 'Pérez', 'CC', '10987667', '3116658900', 'MUJER', 'O+', 'han@gmail.com', NULL, '$2y$12$7TlJHYAhe3bG943/j7rd5OyRLRO.B8NYXOaRkg.iUuPsDdnEcG4JG', 0, 'APRENDIZ', NULL, NULL, '2025-12-02 21:38:32', '2025-12-02 21:38:32'),
(109, 'joaquinillo', 'pepinillo', 'TI', '61671165621', '3262671781', 'NO DEFINIDO', 'B-', 'jua@gmail.com', NULL, '$2y$12$rhn4B.SvFjLL91KDkhb.uujvRYfuv9zWrziMgeYVjKorFguTzxSJO', 0, 'LIDER_SEMILLERO', NULL, '2026-02-10 14:33:20', '2025-12-03 15:24:42', '2026-02-10 14:33:20'),
(110, 'june', 'ken', 'CC', '88337738', '32098763847', 'MUJER', 'A-', 'lleno@gmail.com', NULL, '$2y$12$.WPJ/Vml3TZ2x3.3AzqKIeq6Zz9Nh5WFCvTwn6wnAIZWNzTweyqRG', 0, 'LIDER_INVESTIGACION', NULL, NULL, '2025-12-03 15:57:53', '2025-12-03 15:57:53'),
(111, 'Ana', 'Gómez', 'CC', '10862676276', '3456789045', 'HOMBRE', 'AB+', 'kiss@gmail.com', NULL, '$2y$12$7EnNJUpwztKcTFYkmrrm.OUCynbNGNlu41FeYn9kMyiN.Jm0dCK7W', 0, 'APRENDIZ', NULL, '2025-12-18 16:52:02', '2025-12-05 18:15:17', '2025-12-18 16:52:02'),
(112, 'Luis', 'Martínez', 'TI', '2761763172', '3276547899', 'HOMBRE', 'B+', 'iva@gmail.com', NULL, '$2y$12$bm9oCcODgb6lOD5L0XCDfu1gILnzX0eYDKkdayoNA6/nBqr.3p4Mm', 0, 'APRENDIZ', NULL, '2026-01-15 16:43:58', '2025-12-05 18:22:04', '2026-01-15 16:43:58'),
(117, 'kika', 'nieto', 'CC', '4234234', '3245435433', 'MUJER', 'B+', 'kia@gmail.com', NULL, '$2y$12$zoDc8jFQp2Ojhq3qWAAWve64AViTc4.w96zpxRWB.KYWcYZvo.b92', 0, 'LIDER_SEMILLERO', NULL, NULL, '2025-12-10 13:16:16', '2025-12-10 13:16:16'),
(118, 'Jeremias', 'salgado', 'TI', '6556756667', '321233489', 'MUJER', 'B-', 'jere@gmail.com', NULL, '$2y$12$NVXo34L.8Swtt0zDs6QTOOBbglTvekztvJLlenmRGhYO5W8ilZDn6', 0, 'LIDER_INVESTIGACION', NULL, NULL, '2025-12-10 13:19:27', '2025-12-10 13:19:27'),
(119, 'Sara', 'Rojas', 'CC', '17627637621', '32462362732', 'MUJER', 'O+', 'lu@gmail.com', NULL, '$2y$12$XzVwJXbQpodW.c7T8WDLMeOatwc.FBd39sQrthj8MfpHZslQt3sDu', 0, 'APRENDIZ', NULL, NULL, '2025-12-10 13:20:58', '2025-12-10 13:20:58'),
(121, 'guz', 'guz', 'TI', '1232132', '3214232211', 'HOMBRE', 'AB+', 'guz@gmail.com', NULL, '$2y$12$xaW.XqZyumjefFEi5DYaOuHYE9T787Txm6XpsbuZUUy.7.AfVunSe', 0, 'APRENDIZ', NULL, NULL, '2025-12-17 15:38:30', '2025-12-17 15:38:30'),
(127, 'valu', 'sal', 'PERMISO ESPECIAL', '793847374', '323993399', 'MUJER', 'B+', 'valu@gamil.com', NULL, '$2y$12$4v9ZZ2FOaG/2hrMFs8pOI.NKk0pLQ3Lqz.HDmYj2nVWiv0h5M8DUy', 0, 'LIDER_SEMILLERO', NULL, NULL, '2025-12-26 19:07:11', '2025-12-26 19:07:11'),
(129, 'jim', 'toner', 'TI', '123456789', '324345645', NULL, 'B-', 'semi@gmail.com', NULL, '$2y$12$Zc67o8e7DvLn27OQT4sSwu5OS1fKfmt0N4VnupywLXgxWCINTpVNm', 1, 'LIDER_SEMILLERO', NULL, '2026-01-13 21:37:14', '2026-01-10 00:41:15', '2026-01-13 21:37:14'),
(130, 'Carolina', 'Cardenas', 'CC', '10123456789', '3245679844', 'MUJER', 'O+', 'carolina@gmail.com', NULL, '$2y$12$dOvYwCxceA2/5FkMjZ/vs..b8SbVoIfixZrn17xVSo/t9qE9MSCxK', 0, 'ADMIN', NULL, '2026-02-10 19:35:16', '2026-02-10 15:52:11', '2026-02-10 19:35:16'),
(131, 'Wilson', 'Quintero', 'CC', '1023456789', '3214567882', 'HOMBRE', 'B+', 'wilson@gmail.com', NULL, '$2y$12$fzRtTO2aM8XsKvL19Qd06uuCuKlBH/GO3TweN2deVp7vbbGLaWcsC', 0, 'LIDER_SEMILLERO', NULL, '2026-02-10 19:36:47', '2026-02-10 15:56:51', '2026-02-10 19:36:47'),
(132, 'Hansbleidi', 'Cardenas', 'CC', '1071542882', '3126789838', 'MUJER', 'O+', 'hans@gmail.com', NULL, '$2y$12$gc1L0/gW/qPiz1tOKINRFO6YJ32Pe6K2z9AtYTKYklsWwh8XK0492', 0, 'APRENDIZ', NULL, '2026-02-10 18:09:57', '2026-02-10 16:20:51', '2026-02-10 18:10:20'),
(133, 'Joaquin Daniel', 'Cañon Forero', 'CC', '1098273645', '3126258733', 'HOMBRE', 'O+', 'joaquin@gmail.com', NULL, '$2y$12$h/C0CnUCNUG4m4OFXsUkWuhrVDbsFRVRCbai2DbkbZrdv2NSLSKFO', 1, 'APRENDIZ', NULL, NULL, '2026-02-10 16:34:34', '2026-02-10 16:34:34'),
(134, 'Geraldine', 'Fajardo', 'CC', '12365478', '3245679845', 'MUJER', 'O+', 'geral@gmail.com', NULL, '$2y$12$HeU9iDgQVZTxcMoIB.CYz.HDSMjdb0Db65gblJgquXTOPFu9OM.yG', 1, 'APRENDIZ', NULL, NULL, '2026-02-10 16:36:23', '2026-02-10 16:36:23'),
(135, 'Kevin', 'Ruiz', 'CC', '2523544', '3256789034', 'HOMBRE', 'O+', 'kevin@gmail.com', NULL, '$2y$12$C7ONuOiVflL1FDOmPTj5fuue5YNabzmPc3/cu2Vu2O4QXQCiC891G', 1, 'APRENDIZ', NULL, NULL, '2026-02-10 16:38:05', '2026-02-10 16:38:05'),
(136, 'Dayana', 'Lopez', 'CC', '34672892', '3245728390', 'MUJER', 'A+', 'dayana@gmail.com', NULL, '$2y$12$cE/3qrawzwI79b4Ze5CVp.emWPCvcCAf1StbJGYgZAzrK1LkYuS9C', 1, 'APRENDIZ', NULL, NULL, '2026-02-10 16:39:52', '2026-02-10 16:39:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_module_permissions`
--

CREATE TABLE `user_module_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `module` varchar(100) NOT NULL,
  `can_create` tinyint(1) NOT NULL DEFAULT 0,
  `can_update` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_module_permissions`
--

INSERT INTO `user_module_permissions` (`id`, `user_id`, `module`, `can_create`, `can_update`, `can_delete`, `created_at`, `updated_at`) VALUES
(19, 69, 'semilleros', 1, 1, 1, '2025-11-13 05:54:50', '2025-11-13 05:54:50'),
(20, 69, 'usuarios', 1, 1, 1, '2025-11-13 05:54:50', '2025-11-13 05:54:50'),
(21, 69, 'recursos', 1, 1, 1, '2025-11-13 05:54:50', '2025-11-13 05:54:50'),
(22, 69, 'reuniones-lideres', 1, 1, 1, '2025-11-13 05:54:50', '2025-11-13 05:54:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD PRIMARY KEY (`id_aprendiz`),
  ADD UNIQUE KEY `uk_aprendiz_user` (`user_id`),
  ADD KEY `idx_ficha` (`ficha`),
  ADD KEY `idx_correo_institucional` (`correo_institucional`),
  ADD KEY `aprendices_semillero_fk` (`semillero_id`);

--
-- Indices de la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aprendiz` (`id_aprendiz`),
  ADD KEY `id_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD KEY `idx_documentos_tipo_estado` (`tipo_archivo`,`estado`),
  ADD KEY `fk_documentos_aprendices` (`id_aprendiz`),
  ADD KEY `fk_documentos_proyectos` (`id_proyecto`);
ALTER TABLE `documentos` ADD FULLTEXT KEY `ft_documentos` (`titulo_avance`,`descripcion_avance`,`descripcion`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD UNIQUE KEY `eventos_lider_fecha_unique` (`id_lider_semi`,`id_proyecto`,`fecha_hora`),
  ADD KEY `idx_eventos_fecha` (`fecha_hora`),
  ADD KEY `idx_eventos_admin_fecha` (`id_admin`,`fecha_hora`),
  ADD KEY `idx_eventos_lider_fecha` (`id_lider_semi`,`fecha_hora`),
  ADD KEY `eventos_codigo_reunion_index` (`codigo_reunion`),
  ADD KEY `fk_eventos_proyecto` (`id_proyecto`),
  ADD KEY `fk_eventos_creado_por` (`creado_por`);
ALTER TABLE `eventos` ADD FULLTEXT KEY `ft_eventos` (`titulo`,`descripcion`);

--
-- Indices de la tabla `evento_asignaciones`
--
ALTER TABLE `evento_asignaciones`
  ADD PRIMARY KEY (`id_asignaciones`),
  ADD KEY `fk_evento_asignaciones_evento` (`evento_id`),
  ADD KEY `fk_evento_asignaciones_asignado_por` (`asignado_por`);

--
-- Indices de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ep_evento_aprendiz` (`id_evento`,`id_aprendiz`),
  ADD UNIQUE KEY `uq_ep_evento_lider` (`id_evento`,`id_lider_semi`),
  ADD KEY `idx_ep_aprendiz` (`id_aprendiz`),
  ADD KEY `idx_ep_lider` (`id_lider_semi`);

--
-- Indices de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `evidencias_id_usuario_index` (`id_usuario`),
  ADD KEY `fk_evidencias_proyecto` (`proyecto_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lideres_investigacion`
--
ALTER TABLE `lideres_investigacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD PRIMARY KEY (`id_lider_semi`),
  ADD KEY `fk_lider_usuario` (`id_usuario`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD KEY `fk_proyecto_semillero` (`id_semillero`),
  ADD KEY `idx_proyectos_semillero` (`id_semillero`);
ALTER TABLE `proyectos` ADD FULLTEXT KEY `ft_proyectos` (`nombre_proyecto`,`descripcion`);

--
-- Indices de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `proyecto_user_id_proyecto_user_id_unique` (`id_proyecto`,`user_id`);

--
-- Indices de la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`id_recurso`),
  ADD KEY `fk_recursos_user` (`user_id`),
  ADD KEY `fk_recurso_lider_general` (`lider_general_id`),
  ADD KEY `fk_recurso_lider_semillero` (`lider_semillero_id`),
  ADD KEY `fk_recurso_proyecto` (`proyecto_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_roles`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD PRIMARY KEY (`id_semillero`),
  ADD UNIQUE KEY `uk_semilleros_nombre` (`nombre`),
  ADD UNIQUE KEY `uk_semilleros_lider` (`id_lider_semi`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `uq_users_email_lc` (`email_lc`);

--
-- Indices de la tabla `user_module_permissions`
--
ALTER TABLE `user_module_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_module_permissions_user_id_module_unique` (`user_id`,`module`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_user_roles_role` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id_aprendiz` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `evento_asignaciones`
--
ALTER TABLE `evento_asignaciones`
  MODIFY `id_asignaciones` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  MODIFY `id_evidencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lideres_investigacion`
--
ALTER TABLE `lideres_investigacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id_recurso` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  MODIFY `id_semillero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de la tabla `user_module_permissions`
--
ALTER TABLE `user_module_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `administradores_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD CONSTRAINT `aprendices_semillero_fk` FOREIGN KEY (`semillero_id`) REFERENCES `semilleros` (`id_semillero`),
  ADD CONSTRAINT `fk_aprendices_user_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  ADD CONSTRAINT `aprendiz_proyecto_ibfk_1` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_aprendiz`),
  ADD CONSTRAINT `aprendiz_proyecto_ibfk_2` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`);

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_docs_aprendiz` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_aprendiz`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_docs_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `fk_eventos_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_eventos_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento_asignaciones`
--
ALTER TABLE `evento_asignaciones`
  ADD CONSTRAINT `fk_evento_asignaciones_asignado_por` FOREIGN KEY (`asignado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_evento_asignaciones_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD CONSTRAINT `fk_ep_aprendiz` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_aprendiz`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ep_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ep_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD CONSTRAINT `fk_evidencias_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evidencias_user` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `lideres_investigacion`
--
ALTER TABLE `lideres_investigacion`
  ADD CONSTRAINT `lideres_investigacion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD CONSTRAINT `fk_lider_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `fk_recurso_lider_general` FOREIGN KEY (`lider_general_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_recurso_lider_semillero` FOREIGN KEY (`lider_semillero_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_recurso_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id_proyecto`),
  ADD CONSTRAINT `fk_recursos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `recursos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD CONSTRAINT `fk_semillero_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_semilleros_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_module_permissions`
--
ALTER TABLE `user_module_permissions`
  ADD CONSTRAINT `user_module_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_roles`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
