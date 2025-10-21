-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2025 a las 23:33:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.4.13

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
  `nombres` varchar(120) NOT NULL,
  `apellido` varchar(120) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_usuario`, `nombres`, `apellido`, `creado_en`, `actualizado_en`) VALUES
(45, 'Sergio', NULL, '2025-10-22 02:06:33', '2025-10-22 02:06:33'),
(57, 'Andrés', NULL, '2025-10-22 02:30:33', '2025-10-22 02:30:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_aprendiz` int(11) NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellido` varchar(120) DEFAULT NULL,
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

INSERT INTO `aprendices` (`id_aprendiz`, `id_usuario`, `nombres`, `apellido`, `ficha`, `programa`, `tipo_documento`, `documento`, `celular`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `creado_en`, `actualizado_en`) VALUES
(7, 7, 'Joaquin cañon', NULL, '', '', NULL, '', NULL, 'test2@gmail.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(8, NULL, 'Joaquin cañon', NULL, '2848527', 'ADSO', 'CC', '1012443507', '3053970242', 'joaquin_canon@soy.sena.edu.co', 'test3@gmail.com', NULL, NULL, '2025-10-15 20:50:17', '2025-10-15 20:50:17'),
(20, 20, 'Laura Martínez', NULL, '', '', NULL, '', NULL, 'laura@sena.edu.co', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(21, 21, 'Carlos Pérez', NULL, '', '', NULL, '', NULL, 'carlos@sena.edu.co', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(25, 25, 'Laura Rodríguez', NULL, '', '', NULL, '', NULL, 'laura.rod@example.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(26, 26, 'Carlos Gómez', NULL, '', '', NULL, '', NULL, 'carlos.gomez@example.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(27, 27, 'Valentina Ruiz', NULL, '', '', NULL, '', NULL, 'valentina.ruiz@example.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(28, 28, 'Andrés Pérez', NULL, '', '', NULL, '', NULL, 'andres.perez@example.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48'),
(29, 29, 'María Castro', NULL, '', '', NULL, '', NULL, 'maria.castro@example.com', NULL, NULL, NULL, '2025-10-21 15:46:36', '2025-10-21 18:16:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendiz_proyecto`
--

CREATE TABLE `aprendiz_proyecto` (
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aprendiz_proyecto`
--

INSERT INTO `aprendiz_proyecto` (`id_aprendiz`, `id_proyecto`, `created_at`, `updated_at`) VALUES
(4, 3, '2025-10-21 15:51:32', '2025-10-21 15:51:32'),
(8, 3, '2025-10-21 16:31:28', '2025-10-21 16:31:28'),
(8, 5, '2025-10-21 15:53:07', '2025-10-21 15:56:03'),
(20, 2, '2025-10-21 18:22:03', '2025-10-21 18:22:03'),
(21, 1, '2025-10-21 18:01:12', '2025-10-21 18:01:12'),
(21, 4, '2025-10-21 15:53:07', '2025-10-21 15:55:17'),
(25, 4, '2025-10-21 15:53:07', '2025-10-21 15:55:10'),
(26, 3, '2025-10-21 15:53:07', '2025-10-21 15:55:54'),
(27, 1, '2025-10-21 18:01:12', '2025-10-21 18:01:12'),
(28, 2, '2025-10-21 18:22:03', '2025-10-21 18:22:03'),
(29, 5, '2025-10-21 15:53:07', '2025-10-21 15:56:10');

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
  `documento` varchar(255) NOT NULL,
  `fecha_subido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id_documento`, `id_proyecto`, `documento`, `fecha_subido`) VALUES
(1, 1, 'informe_agricultura.pdf', '2025-10-20 20:02:30'),
(2, 2, 'app_aprendizaje.pdf', '2025-10-20 20:02:30'),
(3, 3, 'energia_solar.pdf', '2025-10-20 20:02:30'),
(4, 4, 'reporte_ciberseguridad.pdf', '2025-10-20 20:02:30'),
(5, 5, 'robot_educativo.pdf', '2025-10-20 20:02:30');

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

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `id_proyecto`, `nombre_grupo`, `descripcion`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Semillero IA', 'Grupo de innovación agrícola', 'ACTIVO', '2025-10-20 20:02:37', '2025-10-20 20:02:37'),
(2, 2, 'Semillero App', 'Desarrollo de apps educativas', 'ACTIVO', '2025-10-20 20:02:37', '2025-10-20 20:02:37'),
(3, 3, 'Semillero Energía', 'Investigación solar', 'INACTIVO', '2025-10-20 20:02:37', '2025-10-20 20:02:37'),
(4, 4, 'Semillero Ciberseguridad', 'Protección de redes', 'ACTIVO', '2025-10-20 20:02:37', '2025-10-20 20:02:37'),
(5, 5, 'Semillero Robótica', 'Robótica aplicada a la educación', 'ACTIVO', '2025-10-20 20:02:37', '2025-10-20 20:02:37');

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
  `id_lider_semi` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellido` varchar(120) DEFAULT NULL,
  `tipo_documento` varchar(5) DEFAULT NULL,
  `documento` varchar(40) NOT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_tipo_documento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lideres_semillero`
--

INSERT INTO `lideres_semillero` (`id_lider_semi`, `nombres`, `apellido`, `tipo_documento`, `documento`, `correo_institucional`, `creado_en`, `actualizado_en`, `id_tipo_documento`) VALUES
(5, 'Joaquin cañon', NULL, 'CC', '1012443507', 'test1@gmail.com', '2025-10-15 20:18:13', '2025-10-15 20:18:13', NULL),
(47, 'harol', 'pardo', 'CC', '15245515547', 'harol@hola.com', '2025-10-22 02:07:46', '2025-10-22 02:07:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lider_general`
--

CREATE TABLE `lider_general` (
  `id_lidergen` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellido` varchar(120) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Correo_institucional` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lider_general`
--

INSERT INTO `lider_general` (`id_lidergen`, `nombres`, `apellido`, `creado_en`, `actualizado_en`, `Correo_institucional`) VALUES
(19, 'hansbleidi', NULL, '2025-10-20 18:47:33', '2025-10-20 18:47:33', 'yurani@gmail.com'),
(46, 'maria', 'nova', '2025-10-22 02:07:07', '2025-10-22 02:07:07', 'mariaan@hola.com');

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
(5, '2025_10_15_152742_create_lideres_generales_table', 2),
(8, '2025_10_15_160035_add_nombre_to_admins_and_lideres_generales', 3),
(9, '2025_10_15_191526_update_id_tipo_documento_in_aprendices_and_lideres_semillero', 3),
(10, '2025_10_20_163848_create_semilleros_table', 4);

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
(1, 1, 1, 'IA para Agricultura', 'Proyecto de optimización de cultivos mediante IA', 'EN_EJECUCION', '2025-01-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(2, 2, 1, 'App de Aprendizaje SENA', 'Aplicación para gestión de aprendizajes', 'EN_FORMULACION', '2025-02-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(3, 3, 1, 'Sistema de Energía Solar', 'Investigación en paneles solares', 'FINALIZADO', '2024-09-01', '2025-02-01', '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(4, 4, 1, 'Monitor de Ciberseguridad', 'Monitoreo de redes locales', 'EN_EJECUCION', '2025-03-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(5, 5, 1, 'Robot Educativo', 'Robot con sensores para enseñanza STEAM', 'EN_FORMULACION', '2025-04-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semilleros`
--

CREATE TABLE `semilleros` (
  `id_semillero` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `semilleros`
--

INSERT INTO `semilleros` (`id_semillero`, `created_at`, `updated_at`) VALUES
(1, '2025-10-20 20:02:13', '2025-10-20 20:02:13'),
(2, '2025-10-20 20:02:13', '2025-10-20 20:02:13'),
(3, '2025-10-20 20:02:13', '2025-10-20 20:02:13'),
(4, '2025-10-20 20:02:13', '2025-10-20 20:02:13');

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
('x7F5YzBEBH4kM6tw7Og3wm45Ef6kfEXmAVptojWi', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTzBtMUlvNDRXY1pEMHROR3RsVVN5UFZnYlJ2bjdSaVU1VWdoWk5KNiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1761082233);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `apellido` varchar(120) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `name`, `apellido`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(4, 'Joaquin cañon', NULL, 'test@gmail.com', NULL, '$2y$12$ESFF/wMQPumWmeMHt1/Ij.sOpCKgD1xnximeFE4zvCwctCLudRpt.', 'ADMIN', NULL, '2025-10-16 01:10:41', '2025-10-16 01:10:41'),
(5, 'Joaquin cañon', NULL, 'test1@gmail.com', NULL, '$2y$12$lmTgIhA2MR1UujyoiNm4ieUnBICj0B5jLKEPqq0Cwmgo8XKdNRzyy', 'LIDER_SEMILLERO', NULL, '2025-10-16 01:18:13', '2025-10-16 01:18:13'),
(7, 'Joaquin cañon', NULL, 'test2@gmail.com', NULL, '$2y$12$MzYpU1P2shOnKz6oSehUW.EHnmTqe70i5MMfr5o1B5gtf5NpjKMLu', 'APRENDIZ', NULL, '2025-10-16 01:48:48', '2025-10-16 01:48:48'),
(8, 'Joaquin cañon', NULL, 'test3@gmail.com', NULL, '$2y$12$CysY7mh6WuCxIc.j4vORxuqAEPzjDJr0lxxqSo.Q.8B0Q9caCicLW', 'APRENDIZ', NULL, '2025-10-16 01:50:17', '2025-10-16 01:50:17'),
(9, 'hansita', NULL, 'hanscard@20gmail.com', NULL, '$2y$12$BKJsJ8LlRHORZj/c4gIBqeU1u9Zt3lPlAiyOFjX23Ac084uYZpXR.', 'ADMIN', NULL, '2025-10-16 19:17:57', '2025-10-16 19:17:57'),
(19, 'hansbleidi cardenas', NULL, 'yurani@gmail.com', NULL, '$2y$12$9mTG3Dsy5lkI8d7ce6SwAOfrBGsUkVoLrLW.e0tnDBE35RspSLQWm', 'LIDER GENERAL', NULL, '2025-10-20 18:47:33', '2025-10-20 18:47:33'),
(20, 'Laura Martínez', NULL, 'laura@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(21, 'Carlos Pérez', NULL, 'carlos@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(22, 'Ana Torres', NULL, 'ana@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'LIDER_SEMILLERO', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(23, 'Miguel García', NULL, 'miguel@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'ADMIN', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(24, 'Lucía Rojas', NULL, 'lucia@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'LIDER GENERAL', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(25, 'Laura Rodríguez', NULL, 'laura.rod@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash1', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(26, 'Carlos Gómez', NULL, 'carlos.gomez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash2', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(27, 'Valentina Ruiz', NULL, 'valentina.ruiz@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash3', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(28, 'Andrés Pérez', NULL, 'andres.perez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash4', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(29, 'María Castro', NULL, 'maria.castro@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash5', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(45, 'Sergio', 'Morita', 'andres@hola.com', NULL, '$2y$12$m41vYMdVn5kcfCxaHAfrN.HAdpJg2KYAjXdNETx/UdSXJFxA2WnLG', 'ADMIN', NULL, '2025-10-22 02:06:33', '2025-10-22 02:06:33'),
(46, 'maria', 'nova', 'mariaan@hola.com', NULL, '$2y$12$jxv6hTLR.ZrBfQDbE6QnM.aidIndlgOmeKiOfbiw6ZFtQFYwFNl1i', 'LIDER_GENERAL', NULL, '2025-10-22 02:07:07', '2025-10-22 02:07:07'),
(47, 'harol', 'pardo', 'harol@hola.com', NULL, '$2y$12$Dm6MYUNT79ETFyY.hlwvBOqAKJSKViFiRiSY2rymC79wQmOCneM3m', 'LIDER_SEMILLERO', NULL, '2025-10-22 02:07:46', '2025-10-22 02:07:46'),
(57, 'Andrés', 'garcia', 'asda1@hola.com', NULL, '$2y$12$kYxFZenYW/G/S03Er4kKHOLEi06mW6cAX1XCHHS9dh8FEvQO.FCr2', 'ADMIN', NULL, '2025-10-22 02:30:33', '2025-10-22 02:30:33');

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
  ADD UNIQUE KEY `uk_aprendiz_usuario` (`id_usuario`),
  ADD KEY `idx_documento` (`documento`),
  ADD KEY `idx_ficha` (`ficha`),
  ADD KEY `idx_correo_institucional` (`correo_institucional`);

--
-- Indices de la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  ADD PRIMARY KEY (`id_aprendiz`,`id_proyecto`),
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
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documento_proyecto` (`id_proyecto`);

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
  ADD PRIMARY KEY (`id_lider_semi`);

--
-- Indices de la tabla `lider_general`
--
ALTER TABLE `lider_general`
  ADD PRIMARY KEY (`id_lidergen`);

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
-- Indices de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD PRIMARY KEY (`id_semillero`);

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
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id_aprendiz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  MODIFY `id_semillero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
  ADD CONSTRAINT `fk_aprendices_users` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `aprendiz_proyecto`
--
ALTER TABLE `aprendiz_proyecto`
  ADD CONSTRAINT `aprendiz_proyecto_ibfk_2` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD CONSTRAINT `fk_lideres_semillero_users` FOREIGN KEY (`id_lider_semi`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lider_general`
--
ALTER TABLE `lider_general`
  ADD CONSTRAINT `lideres_generales_id_usuario_foreign` FOREIGN KEY (`id_lidergen`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
