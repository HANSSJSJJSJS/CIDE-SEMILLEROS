-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 30-10-2025 a las 22:46:44
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

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
  `apellidos` varchar(120) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_usuario`, `nombre`, `apellidos`, `creado_en`, `actualizado_en`) VALUES
(30, 'maria', 'torres', '2025-10-17 01:21:34', '2025-10-17 01:21:34'),
(31, 'k', 'r', '2025-10-16 21:41:24', '2025-10-16 21:41:24'),
(39, 'f', 'f', '2025-10-17 02:48:02', '2025-10-17 02:48:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `ficha` varchar(30) NOT NULL,
  `programa` varchar(160) NOT NULL,
  `tipo_documento` varchar(5) DEFAULT NULL,
  `documento` varchar(40) NOT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `correo_personal` varchar(160) DEFAULT NULL,
  `contacto_nombre` varchar(160) DEFAULT NULL,
  `contacto_celular` varchar(30) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aprendices`
--

INSERT INTO `aprendices` (`id_usuario`, `estado`, `nombres`, `apellidos`, `ficha`, `programa`, `tipo_documento`, `documento`, `celular`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `creado_en`, `actualizado_en`) VALUES
(26, 'Activo', 'joaquin', 'cañon', '2848527', 'adso', 'CC', '1012443507', '3053970242', 'hola11@hotmail.com', 'danielcf97@hotmail.com', NULL, NULL, '2025-10-16 15:03:39', '2025-10-16 15:03:39'),
(33, 'Activo', 'c', 'c', '1222', 'Adso', 'CC', '2', '12', 'c@sena.edu.co', 'c@pol.es', NULL, NULL, '2025-10-16 16:45:59', '2025-10-22 14:39:58'),
(43, 'Activo', 'a', 's', '123456s', 'Adsos', 'TI', '43', '14s', 'as@sena.edu.co', 'aa@pol.es', NULL, NULL, '2025-10-22 00:56:14', '2025-10-22 00:56:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `proyecto_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `nombre_almacenado` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `estado` enum('pendiente','aprobado') NOT NULL DEFAULT 'pendiente',
  `mime_type` varchar(255) DEFAULT NULL,
  `subido_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `archivos`
--

INSERT INTO `archivos` (`id`, `user_id`, `proyecto_id`, `nombre_original`, `nombre_almacenado`, `ruta`, `estado`, `mime_type`, `subido_en`, `created_at`, `updated_at`) VALUES
(1, 33, 2, 'chromewebstore_google.pdf', '68ffd9b059fb4_chromewebstore_google.pdf', 'documentos/68ffd9b059fb4_chromewebstore_google.pdf', 'aprobado', 'application/pdf', '2025-10-28 01:44:32', '2025-10-28 01:44:32', '2025-10-28 01:44:32'),
(2, 33, 1, 'chromewebstore_google.pdf', '68ffd9d3e920d_chromewebstore_google.pdf', 'documentos/68ffd9d3e920d_chromewebstore_google.pdf', 'pendiente', 'application/pdf', '2025-10-28 01:45:07', '2025-10-28 01:45:07', '2025-10-28 01:45:07'),
(3, 33, 5, 'chromewebstore_google.pdf', '68ffdc3765b6c_chromewebstore_google.pdf', 'documentos/68ffdc3765b6c_chromewebstore_google.pdf', 'pendiente', 'application/pdf', '2025-10-28 01:55:19', '2025-10-28 01:55:19', '2025-10-28 01:55:19'),
(4, 33, 2, 'chromewebstore_google.pdf', '68ffddfcd923a_chromewebstore_google.pdf', 'documentos/68ffddfcd923a_chromewebstore_google.pdf', 'pendiente', 'application/pdf', '2025-10-28 02:02:52', '2025-10-28 02:02:52', '2025-10-28 02:02:52'),
(5, 33, 5, 'chromewebstore_google.pdf', '6903bb9f7112b_chromewebstore_google.pdf', 'documentos/6903bb9f7112b_chromewebstore_google.pdf', 'aprobado', 'application/pdf', '2025-10-31 00:25:19', '2025-10-31 00:25:19', '2025-10-31 00:25:19'),
(6, 33, 1, 'chromewebstore_google.pdf', '6903bbd843766_chromewebstore_google.pdf', 'documentos/6903bbd843766_chromewebstore_google.pdf', 'aprobado', 'application/pdf', '2025-10-31 00:26:16', '2025-10-31 00:26:16');

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
('laravel12-cache-|wquinteroc@sena.edu.co|127.0.0.1', 'i:3;', 1761664884),
('laravel12-cache-|wquinteroc@sena.edu.co|127.0.0.1:timer', 'i:1761664884;', 1761664884),
('laravel12-cache-ca@pol.es|127.0.0.1', 'i:1;', 1761582101),
('laravel12-cache-ca@pol.es|127.0.0.1:timer', 'i:1761582101;', 1761582101),
('laravel12-cache-luis111@hotmail.com|127.0.0.1', 'i:1;', 1761668573),
('laravel12-cache-luis111@hotmail.com|127.0.0.1:timer', 'i:1761668573;', 1761668573),
('laravel12-cache-wquinteroc@sena.edu.co|127.0.0.1', 'i:2;', 1761664873),
('laravel12-cache-wquinteroc@sena.edu.co|127.0.0.1:timer', 'i:1761664873;', 1761664873);

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
  `documento` varchar(255) NOT NULL,
  `fecha_subido` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','completado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_lider` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `id_proyecto` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion` int(11) NOT NULL DEFAULT 60,
  `ubicacion` varchar(255) NOT NULL DEFAULT 'presencial',
  `link_virtual` varchar(255) DEFAULT NULL,
  `codigo_reunion` varchar(255) DEFAULT NULL,
  `recordatorio` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `id_lider`, `id_usuario`, `id_proyecto`, `titulo`, `tipo`, `descripcion`, `fecha_hora`, `duracion`, `ubicacion`, `link_virtual`, `codigo_reunion`, `recordatorio`, `created_at`, `updated_at`) VALUES
(1, 38, 33, 2, 'Prueba', 'ADSO', 'Lolete', '2025-10-31 22:30:00', 120, 'presencial', 'N/A', 'N/A', NULL, '2025-10-28 14:13:20', '2025-10-28 14:13:20'),
(9, 38, NULL, NULL, 'Valorant', 'revision', 'Subir de rango chavales', '2025-10-28 15:00:00', 120, 'lab2', NULL, NULL, '15', '2025-10-28 20:23:09', '2025-10-28 20:23:09'),
(11, 38, NULL, NULL, 'Pol', 'seguimiento', 'Records', '2025-10-28 14:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_oiTpactnmV%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%2238%22%7D&subject=Pol', 'ZfJubSedPy', '15', '2025-10-28 20:32:40', '2025-10-28 20:32:40'),
(12, 38, NULL, NULL, 'Jijijaja', 'general', 'Subir de copas en el Clash', '2025-10-29 15:00:00', 30, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_S0d8Av2xgy%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%2238%22%7D&subject=Jijijaja', 'Ua6kTjKlYJ', '15', '2025-10-29 18:40:52', '2025-10-29 18:40:52'),
(13, 38, NULL, NULL, 'fgh', 'seguimiento', 'asdasdasdada', '2025-10-29 10:00:00', 30, 'lab1', NULL, NULL, '15', '2025-10-29 19:46:22', '2025-10-29 19:46:22'),
(14, 38, NULL, NULL, 'assa', 'seguimiento', NULL, '2025-10-31 14:00:00', 60, 'sala2', NULL, NULL, 'none', '2025-10-29 20:37:54', '2025-10-29 20:37:54'),
(15, 38, NULL, 5, 'assa', 'capacitacion', NULL, '2025-10-30 16:00:00', 30, 'otra', NULL, NULL, '30', '2025-10-31 01:06:27', '2025-10-31 01:06:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_participantes`
--

CREATE TABLE `evento_participantes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_participantes`
--

INSERT INTO `evento_participantes` (`id`, `id_evento`, `id_aprendiz`, `created_at`, `updated_at`) VALUES
(1, 1, 33, '2025-10-28 14:14:01', '2025-10-28 14:14:01'),
(2, 1, 43, '2025-10-28 14:27:58', '2025-10-28 14:27:58'),
(22, 9, 43, '2025-10-28 20:23:09', '2025-10-28 20:23:09'),
(23, 9, 33, '2025-10-28 20:23:09', '2025-10-28 20:23:09'),
(24, 9, 26, '2025-10-28 20:23:09', '2025-10-28 20:23:09'),
(28, 11, 43, '2025-10-28 20:32:40', '2025-10-28 20:32:40'),
(29, 11, 33, '2025-10-28 20:32:40', '2025-10-28 20:32:40'),
(30, 11, 26, '2025-10-28 20:32:40', '2025-10-28 20:32:40'),
(31, 12, 43, '2025-10-29 18:40:52', '2025-10-29 18:40:52'),
(32, 12, 33, '2025-10-29 18:40:52', '2025-10-29 18:40:52'),
(33, 12, 26, '2025-10-29 18:40:52', '2025-10-29 18:40:52'),
(34, 13, 43, '2025-10-29 19:46:22', '2025-10-29 19:46:22'),
(35, 13, 33, '2025-10-29 19:46:22', '2025-10-29 19:46:22'),
(36, 13, 26, '2025-10-29 19:46:22', '2025-10-29 19:46:22'),
(37, 14, 43, '2025-10-29 20:37:54', '2025-10-29 20:37:54'),
(38, 14, 33, '2025-10-29 20:37:54', '2025-10-29 20:37:54'),
(39, 14, 26, '2025-10-29 20:37:54', '2025-10-29 20:37:54'),
(40, 15, 43, '2025-10-31 01:06:27', '2025-10-31 01:06:27'),
(41, 15, 33, '2025-10-31 01:06:27', '2025-10-31 01:06:27'),
(42, 15, 26, '2025-10-31 01:06:27', '2025-10-31 01:06:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencias`
--

CREATE TABLE `evidencias` (
  `id_evidencia` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('pendiente','completado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evidencias`
--

INSERT INTO `evidencias` (`id_evidencia`, `id_proyecto`, `id_usuario`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 5, 33, 'chromewebstore_google.pdf', 'pendiente', '2025-10-28 01:55:19', '2025-10-28 01:55:19'),
(2, 2, 33, 'chromewebstore_google.pdf', 'completado', '2025-10-28 02:02:52', '2025-10-28 02:02:52'),
(3, 5, 33, 'chromewebstore_google.pdf', 'pendiente', '2025-10-31 00:25:19', '2025-10-31 00:25:19'),
(4, 1, 33, 'chromewebstore_google.pdf', 'pendiente', '2025-10-31 00:26:16', '2025-10-31 00:26:16');

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
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(10) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `nombre_grupo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_aprendices`
--

CREATE TABLE `grupo_aprendices` (
  `id_grupo_aprendiz` int(10) UNSIGNED NOT NULL,
  `id_grupo` int(10) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `fecha_ingreso` date DEFAULT curdate(),
  `activo` tinyint(1) DEFAULT 1
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
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `tipo_documento` varchar(5) DEFAULT NULL,
  `documento` varchar(40) NOT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lideres_semillero`
--

INSERT INTO `lideres_semillero` (`id_usuario`, `nombres`, `apellidos`, `tipo_documento`, `documento`, `correo_institucional`, `creado_en`, `actualizado_en`) VALUES
(25, 'Jose', 'mogoñon', 'CE', '123456789', 'correo@hotmail.com', '2025-10-16 15:01:45', '2025-10-16 15:01:45'),
(32, 'a', 'a', 'CC', '1', 'a@pol.es', '2025-10-16 16:44:54', '2025-10-16 16:44:54'),
(38, 'l', 'l', 'CC', '34', 'lider1@gmail.com', '2025-10-16 21:45:51', '2025-10-16 21:45:51'),
(42, 'L', 'Lopez', 'CC', '55', 'l@pol.es', '2025-10-17 15:41:16', '2025-10-17 15:41:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lider_general`
--

CREATE TABLE `lider_general` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Correo_institucional` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lider_general`
--

INSERT INTO `lider_general` (`id_usuario`, `nombres`, `apellidos`, `creado_en`, `actualizado_en`, `Correo_institucional`) VALUES
(23, 'luis', '', '2025-10-16 14:25:05', '2025-10-16 14:33:46', 'luis111@hotmail.com');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_15_152741_create_administradores_table', 2),
(5, '2025_10_17_163819_create_archivos_table', 3),
(6, '2025_10_20_172439_add_estado_to_users_table', 4),
(7, '2025_10_24_133708_add_estado_to_documentos_table', 5),
(8, '2025_10_24_170619_add_estado_to_archivos_table', 6),
(9, '2025_10_24_191739_add_proyecto_id_to_archivos_table', 7),
(10, '2025_10_27_200800_add_id_usuario_to_evidencias_table', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('k@pol.es', '$2y$12$xDTepohH4kB/7czWPOBOhuA6T0CEjMFYduuo1i8X6/3Az84Kk9pV.', '2025-10-17 20:57:42'),
('kebosi1177@fogdiver.com', '$2y$12$d/d/To9TuZm1CICvm/XOjO334Klte.1RHuDmqrZY1O.6t6dG6S49.', '2025-10-17 02:40:26'),
('lider1@gmail.com', '$2y$12$QzbKr/Kcr.iq4uP4wOcLme4.FT7OLyBv5LINQSC8rf/MqPb99zUe.', '2025-10-17 02:46:12'),
('luis111@hotmail.com', '$2y$12$ZcSlyHukXCEo5HLGAmxC1OywKTAXPuocV1owqFN0R5/QGDgxe5RZm', '2025-10-17 02:45:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `id_semillero` int(10) UNSIGNED NOT NULL,
  `id_tipo_proyecto` tinyint(3) UNSIGNED NOT NULL,
  `nombre_proyecto` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('EN_FORMULACION','EN_EJECUCION','FINALIZADO','ARCHIVADO') DEFAULT 'EN_FORMULACION',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id_proyecto`, `id_semillero`, `id_tipo_proyecto`, `nombre_proyecto`, `descripcion`, `estado`, `fecha_inicio`, `fecha_fin`, `creado_en`, `actualizado_en`) VALUES
(1, 33, 1, 'Prueba', 'Temp', 'EN_FORMULACION', '2025-10-20', '2025-10-30', '2025-10-20 19:59:48', '2025-10-22 21:25:41'),
(2, 33, 2, 'Lx', '........as', 'EN_EJECUCION', '2025-10-22', '2025-10-31', '2025-10-22 21:23:54', '2025-10-22 21:25:51'),
(3, 33, 3, 'PerritosXpress', 'Tipo de proyecto en ayuda y beneficencia a los perritos. ', 'FINALIZADO', '2025-10-23', '2025-10-23', '2025-10-23 16:06:32', '2025-10-23 16:06:32'),
(5, 33, 5, 'MotosVictoria', 'Alquila y Venta de Motos', 'ARCHIVADO', '2025-10-23', '2025-10-23', '2025-10-23 16:32:01', '2025-10-24 14:13:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_user`
--

CREATE TABLE `proyecto_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyecto_user`
--

INSERT INTO `proyecto_user` (`id`, `user_id`, `id_proyecto`, `created_at`, `updated_at`) VALUES
(5, 33, 5, '2025-10-23 16:34:39', '2025-10-23 16:34:39'),
(7, 26, 2, '2025-10-30 01:56:17', '2025-10-30 01:56:17'),
(16, 26, 1, '2025-10-30 02:07:01', '2025-10-30 02:07:01'),
(17, 33, 1, '2025-10-30 02:07:16', '2025-10-30 02:07:16'),
(18, 43, 1, '2025-10-30 02:07:22', '2025-10-30 02:07:22'),
(19, 33, 2, '2025-10-30 02:13:07', '2025-10-30 02:13:07'),
(20, 33, 3, '2025-10-30 02:14:08', '2025-10-30 02:14:08'),
(21, 26, 5, '2025-10-30 02:17:59', '2025-10-30 02:17:59'),
(22, 26, 3, '2025-10-30 02:18:30', '2025-10-30 02:18:30'),
(23, 43, 3, '2025-10-30 02:18:33', '2025-10-30 02:18:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semilleros`
--

CREATE TABLE `semilleros` (
  `id_semillero` int(10) UNSIGNED NOT NULL,
  `nombre_semillero` varchar(120) NOT NULL,
  `línea_investigación` text DEFAULT NULL,
  `id_lider_usuario` int(10) UNSIGNED NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('UUKQHomHrI4diBOgs1IZNH9A0EuK2vEIufVu4NrS', 31, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQWF0U1VnTEJpdUFpSThRSFRmd3M3OUZTQWdDTnJYUnk2ZFE1UjUwQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi91c3Vhcmlvcz9wYWdlPTEiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMTt9', 1761860784);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'APRENDIZ',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(23, 'luis', 'luis111@hotmail.com', NULL, '$2y$12$telu8zAjQEs8aUnnIPfTFugatCjbGFxsCxT5ri97UnP/JFeLn2vPC', 'LIDER GENERAL', NULL, '2025-10-16 19:25:05', '2025-10-16 19:25:05'),
(24, 'joaquin cañon', 'admin@hola.com', NULL, '$2y$12$XoBCQBVadL3/0PY1sIRFfuAoorRFd4rKVvTNf3dEvgAo7VIDeKEqy', 'ADMIN', NULL, '2025-10-16 20:00:00', '2025-10-16 20:00:00'),
(25, 'Jose mogoñon', 'correo@hotmail.com', NULL, '$2y$12$hXAZa42VsWTMGsBHLO2aKOiTczq2RHhjiDdUNZtEUNwE0827F7fJ6', 'LIDER_SEMILLERO', NULL, '2025-10-16 20:01:45', '2025-10-16 20:01:45'),
(26, 'joaquin cañon', 'danielcf97@hotmail.com', NULL, '$2y$12$Hnha5y1PV6ZDbQdj/X4dDuDVM2oO2zyRDNSzPTy0qwwVOcbB6fNAq', 'APRENDIZ', NULL, '2025-10-16 20:03:39', '2025-10-16 20:03:39'),
(30, 'maria torres', 'maria@hotmail.com', NULL, '$2y$12$XJsoiu5YUaGqBYYP1aLGluHYJEhhnT1io8WSE9TMRGqtyTm7viiIa', 'ADMIN', NULL, '2025-10-16 20:21:34', '2025-10-16 20:21:34'),
(31, 'k r', 'k@pol.es', NULL, '$2y$12$EPaJ4hGzc//2rwf/2s5SEOfkD5AI.lRD5G86q/Vg6XMFE/4JfiE.i', 'ADMIN', NULL, '2025-10-16 21:41:24', '2025-10-16 21:41:24'),
(32, 'a a', 'a@pol.es', NULL, '$2y$12$1je85TrEF3zZ2Tj.TCdz2uUtjZwVnTxxkPZplIokWnPHRb8u8kA1i', 'LIDER_SEMILLERO', NULL, '2025-10-16 21:44:54', '2025-10-16 21:44:54'),
(33, 'Carlo Gambino', 'cc@pol.es', NULL, '$2y$12$BYn5TZ6KeB4P/Q7cDBiLYuWMT1VZJ5KTypPtHCSKycWCoG0AbOmbi', 'APRENDIZ', NULL, '2025-10-16 21:45:59', '2025-10-28 00:30:09'),
(38, 'l l', 'lider1@gmail.com', NULL, '$2y$12$BNhxLY2AQhj2ymowbt8HR.QkKvfdhjsCWWE35aLfCOWHvT/3JVYDe', 'LIDER_SEMILLERO', NULL, '2025-10-17 02:45:51', '2025-10-17 02:45:51'),
(39, 'f f', 'f@gmail.com', NULL, '$2y$12$.fBOLu51FeWxoe6VIhEn1ekQiNG8CoGigu8XNpXwC5gcaIp1onGwO', 'ADMIN', NULL, '2025-10-17 02:48:02', '2025-10-17 02:48:02'),
(42, 'L Lopez', 'l@pol.es', NULL, '$2y$12$V0iOHlF/rse.XLMmN/4mYufObNCfL6xwcf8lI/Q9pn7mAa85N2f5C', 'LIDER_SEMILLERO', NULL, '2025-10-17 20:41:16', '2025-10-17 20:41:16'),
(43, 'a a', 'aa@pol.es', NULL, '$2y$12$QAlKGal7.JtK5AnQ0p9SEOOY16ku00xIH4f9EC8lEuX2kodbY5eDy', 'APRENDIZ', NULL, '2025-10-22 00:56:14', '2025-10-22 01:07:43');

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
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archivos_user_id_foreign` (`user_id`);

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
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documento_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `eventos_id_lider_foreign` (`id_lider`),
  ADD KEY `eventos_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_participantes_id_evento_id_aprendiz_unique` (`id_evento`,`id_aprendiz`);

--
-- Indices de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `evidencias_id_usuario_index` (`id_usuario`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD UNIQUE KEY `id_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `grupo_aprendices`
--
ALTER TABLE `grupo_aprendices`
  ADD PRIMARY KEY (`id_grupo_aprendiz`),
  ADD UNIQUE KEY `uq_grupo_aprendiz` (`id_grupo`,`id_usuario`),
  ADD KEY `fk_ga_aprendiz_perfil` (`id_usuario`);

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
-- Indices de la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `lider_general`
--
ALTER TABLE `lider_general`
  ADD PRIMARY KEY (`id_usuario`);

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
  ADD KEY `fk_proyecto_tipo` (`id_tipo_proyecto`);

--
-- Indices de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_user_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD PRIMARY KEY (`id_semillero`),
  ADD UNIQUE KEY `nombre_semillero` (`nombre_semillero`),
  ADD KEY `fk_semillero_liderperfil` (`id_lider_usuario`);

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
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  MODIFY `id_evidencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  ADD CONSTRAINT `aprendices_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD CONSTRAINT `archivos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_documento_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_id_lider_foreign` FOREIGN KEY (`id_lider`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventos_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD CONSTRAINT `fk_ep_aprendiz_perfil` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ep_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD CONSTRAINT `evidencias_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evidencias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupo_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo_aprendices`
--
ALTER TABLE `grupo_aprendices`
  ADD CONSTRAINT `fk_ga_aprendiz_perfil` FOREIGN KEY (`id_usuario`) REFERENCES `aprendices` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ga_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD CONSTRAINT `lideres_semillero_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lider_general`
--
ALTER TABLE `lider_general`
  ADD CONSTRAINT `lider_general_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyecto_semillero` FOREIGN KEY (`id_semillero`) REFERENCES `lideres_semillero` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  ADD CONSTRAINT `proyecto_user_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proyecto_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD CONSTRAINT `fk_semillero_liderperfil` FOREIGN KEY (`id_lider_usuario`) REFERENCES `lideres_semillero` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
