-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 20-10-2025 a las 15:59:59
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
DROP DATABASE IF EXISTS `cide_sena`;
CREATE DATABASE IF NOT EXISTS `cide_sena` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cide_sena`;

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
(33, 'c a', 'cc@pol.es', NULL, '$2y$12$BYn5TZ6KeB4P/Q7cDBiLYuWMT1VZJ5KTypPtHCSKycWCoG0AbOmbi', 'APRENDIZ', NULL, '2025-10-16 21:45:59', '2025-10-20 18:56:42'),
(36, 'j j', 'kebosi1177@fogdiver.com', NULL, '$2y$12$LRznCqIl/OZFpbp2vh6vdOi5tC5KsTaJ0XRlXaOrZarZ6Z8ZtuK5C', 'ADMIN', NULL, '2025-10-17 01:49:56', '2025-10-17 01:49:56'),
(38, 'l l', 'lider1@gmail.com', NULL, '$2y$12$BNhxLY2AQhj2ymowbt8HR.QkKvfdhjsCWWE35aLfCOWHvT/3JVYDe', 'LIDER_SEMILLERO', NULL, '2025-10-17 02:45:51', '2025-10-17 02:45:51'),
(39, 'f f', 'f@gmail.com', NULL, '$2y$12$.fBOLu51FeWxoe6VIhEn1ekQiNG8CoGigu8XNpXwC5gcaIp1onGwO', 'ADMIN', NULL, '2025-10-17 02:48:02', '2025-10-17 02:48:02'),
(40, 'Laura Lopez', 'laura@gmail.com', NULL, '$2y$12$J8WWaqjPxPMWz0.EoaRcxudPASEdC7PK4cQEH7r.POSZAc5AhHTBm', 'ADMIN', NULL, '2025-10-17 19:49:07', '2025-10-17 19:49:07'),
(42, 'L Lopez', 'l@pol.es', NULL, '$2y$12$V0iOHlF/rse.XLMmN/4mYufObNCfL6xwcf8lI/Q9pn7mAa85N2f5C', 'LIDER_SEMILLERO', NULL, '2025-10-17 20:41:16', '2025-10-17 20:41:16');


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
<<<<<<< HEAD
(37, 'maria', 'mogoñon', '2025-10-21 00:25:20', '2025-10-21 00:25:20'),
(38, 'deivit', 'mogoñon', '2025-10-21 01:10:49', '2025-10-21 01:10:49'),
(39, 'harol', 'torres', '2025-10-21 01:34:23', '2025-10-21 01:34:23'),
(43, 'joaquin', 'cañon', '2025-10-21 19:23:45', '2025-10-21 19:23:45');
=======
(30, 'maria', 'torres', '2025-10-17 01:21:34', '2025-10-17 01:21:34'),
(31, 'k', 'r', '2025-10-16 21:41:24', '2025-10-16 21:41:24'),
(36, 'j', 'j', '2025-10-17 01:49:56', '2025-10-17 01:49:56'),
(39, 'f', 'f', '2025-10-17 02:48:02', '2025-10-17 02:48:02'),
(40, 'Laura', 'Lopez', '2025-10-17 19:49:07', '2025-10-17 19:49:07');
>>>>>>> Kev-rama

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `ficha` varchar(30) NOT NULL,
  `programa` varchar(160) NOT NULL,
  `id_tipo_documento` int(11) DEFAULT NULL,
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

INSERT INTO `aprendices` (`id_usuario`, `nombres`, `apellidos`, `ficha`, `programa`, `tipo_documento`, `documento`, `celular`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `creado_en`, `actualizado_en`) VALUES
(26, 'joaquin', 'cañon', '2848527', 'adso', 'CC', '1012443507', '3053970242', 'hola11@hotmail.com', 'danielcf97@hotmail.com', NULL, NULL, '2025-10-16 15:03:39', '2025-10-16 15:03:39'),
(33, 'c', 'c', '1222', 'Adso', 'CC', '2', '12', 'c@sena.edu.co', 'c@pol.es', NULL, NULL, '2025-10-16 16:45:59', '2025-10-16 16:45:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `nombre_almacenado` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `subido_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('laravel-cache-admin@hola.com|127.0.0.1', 'i:1;', 1760712540),
('laravel-cache-admin@hola.com|127.0.0.1:timer', 'i:1760712540;', 1760712540),
('laravel-cache-c@pol.es|127.0.0.1', 'i:3;', 1760967300),
('laravel-cache-c@pol.es|127.0.0.1:timer', 'i:1760967300;', 1760967300),
('laravel-cache-llopez@gmail.com|127.0.0.1', 'i:1;', 1760714223),
('laravel-cache-llopez@gmail.com|127.0.0.1:timer', 'i:1760714223;', 1760714223),
('laravel-cache-luis111@hotmail.com|127.0.0.1', 'i:1;', 1760715675),
('laravel-cache-luis111@hotmail.com|127.0.0.1:timer', 'i:1760715675;', 1760715675),
('laravel-cache-s@pol.es|127.0.0.1', 'i:1;', 1760636918),
('laravel-cache-s@pol.es|127.0.0.1:timer', 'i:1760636918;', 1760636918),
('laravel-cache-wquinteroc@sena.edu.co|127.0.0.1', 'i:1;', 1760711351),
('laravel-cache-wquinteroc@sena.edu.co|127.0.0.1:timer', 'i:1760711351;', 1760711351);

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
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_tipo_documento` int(11) DEFAULT NULL
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
  `nombres` varchar(120) DEFAULT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Correo_institucional` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(5, '2025_10_17_163819_create_archivos_table', 3);

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
('HCijWpF0NBP0XiuoTZD8cS6U6ADcKPFRLpu5XFtp', 33, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUdMVWdOMWFmd3NCQnc4QlFSSDMwMUdxZ2Uwdkx5bHE4bE9QMkNrZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMzt9', 1760968614),
('zKVnJzlZ9dRUIf5iEeCgOQZbZQ85HAGfu0LDLJVj', 33, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNkNJb2tWTER3dHREdWh6c2F1M2pya05qU2dVdE8yMlRKM29RS2ZqQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMzt9', 1760737659);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `archivos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  ADD CONSTRAINT `proyecto_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
