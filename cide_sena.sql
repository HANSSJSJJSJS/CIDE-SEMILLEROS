-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2025 a las 22:09:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cide_sena`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_usuario`, `nombre`, `nombres`, `apellidos`, `creado_en`, `actualizado_en`) VALUES
(43, '', 'Sergio', 'nova', '2025-10-22 21:29:19', '2025-10-22 21:29:19'),
(47, '', 'maria', 'nova', '2025-10-22 23:53:38', '2025-10-22 23:53:38'),
(49, '', 'LOREE', 'nova', '2025-10-23 02:40:11', '2025-10-23 02:40:11'),
(69, '', 'luis', 'garcia', '2025-10-30 00:29:35', '2025-10-30 00:29:35'),
(83, '', 'joaquin', 'cañon', '2025-11-18 13:52:56', '2025-11-18 13:52:56'),
(92, 'conan Morita', 'conan', 'Morita', '2025-11-28 18:20:10', '2025-11-28 18:20:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `ficha` varchar(30) DEFAULT NULL,
  `programa` varchar(160) DEFAULT NULL,
  `nivel_educativo` enum('ARTICULACION_MEDIA_10_11','TECNOACADEMIA_7_9','TECNICO','TECNOLOGO','PROFESIONAL') DEFAULT NULL,
  `vinculado_sena` tinyint(1) NOT NULL DEFAULT 1,
  `institucion` varchar(160) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `correo_personal` varchar(160) DEFAULT NULL,
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

INSERT INTO `aprendices` (`id_aprendiz`, `user_id`, `nombres`, `apellidos`, `ficha`, `programa`, `nivel_educativo`, `vinculado_sena`, `institucion`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `semillero_id`, `creado_en`, `actualizado_en`, `estado`) VALUES
(62, NULL, 'Laura', 'García Pérez', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'laura.garcia@misena.edu.co', 'laura.garcia@gmail.com', 'Marta Pérez', '3101111111', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(63, NULL, 'Carlos', 'Hernández Ruiz', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'carlos.hernandez@misena.edu.co', 'carlos.hernandez@gmail.com', 'Juan Ruiz', '3102222222', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(64, NULL, 'María', 'López Díaz', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'maria.lopez@misena.edu.co', 'maria.lopez@gmail.com', 'Ana Díaz', '3103333333', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(65, NULL, 'Andrés', 'Torres Gómez', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'andres.torres@misena.edu.co', 'andres.torres@gmail.com', 'Laura Gómez', '3104444444', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(66, NULL, 'Camila', 'Martínez Rojas', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'camila.martinez@misena.edu.co', 'camila.martinez@gmail.com', 'Rosa Rojas', '3105555555', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(67, NULL, 'Felipe', 'Gutiérrez Ramos', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'felipe.gutierrez@misena.edu.co', 'felipe.gutierrez@gmail.com', 'Marta Ramos', '3106666666', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(68, NULL, 'Diana', 'Morales Castillo', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'diana.morales@misena.edu.co', 'diana.morales@gmail.com', 'Carlos Castillo', '3107777777', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(69, 8, 'Santiago', 'Jiménez Herrera', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'santiago.jimenez@misena.edu.co', 'santiago.jimenez@gmail.com', 'María Herrera', '3108888888', NULL, '2025-11-11 18:29:17', '2025-11-11 18:29:17', 'Activo'),
(70, 9, 'Valentina', 'Ruiz Cabrera', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'valentina.ruiz@misena.edu.co', 'valentina.ruiz@gmail.com', 'Lucía Cabrera', '3109999999', NULL, '2025-11-11 18:29:17', '2025-11-11 18:29:17', 'Activo'),
(71, NULL, 'Mateo', 'Castro Peña', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'mateo.castro@misena.edu.co', 'mateo.castro@gmail.com', 'José Peña', '3110000000', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(72, NULL, 'Paula', 'Rodríguez León', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'paula.rodriguez@misena.edu.co', 'paula.rodriguez@gmail.com', 'María León', '3111111111', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(73, NULL, 'Juan', 'Vargas Ortiz', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'juan.vargas@misena.edu.co', 'juan.vargas@gmail.com', 'Carmen Ortiz', '3112222222', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(74, NULL, 'Isabella', 'Mendoza Suárez', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'isabella.mendoza@misena.edu.co', 'isabella.mendoza@gmail.com', 'Julio Suárez', '3113333333', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(75, NULL, 'Sebastián', 'Gómez Vera', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'sebastian.gomez@misena.edu.co', 'sebastian.gomez@gmail.com', 'Andrés Vera', '3114444444', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(76, NULL, 'Sara', 'Ramírez Patiño', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'sara.ramirez@misena.edu.co', 'sara.ramirez@gmail.com', 'Claudia Patiño', '3115555555', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(77, NULL, 'Daniel', 'Córdoba Mejía', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'daniel.cordoba@misena.edu.co', 'daniel.cordoba@gmail.com', 'Sandra Mejía', '3116666666', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(78, NULL, 'Lucía', 'Pérez Torres', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'lucia.perez@misena.edu.co', 'lucia.perez@gmail.com', 'Mario Torres', '3117777777', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(79, NULL, 'Tomás', 'Martínez Ospina', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'tomas.martinez@misena.edu.co', 'tomas.martinez@gmail.com', 'Luisa Ospina', '3118888888', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(80, NULL, 'Natalia', 'Reyes Gómez', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'natalia.reyes@misena.edu.co', 'natalia.reyes@gmail.com', 'Felipe Gómez', '3119999999', NULL, '2025-11-11 18:29:17', '2025-11-18 18:46:20', 'Activo'),
(81, 20, 'David', 'Moreno Silva', '258963', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'david.moreno@misena.edu.co', 'david.moreno@gmail.com', 'Sofía Silva', '3120000000', NULL, '2025-11-11 18:29:17', '2025-11-11 18:29:17', 'Activo'),
(82, 71, 'jubhb', 'jhvv', 'mi999990', 'vgvhgcfc', NULL, 1, NULL, 'khbhbkh@gmail.com', 'hidalgo.16@gmail.com', 'hgyugy', '987897987', NULL, '2025-11-11 23:37:32', '2025-11-11 23:37:32', 'Activo'),
(83, 72, 'Juan', 'Pérez López', '2456789', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'juan.perez@misena.edu.co', 'juan.perez@misena.edu.co', 'María López', '3119876543', NULL, '2025-11-12 18:18:57', '2025-11-12 18:18:57', 'Activo'),
(84, 73, 'Laura', 'Gómez Rincón', '2456790', 'Gestión Administrativa', NULL, 1, NULL, 'laura.gomez@misena.edu.co', 'laura.gomez@misena.edu.co', 'Jorge Gómez', '3124567891', NULL, '2025-11-12 18:20:35', '2025-11-12 18:20:35', 'Activo'),
(85, 74, 'Carlos', 'Méndez Silva', '2456791', 'Mantenimiento Electromecánico', NULL, 1, NULL, 'carlos.mendez@misena.edu.co', 'carlos.mendez@misena.edu.co', 'Ana Silva', '3136547890', NULL, '2025-11-12 18:21:57', '2025-11-12 18:21:57', 'Activo'),
(86, 75, 'Andrea', 'Díaz', '2456792', 'Contabilidad y Finanzas', NULL, 1, NULL, 'andrea.moreno@misena.edu.co', 'andrea.moreno@misena.edu.co', 'Luis Moreno', '3115678923', NULL, '2025-11-12 18:23:31', '2025-11-12 18:23:31', 'Activo'),
(87, 76, 'Kevin', 'levinin', '3548751', 'ADSO', NULL, 1, NULL, 'kevin@hsena.com', 'kevinsan@hotmail.com', 'maria', '21641561', 13, '2025-11-12 22:39:15', '2025-11-12 22:39:15', 'Activo'),
(88, 77, 'Mario', 'nova', '3548751', 'Adso', NULL, 1, NULL, 'marionova@hotmail.com', 'aprendiz@hotmail.com', 'aasd', '21641561', 13, '2025-11-13 00:45:01', '2025-11-13 00:45:01', 'Activo'),
(89, 82, 'cangiro', 'Sáenz', NULL, NULL, NULL, 0, 'Manuela', 'cangiro@hsena.com', 'aprendizsinficha@hola.com', 'jose', '21641561', 6, '2025-11-13 01:06:03', '2025-11-13 01:06:03', 'Activo'),
(90, 84, 'Sergio', 'mogoyon', '3548751', 'Deporte', NULL, 1, NULL, 'serm@hsena.com', 'sergius@gmail.com', 'aasd', '21641561', 7, '2025-11-18 13:54:41', '2025-11-18 13:54:41', 'Activo'),
(91, 85, 'geral', 'fajardo', '9814728', 'Animacion 3D', NULL, 1, NULL, 'example@gmail.com', 'example@mi.sena.edu.co', 'Madre', 'SOLO HAY UNA', 9, '2025-11-19 13:12:42', '2025-11-19 13:12:42', 'Activo'),
(92, 86, 'jhon', 'cortes', '2872387', 'Análisis y Desarrollo de Software', NULL, 1, NULL, 'example2@gmail.com', 'example2@soy.sena.edu.co', 'la mama', 'pa siempre', 9, '2025-11-19 13:39:28', '2025-11-19 13:39:28', 'Activo'),
(93, 87, 'Santiago', 'Aves', '982734893', 'Animacion 3D', NULL, 1, NULL, 'example3@gmail.com', 'example3@soy.sena.edu.co', 'el papa', 'lo abandono', 9, '2025-11-19 13:40:32', '2025-11-19 13:40:32', 'Activo'),
(94, 88, 'jhonnatan', 'torres', '254426554', 'adso', NULL, 1, NULL, 'asdadsj@hotmail', 'kg22@gmail.com', 'asda', 'sumakmsa', 9, '2025-11-20 18:49:06', '2025-11-28 14:31:05', 'Activo');

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
(2, 82, 6),
(3, 82, 7),
(4, 82, 8),
(5, 69, 9),
(7, 85, 6),
(8, 84, 6),
(13, 69, 10),
(14, 85, 10),
(15, 83, 10),
(16, 82, 10),
(17, 85, 13),
(18, 90, 12),
(19, 89, 6),
(20, 69, 6),
(21, 91, 7),
(22, 92, 7),
(23, 86, 7),
(24, 85, 7),
(25, 94, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `titulo_avance` varchar(255) DEFAULT NULL,
  `descripcion_avance` text DEFAULT NULL,
  `documento` varchar(255) DEFAULT NULL,
  `ruta_archivo` varchar(1000) DEFAULT NULL,
  `tipo_archivo` enum('PDF','WORD','PRESENTACION','VIDEO','IMAGEN','ENLACE','OTRO') NOT NULL,
  `enlace_evidencia` varchar(1000) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `fecha_subido` datetime DEFAULT current_timestamp(),
  `fecha_avance` date DEFAULT NULL,
  `tamanio` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_subida` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_limite` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id_documento`, `id_proyecto`, `id_aprendiz`, `titulo_avance`, `descripcion_avance`, `documento`, `ruta_archivo`, `tipo_archivo`, `enlace_evidencia`, `tipo_documento`, `mime_type`, `fecha_subido`, `fecha_avance`, `tamanio`, `fecha_subida`, `estado`, `fecha_limite`, `descripcion`) VALUES
(1, 12, 90, NULL, NULL, 'ejemplo entrega', 'documentos/1763479448_90_Plantilla_cide2.docx', 'PDF', NULL, 'pdf', '', '2025-11-18 09:06:26', NULL, 107498, '2025-11-18 10:24:08', 'aprobado', '2025-11-18', 'manual'),
(2, 12, 90, NULL, NULL, 'ejemplo entrega', 'documentos/1763475058_90_manualejemplo2.pdf', 'PDF', NULL, 'pdf', '', '2025-11-18 09:07:11', NULL, 272439, '2025-11-18 09:10:58', 'aprobado', '2025-11-29', 'manual  empresarial'),
(3, 12, 90, NULL, NULL, 'manual', 'documentos/1763474949_90_manualejemplo2.pdf', 'PDF', NULL, NULL, NULL, '2025-11-18 09:09:09', NULL, 272439, '2025-11-18 09:09:09', 'aprobado', NULL, NULL),
(4, 12, 90, NULL, NULL, 'ejemplo entrega 1.1.1', 'documentos/1763479325_90_manualejemplo2.pdf', 'PDF', NULL, 'pdf', '', '2025-11-18 10:10:44', NULL, 272439, '2025-11-18 10:22:05', 'aprobado', '2025-11-29', 'entrega de proyecto final'),
(5, 7, 94, NULL, NULL, 'manual', 'documentos/1763664924_94_Lideres de Semilleros CIDE 2025.pdf', 'PDF', NULL, 'pdf', '', '2025-11-20 13:52:51', NULL, 602030, '2025-11-20 13:55:24', 'rechazado', '2025-11-22', 'maluma'),
(6, 7, 94, NULL, NULL, 'manual', 'documentos/1763664916_94_constancia_complementaria.pdf', 'PDF', NULL, 'pdf', '', '2025-11-20 13:53:18', NULL, 36306, '2025-11-20 13:55:16', 'aprobado', '2025-11-23', 'mau');

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
  `linea_investigacion` varchar(255) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion` int(11) NOT NULL DEFAULT 60,
  `ubicacion` varchar(255) DEFAULT NULL,
  `link_virtual` varchar(255) DEFAULT NULL,
  `codigo_reunion` varchar(255) DEFAULT NULL,
  `recordatorio` varchar(255) NOT NULL DEFAULT 'none',
  `tipo` varchar(255) NOT NULL DEFAULT 'reunion',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `id_lider_semi`, `id_admin`, `id_proyecto`, `titulo`, `descripcion`, `linea_investigacion`, `fecha_hora`, `duracion`, `ubicacion`, `link_virtual`, `codigo_reunion`, `recordatorio`, `tipo`, `created_at`, `updated_at`) VALUES
(1, 70, NULL, NULL, 'AVANECE', NULL, '', '2025-11-19 14:00:00', 60, 'virtual', 'https://teams.live.com/meet/9384643488347?p=oddDzjUY8wqa2wF0Tb', 'xPPcCdA6r2', '15', 'entrega', '2025-11-05 21:16:13', '2025-11-06 01:59:32'),
(2, 70, NULL, 2, 'fbrrdbre', NULL, '', '2025-11-14 10:00:00', 60, 'virtual', 'https://teams.live.com/meet/9384643488347?p=oddDzjUY8wqa2wF0Tb', 'le2ZvyymMg', '30', 'seguimiento', '2025-11-06 01:59:54', '2025-11-10 18:51:26'),
(3, 70, NULL, NULL, 'AVANECE', NULL, '', '2025-11-10 09:00:00', 60, 'virtual', 'https://teams.live.com/meet/9384643488347?p=oddDzjUY8wqa2wF0Tb', 'k7uzqN6mNX', '0', 'seguimiento', '2025-11-06 02:02:18', '2025-11-10 18:51:36'),
(4, 70, NULL, NULL, 'AVANECE', NULL, '', '2025-11-21 08:00:00', 60, 'virtual', NULL, '1hYwdahIeK', '0', 'seguimiento', '2025-11-06 02:02:40', '2025-11-10 18:31:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_participantes`
--

CREATE TABLE `evento_participantes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `id_lider_semi` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
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
(1, 89, 0, '2025-11-21 20:14:35', '2025-11-24 15:18:55'),
(2, 95, 0, '2025-11-28 18:44:12', '2025-11-28 18:44:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_lider_semi` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_semillero` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lideres_semillero`
--

INSERT INTO `lideres_semillero` (`id_lider_semi`, `nombres`, `apellidos`, `correo_institucional`, `creado_en`, `actualizado_en`, `id_semillero`) VALUES
(57, 'Carlos Hernando', 'Niño Rivera', 'chninor@sena.edu.co', '2025-10-28 01:55:05', '2025-10-28 01:55:05', NULL),
(58, 'Karol Vanesa', 'Hernández', 'khernandez@sena.edu.co', '2025-10-28 01:56:09', '2025-10-28 01:56:09', NULL),
(59, 'William Rolando', 'Rodríguez', 'wrodriguezr@sena.edu.co', '2025-10-28 01:56:59', '2025-10-28 01:56:59', NULL),
(60, 'Cesar', 'Moreno', 'cmorenogu@sena.edu.co', '2025-10-28 02:03:45', '2025-10-28 02:03:45', NULL),
(61, 'Marly Julieth', 'Hernández Sánchez', 'marhermandezs@sena.edu.co', '2025-10-28 02:04:42', '2025-10-28 02:04:42', NULL),
(63, 'Lina Angélica', 'Ubaque', 'lubaqueb@sena.edu.co', '2025-10-28 02:06:03', '2025-10-28 02:06:03', NULL),
(64, 'Diana Marcela', 'Acosta Torres', 'dacostat@sena.edu.co', '2025-10-28 02:06:41', '2025-10-28 02:06:41', NULL),
(65, 'Arlix Carolina', 'Aragón', 'aaragonc@sena.edu.co', '2025-10-28 02:07:18', '2025-10-28 02:07:18', NULL),
(66, 'Carlos Andrés', 'Sáenz', 'casaenz@sena.edu.co', '2025-10-28 23:41:56', '2025-10-28 23:41:56', NULL),
(67, 'Harol', 'Pardos', 'sergio@admin1.com', '2025-10-29 00:24:23', '2025-10-29 19:47:29', NULL),
(70, 'Hansbleidi', 'Cardenas', 'yurani12@gmail.com', '2025-11-05 18:21:44', '2025-11-05 18:21:44', NULL),
(93, 'Sergio', 'nova', 'casata@hotmail.com', '2025-11-28 18:31:26', '2025-11-28 18:31:26', 9),
(94, 'maria', 'Morita', 's24ffdd@hotmail.com', '2025-11-28 18:32:18', '2025-11-28 18:32:18', 8);

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
(1, '2025_11_19_142456_add_dirigido_a_to_recursos_table', 1),
(2, '2025_11_21_152555_add_tiene_permisos_to_lideres_investigacion_table', 2),
(3, '2025_11_21_152649_add_tiene_permisos_to_lideres_investigacion_table', 3);

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

INSERT INTO `proyectos` (`id_proyecto`, `id_semillero`, `nombre_proyecto`, `descripcion`, `documentos_requeridos`, `estado`, `fecha_inicio`, `fecha_fin`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'IA para Agricultura', 'Proyecto de optimización de cultivos mediante IA', 3, 'EN_EJECUCION', '2025-01-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(2, 2, 'App de Aprendizaje SENA', 'Aplicación para gestión de aprendizajes', 3, 'EN_FORMULACION', '2025-02-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(3, 3, 'Sistema de Energía Solar', 'Investigación en paneles solares', 3, 'FINALIZADO', '2024-09-01', '2025-02-01', '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(4, 4, 'Monitor de Ciberseguridad', 'Monitoreo de redes locales', 3, 'EN_EJECUCION', '2025-03-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(5, 5, 'Robot Educativo', 'Robot con sensores para enseñanza STEAM', 3, 'EN_FORMULACION', '2025-04-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(6, 9, 'sgvevgewb', 'fsdbvdrbre', 3, 'FINALIZADO', '2025-11-14', '2025-11-30', '2025-11-10 19:12:02', '2025-11-10 19:12:02'),
(7, 9, 'avgerhgber', 'dvwdvd', 3, 'FINALIZADO', '2026-01-01', '2026-02-20', '2025-11-10 19:12:32', '2025-11-10 19:12:32'),
(8, 9, '+queperros', 'wedvbdebb', 3, 'EN_FORMULACION', '2026-01-19', '2026-03-14', '2025-11-10 19:12:53', '2025-11-10 19:12:53'),
(9, 9, '+queperros', 'dsv dbdeberb', 3, 'FINALIZADO', '2026-02-13', '2026-04-03', '2025-11-10 19:13:14', '2025-11-10 19:13:14'),
(10, 7, 'Nombre de Ejemplo de proyecto', 'Descripción  de Ejemplo de proyecto', 3, 'EN_FORMULACION', '2025-11-12', '2025-11-30', '2025-11-12 14:49:14', '2025-11-12 14:49:14'),
(11, 7, 'Nombre de Ejemplo de proyecto222', 'descripción  de Ejemplo de proyecto2', 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:49:59', '2025-11-12 15:52:26'),
(12, 7, 'aaa123', 'aaa123', 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:50:26', '2025-11-12 14:50:26'),
(13, 7, 'Proyecto Alcón', 'abc es', 3, 'EN_FORMULACION', NULL, NULL, '2025-11-12 14:50:42', '2025-11-12 15:45:25');

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
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `categoria` enum('plantillas','manuales','otros') NOT NULL DEFAULT 'otros',
  `dirigido_a` enum('todos','aprendices','lideres') NOT NULL DEFAULT 'todos',
  `descripcion` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id`, `nombre_archivo`, `archivo`, `categoria`, `dirigido_a`, `descripcion`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Plantilla rx56', 'recursos/72fkBbEgdn1DJ08qFQqZF8DLZKWVO8xcw7fov004.pdf', 'plantillas', 'todos', 'detalles', 83, '2025-11-20 18:26:55', '2025-11-20 18:26:55'),
(2, 'Plantilla rx56', 'recursos/fG3M8b78kOUqp6Mj4KM2PvI6yuHLINLS9g39hpre.pdf', 'plantillas', 'todos', 'detalles', 83, '2025-11-20 18:26:59', '2025-11-20 18:26:59');

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
(6, 'Bioprocesos y Biotecnología Aplicada (BIBA)', 'Ciencias Aplicadas en Desarrollo Ambiental', 57, '2025-10-27 18:08:12', '2025-10-28 01:57:50'),
(7, 'Administración y Salud, Deportes y Bienestar', 'Administración en Salud, Deportes y Bienestar', 58, '2025-10-27 18:08:12', '2025-11-10 18:42:36'),
(8, 'Agroindustria Seguridad Alimentaria', 'Seguridad Alimentaria', 59, '2025-10-27 18:08:12', '2025-10-28 01:58:57'),
(9, 'Grupo de Estudio de Desarrollo de Software (GEDS)', 'Telecomunicaciones y Tecnologías Virtuales', 70, '2025-10-27 18:08:12', '2025-11-11 23:18:34'),
(10, 'Investigación de Mercados para las Mipymes (INVERPYMES)', 'Comercio y Servicios para el Desarrollo Empresarial', 61, '2025-10-27 18:08:12', '2025-10-28 02:09:20'),
(11, 'Materiales, Procesos de Manufactura y Automatización (MAPRA)', 'Diseño, Ingeniería y Mecatrónica', 66, '2025-10-27 18:08:12', '2025-10-28 23:42:15'),
(12, 'Micronanotec', 'Integración de tecnologías convergentes para el mejoramiento de la calidad de vida', 63, '2025-10-27 18:08:12', '2025-10-28 02:10:07'),
(13, 'Desarrollo de Videojuegos Serios', 'Telecomunicaciones y Tecnologías Virtuales', 64, '2025-10-27 18:08:12', '2025-11-11 00:11:07'),
(14, 'PICIDE (Pedagogía)', 'Ciencias Sociales y Ciencias de la Educación', 65, '2025-10-27 18:08:13', '2025-10-28 02:10:28');

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
  `name` varchar(255) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `documento` varchar(40) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `genero` enum('HOMBRE','MUJER','NO DEFINIDO') DEFAULT NULL,
  `tipo_rh` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
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

INSERT INTO `users` (`id`, `name`, `apellidos`, `tipo_documento`, `documento`, `celular`, `genero`, `tipo_rh`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `last_login_at`, `created_at`, `updated_at`) VALUES
(8, 'Joaquin cañon', NULL, NULL, NULL, NULL, NULL, NULL, 'test3@gmail.com', NULL, '$2y$12$CysY7mh6WuCxIc.j4vORxuqAEPzjDJr0lxxqSo.Q.8B0Q9caCicLW', 'APRENDIZ', NULL, NULL, '2025-10-16 01:50:17', '2025-10-16 01:50:17'),
(9, 'hansita', NULL, NULL, NULL, NULL, NULL, NULL, 'hanscard@20gmail.com', NULL, '$2y$12$BKJsJ8LlRHORZj/c4gIBqeU1u9Zt3lPlAiyOFjX23Ac084uYZpXR.', 'ADMIN', NULL, NULL, '2025-10-16 19:17:57', '2025-10-16 19:17:57'),
(20, 'Laura Martínez', NULL, NULL, NULL, NULL, NULL, NULL, 'laura@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(21, 'Carlos Pérez', NULL, NULL, NULL, NULL, NULL, NULL, 'carlos@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(26, 'Carlos Gómez', NULL, NULL, NULL, NULL, NULL, NULL, 'carlos.gomez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash2', 'APRENDIZ', NULL, NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(27, 'Valentina Ruiz', NULL, NULL, NULL, NULL, NULL, NULL, 'valentina.ruiz@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash3', 'APRENDIZ', NULL, NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(28, 'Andrés Pérez', NULL, NULL, NULL, NULL, NULL, NULL, 'andres.perez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash4', 'APRENDIZ', NULL, NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(29, 'María', 'Morita', NULL, NULL, NULL, NULL, NULL, 'maria.castro@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash5', 'APRENDIZ', NULL, NULL, '2025-10-21 15:08:35', '2025-10-22 23:46:58'),
(43, 'Sergio', 'nova', NULL, NULL, NULL, NULL, NULL, 'hola1@hola.com', NULL, '$2y$12$X6be5Oyv8GOMcJ6XeiR2E.kexPv76F04xDiid9KsY3/u9lW5AHP82', 'ADMIN', NULL, '2025-11-18 13:52:10', '2025-10-22 21:29:19', '2025-11-18 13:52:10'),
(45, 'Andrés', 'garcia', NULL, NULL, NULL, NULL, NULL, 'hola@hola.com', NULL, '$2y$12$gDClHnaBJeyAT.p1n6mlR.r.0LBbtL/ejEhvY26v23OcnKORnEWAi', 'LIDER_SEMILLERO', NULL, '2025-11-19 18:39:35', '2025-10-22 21:30:06', '2025-11-19 18:39:35'),
(46, 'Sergio', 'Morita', NULL, NULL, NULL, NULL, NULL, 'asd2223@hola.com', NULL, '$2y$12$MBRwPyhaJOUhkX3g1sprD.6X3OZaVMzlM04O8PfHMfoXMuhuHnMme', 'APRENDIZ', NULL, NULL, '2025-10-22 21:48:14', '2025-10-22 23:46:41'),
(47, 'maria', 'nova', NULL, NULL, NULL, NULL, NULL, 'danielcf97@hotmail.com', NULL, '$2y$12$gIcmOXdncQNHiCYKuHoUKufcEKdTgJjE4xjAAU4BQBp7JHGi1.gRO', 'ADMIN', NULL, NULL, '2025-10-22 23:53:38', '2025-10-22 23:53:38'),
(48, 'Sergio', 'nova', NULL, NULL, NULL, NULL, NULL, 'daniel7@hotmail.com', NULL, '$2y$12$3j3LhORhr9ggHeZLCNDNvePrJvy.CGFEOlwM6whleyKac8XsKKqBm', 'ADMIN', NULL, NULL, '2025-10-23 02:39:02', '2025-10-23 02:39:02'),
(49, 'LOREE', 'nova', NULL, NULL, NULL, NULL, NULL, 'lui1@hotmail.com', NULL, '$2y$12$vkzG43/t8XiXhxA6KIIhIem3Rc9VTzevb5beLUN95vX6d4rzrUdiu', 'ADMIN', NULL, NULL, '2025-10-23 02:40:11', '2025-10-23 02:40:11'),
(50, 'ñorcus', 'pardo', NULL, NULL, NULL, NULL, NULL, 'norcus@hola.com', NULL, '$2y$12$ZOJVqJKIm9TgY19QJsEMuuYaug1iL6hloDYUYScCi3l/vDKIQtSuG', 'ADMIN', NULL, NULL, '2025-10-24 00:29:42', '2025-10-24 00:29:42'),
(51, 'LOREEna', 'mogoyon', NULL, NULL, NULL, NULL, NULL, 'loreadmin@hola.com', NULL, '$2y$12$gV9quZG5HruIW0FcKDUqcO.KMB2jr/phmGjNgBaSdYuQIlQ5vp8Oi', 'ADMIN', NULL, NULL, '2025-10-24 00:30:08', '2025-10-24 00:30:08'),
(54, 'maria', 'Morita', NULL, NULL, NULL, NULL, NULL, 'hoaprela@hola.com', NULL, '$2y$12$D6qRyFHBWW5FwmZEDI.6sel6F8tFEY0dkcmYhdk9nqYl9VgWTAU4e', 'APRENDIZ', NULL, NULL, '2025-10-24 00:31:38', '2025-10-24 00:31:38'),
(55, 'Test User', NULL, NULL, NULL, NULL, NULL, NULL, 'test@example.com', '2025-10-27 21:52:19', '$2y$12$MWjLtw9w/Araa.FitOcv.OyfUFlBVXvQeipNfNkPQaEzKYfgzJnnu', 'APRENDIZ', 'UxHPINOx1R', NULL, '2025-10-27 21:52:20', '2025-10-27 21:52:20'),
(56, 'Sergio', 'Morita', NULL, NULL, NULL, NULL, NULL, 'sergio@sena.com', NULL, '$2y$12$IZH2smkp0bK.KMJN1hVgxOWHruIuyfshhW8wIMELGbl1twC9klCsK', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 01:35:08', '2025-10-28 01:35:08'),
(57, 'Carlos Hernando', 'Niño Rivera', NULL, NULL, NULL, NULL, NULL, 'chninor@sena.edu.co', NULL, '$2y$12$2u3HSnrhTaf1Szhm/Zszt.9Rx0NxPeqD71Brg2RV8fmhz0LLDOaYC', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 01:55:05', '2025-10-28 01:55:05'),
(58, 'Karol Vanesa', 'Hernández', NULL, NULL, NULL, NULL, NULL, 'khernandez@sena.edu.co', NULL, '$2y$12$K0hG1LjlEGqRIPFlXihubu26Y8rGxnrwA07OcxX.xGzYDivps6WoS', 'LIDER_SEMILLERO', NULL, '2025-11-28 20:37:32', '2025-10-28 01:56:09', '2025-11-28 20:37:32'),
(59, 'William Rolando', 'Rodríguez', NULL, NULL, NULL, NULL, NULL, 'wrodriguezr@sena.edu.co', NULL, '$2y$12$jFM7Upw4h3TD4DKZySV3d.QT2xwS1sQkQ4WchZoLVPTOhvn1MBPR6', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 01:56:59', '2025-10-28 01:56:59'),
(60, 'Cesar', 'Moreno', NULL, NULL, NULL, NULL, NULL, 'cmorenogu@sena.edu.co', NULL, '$2y$12$EWRQGOJoPb3S2lsIcIfpaOja9/fyNjcC8RYwZWSPS69Psv4tFS.m.', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 02:03:45', '2025-10-28 02:03:45'),
(61, 'Marly Julieth', 'Hernández Sánchez', NULL, NULL, NULL, NULL, NULL, 'marhermandezs@sena.edu.co', NULL, '$2y$12$3COInLSxSGW0VKA.qyyBVebMxPUOB7vUw2H4COtGodVanGqkPYByy', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 02:04:42', '2025-10-28 02:04:42'),
(63, 'Lina Angélica', 'Ubaque', NULL, NULL, NULL, NULL, NULL, 'lubaqueb@sena.edu.co', NULL, '$2y$12$haGM4Gz72sf/v8wBzflhCeaE5yRwCFAACQlR0KH185D3iC1MrGToW', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 02:06:03', '2025-10-28 02:06:03'),
(64, 'Diana Marcela', 'Acosta Torres', NULL, NULL, NULL, NULL, NULL, 'dacostat@sena.edu.co', NULL, '$2y$12$FjzqHVGBfWlCq7IEliyo/OmMhWHJq/8DGF3Q88WPXK8EcigrhiB5e', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 02:06:41', '2025-10-28 02:06:41'),
(65, 'Arlix Carolina', 'Aragón', NULL, NULL, NULL, NULL, NULL, 'aaragonc@sena.edu.co', NULL, '$2y$12$VftA7JvPDUz2PaH11KMsvOX/mZNQ9rq1QspsFx9s1xngxJl9Ol622', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 02:07:18', '2025-10-28 02:07:18'),
(66, 'Carlos Andrés', 'Sáenz', NULL, NULL, NULL, NULL, NULL, 'casaenz@sena.edu.co', NULL, '$2y$12$cMQuRFhEsAMGtz0tjcjIbOSxBNXgUvnecHFuTw1iiT4IsrgTLAOj2', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-28 23:41:56', '2025-10-28 23:41:56'),
(67, 'Harol', 'Pardos', NULL, NULL, NULL, NULL, NULL, 'sergio@admin1.com', NULL, '$2y$12$oqJyGYOnWOqd6WLDIOOaHOXybBykL2OIOGYhs77dUPf3sPo4BhpRq', 'LIDER_SEMILLERO', NULL, NULL, '2025-10-29 00:24:23', '2025-10-29 19:47:29'),
(69, 'luis', 'garcia', NULL, NULL, NULL, NULL, NULL, 'lideradmin@hola.com', NULL, '$2y$12$xgChGVNvHOBXhGjD98dmU.k94pgDEn7akThvCIKVtwhSg7uvy3e0.', 'ADMIN', NULL, NULL, '2025-10-30 00:29:35', '2025-10-30 00:29:35'),
(70, 'Hansbleidi', 'Cardenas', NULL, NULL, NULL, NULL, NULL, 'yurani12@gmail.com', NULL, '$2y$12$IaEEeNepnPujOXZk6c5fQeYMB.38rLT6MDZsuBa.xq6ZfBV3sx.Ka', 'LIDER_INVESTIGACION', NULL, '2025-11-20 18:55:45', '2025-11-05 18:21:44', '2025-11-20 18:55:45'),
(71, 'jubhb', 'jhvv', NULL, NULL, NULL, NULL, NULL, 'hidalgo.16@gmail.com', NULL, '$2y$12$IL1eNma7i4KitbcXlSYfuO8IVp2Y1RZkQHa9W451RURzi/WehtJjS', 'APRENDIZ', NULL, NULL, '2025-11-11 23:37:32', '2025-11-11 23:37:32'),
(72, 'Juan', 'Pérez López', NULL, NULL, NULL, NULL, NULL, 'juan.perez@misena.edu.co', NULL, '$2y$12$RlunXngGHsN4EyYmNHCj3OkIdTQfriQzIprFNnuNAEbTh0kLCtjje', 'APRENDIZ', NULL, NULL, '2025-11-12 18:18:57', '2025-11-12 18:18:57'),
(73, 'Laura', 'Gómez Rincón', NULL, NULL, NULL, NULL, NULL, 'laura.gomez@misena.edu.co', NULL, '$2y$12$CDQ.mIzBy2nP7mEVHLsBYuTwW.VOMVRmEDOM9Hq1jd2KzY0WdMB4C', 'APRENDIZ', NULL, NULL, '2025-11-12 18:20:35', '2025-11-12 18:20:35'),
(74, 'Carlos', 'Méndez Silva', NULL, NULL, NULL, NULL, NULL, 'carlos.mendez@misena.edu.co', NULL, '$2y$12$6wwudRztzdTSwo6UOxV.mevXE0aU3LkD53IS5OH8xbx6hIuRjR9P6', 'APRENDIZ', NULL, NULL, '2025-11-12 18:21:57', '2025-11-12 18:21:57'),
(75, 'Andrea', 'Díaz', NULL, NULL, NULL, NULL, NULL, 'andrea.moreno@misena.edu.co', NULL, '$2y$12$8./hYkYMUKYq40b0fgPOYOgX8v8ImoVHqXpUzGvl9vYyzgw2OF0Z2', 'APRENDIZ', NULL, NULL, '2025-11-12 18:23:31', '2025-11-12 18:23:31'),
(76, 'Kevin', 'levinin', NULL, NULL, NULL, NULL, NULL, 'kevinsan@hotmail.com', NULL, '$2y$12$Y8HQzARbw1BN4gQR.172KulRsUAwmmjwRsLDHGo5ThVxK3hwHfFZW', 'APRENDIZ', NULL, NULL, '2025-11-12 22:39:15', '2025-11-12 22:39:15'),
(77, 'Mario', 'nova', NULL, NULL, NULL, NULL, NULL, 'aprendiz@hotmail.com', NULL, '$2y$12$n9s5zzz5aP.ELxl5KGrhn.g0UhDy6dYNNDdrTN38AEqY0SWId/qMO', 'APRENDIZ', NULL, NULL, '2025-11-13 00:45:01', '2025-11-13 00:45:01'),
(82, 'cangiro', 'Sáenz', NULL, NULL, NULL, NULL, NULL, 'aprendizsinficha@hola.com', NULL, '$2y$12$jxN6wbzuLjed0saX7ydvDuACXwotkCMDtdqgja/vDFiam0EoiP0Ia', 'APRENDIZ', NULL, NULL, '2025-11-13 01:06:03', '2025-11-13 01:06:03'),
(83, 'joaquin', 'cañon', NULL, NULL, NULL, NULL, NULL, 'test@gmail.com', NULL, '$2y$12$sHrfMiEsMJUZcwdjDaW9Duw6UUXlzdhji2XuTZ/HchRUVqqT.n5qe', 'ADMIN', NULL, '2025-11-28 20:37:56', '2025-11-18 13:52:56', '2025-11-28 20:37:56'),
(84, 'Sergio', 'mogoyon', NULL, NULL, NULL, NULL, NULL, 'sergius@gmail.com', NULL, '$2y$12$15JuiLt.GUmdNvJmgc8tz.bnEwowAlWrL9XEp/lqwZ.VswWlWKhuW', 'APRENDIZ', NULL, '2025-11-18 15:23:38', '2025-11-18 13:54:41', '2025-11-18 15:23:38'),
(85, 'geral', 'fajardo', NULL, NULL, NULL, NULL, NULL, 'example@mi.sena.edu.co', NULL, '$2y$12$fNW/LwABdKI6WxTCwGa.xuQ/hcYd8CftseU/UV2kPKoOQ2J9fwTQK', 'APRENDIZ', NULL, NULL, '2025-11-19 13:12:42', '2025-11-19 13:12:42'),
(86, 'jhon', 'cortes', NULL, NULL, NULL, NULL, NULL, 'example2@soy.sena.edu.co', NULL, '$2y$12$MKX16HShWl83SlpPnK6DQuQlX3LaxbkfIagGlKZ0BNRKRod62OP86', 'APRENDIZ', NULL, NULL, '2025-11-19 13:39:28', '2025-11-19 13:39:28'),
(87, 'Santiago', 'Aves', NULL, NULL, NULL, NULL, NULL, 'example3@soy.sena.edu.co', NULL, '$2y$12$7xsdRlZ2I66UhOuIJLU/Q.hDKSRlnZs4j74uM1wnD4hOwA9xSbPam', 'APRENDIZ', NULL, NULL, '2025-11-19 13:40:32', '2025-11-19 13:40:32'),
(88, 'jhonnatan', 'torres', NULL, NULL, NULL, NULL, NULL, 'kg22@gmail.com', NULL, '$2y$12$TRolyCgZ2lLB7moUXLxQ4O7M8DLpsQsf1vj3WvB9nnNPKUvaMRFdG', 'APRENDIZ', NULL, '2025-11-20 18:56:34', '2025-11-20 18:49:06', '2025-11-28 14:31:05'),
(89, 'Jhon', 'Torres', NULL, NULL, NULL, NULL, NULL, 'liderinves@hotmail.com', NULL, '$2y$12$pA/obeF92h2XH2Ec1u6OjO8V8m5BIwjh3sUyFC/Nky9rkpw/qZEZy', 'LIDER_INVESTIGACION', NULL, '2025-11-24 15:01:07', '2025-11-21 20:14:34', '2025-11-24 15:01:07'),
(92, 'conan', 'Morita', 'CC', '5478645648', '3053970242', 'HOMBRE', 'A+', 'ejemplogenero@sena.edu.co', NULL, '$2y$12$w2lNef/9ClU6NxzRiwPsce6Wx9sxZC4JIlC/m9oY3693uLeM2q1T.', 'ADMIN', NULL, NULL, '2025-11-28 18:20:10', '2025-11-28 18:20:10'),
(93, 'Sergio', 'nova', 'CC', 'asdsad', '1231232', 'HOMBRE', 'A+', 'aaaaaa@gmail.com', NULL, '$2y$12$5fNajMA/y/.NsTOfSAE4B.voJZgsr6aisnJlFCVo6Eyyvl7zfNsD6', 'LIDER_SEMILLERO', NULL, NULL, '2025-11-28 18:31:26', '2025-11-28 18:31:26'),
(94, 'maria', 'Morita', 'TI', '15576151', '3524587154', 'MUJER', 'AB-', 'asdasdccc@hola.com', NULL, '$2y$12$A9qV8p1lo5jrT7TQ.wr0DujevKwOAwwHKlbK3wrx7eLpBBBPE5fae', 'LIDER_SEMILLERO', NULL, NULL, '2025-11-28 18:32:18', '2025-11-28 18:32:18'),
(95, 'domniko', 'misterio', 'CE', '1245774584', '3502458754', 'NO DEFINIDO', 'B+', 'lodeinvestiga@hotmail.com', NULL, '$2y$12$ttanSvnlLhsbR3p33IOlKOxrn5oUMEHJjiArv8k7iI5yjZSQEnxqC', 'LIDER_INVESTIGACION', NULL, NULL, '2025-11-28 18:44:12', '2025-11-28 18:44:12');

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
  ADD UNIQUE KEY `eventos_lider_fecha_unique` (`id_lider_semi`,`fecha_hora`),
  ADD KEY `idx_eventos_fecha` (`fecha_hora`),
  ADD KEY `idx_eventos_admin_fecha` (`id_admin`,`fecha_hora`),
  ADD KEY `idx_eventos_lider_fecha` (`id_lider_semi`,`fecha_hora`),
  ADD KEY `eventos_codigo_reunion_index` (`codigo_reunion`),
  ADD KEY `fk_eventos_proyecto` (`id_proyecto`);
ALTER TABLE `eventos` ADD FULLTEXT KEY `ft_eventos` (`titulo`,`descripcion`);

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
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

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
  ADD PRIMARY KEY (`id_lider_semi`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recursos_user` (`user_id`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id_aprendiz` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

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
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lideres_investigacion`
--
ALTER TABLE `lideres_investigacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  MODIFY `id_semillero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

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
  ADD CONSTRAINT `fk_eventos_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Filtros para la tabla `recursos`
--
ALTER TABLE `recursos`
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
