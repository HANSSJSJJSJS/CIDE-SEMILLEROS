-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-10-2025 a las 22:58:05
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
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

<<<<<<< HEAD
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
=======
INSERT INTO `administradores` (`id_usuario`, `nombres`, `apellidos`, `creado_en`, `actualizado_en`) VALUES
(43, 'Sergio', 'nova', '2025-10-22 21:29:19', '2025-10-22 21:29:19');
>>>>>>> origin/main

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_aprendiz` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
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

<<<<<<< HEAD
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
=======
INSERT INTO `aprendices` (`id_aprendiz`, `user_id`, `nombres`, `apellidos`, `nombre_completo`, `ficha`, `programa`, `tipo_documento`, `documento`, `celular`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `creado_en`, `actualizado_en`) VALUES
(7, NULL, 'Joaquin cañon', NULL, 'Joaquin cañon ', '35435345', 'adso', 'T.I', '8344873278', '3215678976', 'test2@gmail.com', 'example@gmail.com', 'gsdfd', '3456789642', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(8, NULL, 'Hansbleidi Cardenas', NULL, 'Hansbleidi Cardenas ', '2848527', 'ADSO', 'CC', '1071548288', '3053970242', 'hans@soy.sena.edu.co', 'test3@gmail.com', NULL, NULL, '2025-10-15 20:50:17', '2025-10-22 19:45:18'),
(20, NULL, 'Laura Martínez', NULL, 'Laura Martínez ', '38454893', 'Animacion 3D', 'C.E', '8237498234', '3470909094', 'laura@sena.edu.co', 'IKJEDWN@HSBXJ.COM', 'djnbcuibn', '3129098765', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(21, NULL, 'Carlos Pérez', NULL, 'Carlos Pérez ', '77788888', 'Adso', 'C.C', '76876876', '3700907888', 'carlos@sena.edu.co', 'hfbejhr@fdnjdn.com', 'ebfciewuab', '7263t43', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(25, NULL, 'Laura Rodríguez', NULL, 'Laura Rodríguez ', '7247747', 'Animacion 3D', 'C.C', '72364264', '32145567888', 'laura.rod@example.com', 'vdvcb@gjgjgj.com', 'uhbvusdb', '3457890078', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(26, NULL, 'Carlos Gómez', NULL, 'Carlos Gómez ', '35435345', 'Adso', 'T.I', '7373363663', '3456789009', 'carlos.gomez@example.com', 'gsggs@ifiuewf.com', 'hahhah', '3213456789', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(27, NULL, 'Valentina Ruiz', NULL, 'Valentina Ruiz ', '73478364', 'Animacion 3D', 'C.C', '7468584863', '3215678976', 'valentina.ruiz@example.com', 'example@gnmail.com', 'hahsabahs', '3467674356', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(28, NULL, 'Andrés Pérez', NULL, 'Andrés Pérez ', '62347826', 'ADSO', 'C.C', '847276487', '3456789023', 'andres.perez@example.com', 'dvasvfjh@dbd.com', '3216789045', '6472474127', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(29, NULL, 'María Castro', NULL, 'María Castro ', '35435345', 'animacion 3D', 'C.C', '7689367363', '3214789076', 'maria.castro@example.com', 'example123@gmail.com', 'hasahgvbwi', '3456789012', '2025-10-21 15:46:36', '2025-10-22 19:45:18'),
(30, NULL, 'Laura Martínez Pérez', NULL, 'Laura Martínez Pérez ', '2435345', 'Tecnología en Análisis y Desarrollo de Sistemas de Información', 'CC', '1001234567', '3004567890', 'laura.martinez@sena.edu.co', 'laura.perez@gmail.com', 'Carlos Pérez', '3102345678', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(31, NULL, 'Juan David Torres', NULL, 'Juan David Torres ', '2435346', 'Gestión Empresarial', 'TI', '1002234568', '3014567891', 'juan.torres@sena.edu.co', 'jdavidtorres@yahoo.com', 'Martha Torres', '3103345679', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(32, NULL, 'Mariana Gómez Ríos', NULL, 'Mariana Gómez Ríos ', '2435347', 'Diseño Gráfico Digital', 'CC', '1003234569', '3024567892', 'mariana.gomez@sena.edu.co', 'marianagomez@hotmail.com', 'Luis Ríos', '3104345680', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(33, NULL, 'Carlos Andrés Suárez', NULL, 'Carlos Andrés Suárez ', '2435348', 'Contabilidad y Finanzas', 'CE', '1004234570', '3034567893', 'carlos.suarez@sena.edu.co', 'carsuarez@gmail.com', 'Sandra Suárez', '3105345681', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(34, NULL, 'Ana Milena López', NULL, 'Ana Milena López ', '2435349', 'Gestión del Talento Humano', 'CC', '1005234571', '3044567894', 'ana.lopez@sena.edu.co', 'anamilena@gmail.com', 'Diego López', '3106345682', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(35, NULL, 'Esteban Ramírez Torres', NULL, 'Esteban Ramírez Torres ', '2435350', 'Producción Multimedia', 'TI', '1006234572', '3054567895', 'esteban.ramirez@sena.edu.co', 'eramirez@hotmail.com', 'Natalia Torres', '3107345683', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(36, NULL, 'Luisa Fernanda Salazar', NULL, 'Luisa Fernanda Salazar ', '2435351', 'Seguridad y Salud en el Trabajo', 'CC', '1007234573', '3064567896', 'luisa.salazar@sena.edu.co', 'lusalazar@gmail.com', 'Camilo Salazar', '3108345684', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(37, NULL, 'David Alejandro Méndez', NULL, 'David Alejandro Méndez ', '2435352', 'Mecatrónica', 'CE', '1008234574', '3074567897', 'david.mendez@sena.edu.co', 'damendez@gmail.com', 'Paola Méndez', '3109345685', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(38, NULL, 'Juliana Herrera Osorio', NULL, 'Juliana Herrera Osorio ', '2435353', 'Marketing Digital', 'CC', '1009234575', '3084567898', 'juliana.herrera@sena.edu.co', 'jherrera@gmail.com', 'Hernán Herrera', '3110345686', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(39, NULL, 'Santiago López Vargas', NULL, 'Santiago López Vargas ', '2435354', 'Desarrollo de Videojuegos', 'TI', '1010234576', '3094567899', 'santiago.lopez@sena.edu.co', 'slopezvargas@gmail.com', 'Andrea Vargas', '3111345687', '2025-10-21 19:41:11', '2025-10-22 19:45:18'),
(40, 46, 'Sergio', 'Morita', 'Sergio Morita', '28485274', 'Adsoa', 'TI', '15457845', '54648961', 'asjo@hotmail.com', 'asd2223@hola.com', 'sias', NULL, '2025-10-22 21:48:14', '2025-10-22 19:45:18'),
(41, NULL, 'Sin', 'Asignar', 'Sin Asignar', '0000000', 'N/A', 'CC', 'SIN_ASIGNAR', '0000000000', 'sin.asignar@sena.edu.co', 'sin.asignar@sena.edu.co', 'N/A', '0000000000', '2025-10-22 21:23:03', '2025-10-22 21:23:03');
>>>>>>> origin/main

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
(1, 1, 0, NULL, NULL, 'informe_agricultura.pdf', '', 'OTRO', NULL, NULL, NULL, '2025-10-20 15:02:30', NULL, NULL, '2025-10-22 09:37:06', 'aprobado', NULL, NULL),
(2, 2, 0, NULL, NULL, 'app_aprendizaje.pdf', '', 'OTRO', NULL, NULL, NULL, '2025-10-20 15:02:30', NULL, NULL, '2025-10-22 09:37:06', 'pendiente', NULL, NULL),
(3, 3, 0, NULL, NULL, 'energia_solar.pdf', '', 'OTRO', NULL, NULL, NULL, '2025-10-20 15:02:30', NULL, NULL, '2025-10-22 09:37:06', 'pendiente', NULL, NULL),
(4, 4, 0, NULL, NULL, 'reporte_ciberseguridad.pdf', '', 'OTRO', NULL, NULL, NULL, '2025-10-20 15:02:30', NULL, NULL, '2025-10-22 09:37:06', 'pendiente', NULL, NULL),
(5, 5, 0, NULL, NULL, 'robot_educativo.pdf', '', 'OTRO', NULL, NULL, NULL, '2025-10-20 15:02:30', NULL, NULL, '2025-10-22 09:37:06', 'pendiente', NULL, NULL),
(14, 2, 34, NULL, NULL, 'PLACEHOLDER_34', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:06:30', NULL, NULL, '2025-10-22 20:06:30', 'pendiente', NULL, NULL),
(15, 2, 33, NULL, NULL, 'PLACEHOLDER_33', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:06:30', NULL, NULL, '2025-10-22 20:06:30', 'rechazado', NULL, NULL),
(20, 3, 28, NULL, NULL, 'PLACEHOLDER_28', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:31', NULL, NULL, '2025-10-22 20:07:31', 'pendiente', NULL, NULL),
(21, 3, 39, NULL, NULL, 'PLACEHOLDER_39', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:31', NULL, NULL, '2025-10-22 20:07:31', 'pendiente', NULL, NULL),
(22, 3, 37, NULL, NULL, 'PLACEHOLDER_37', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:31', NULL, NULL, '2025-10-22 20:07:31', 'pendiente', NULL, NULL),
(23, 3, 8, NULL, NULL, 'PLACEHOLDER_8', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:31', NULL, NULL, '2025-10-22 20:07:31', 'pendiente', NULL, NULL),
(28, 5, 34, NULL, NULL, 'PLACEHOLDER_34', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:52', NULL, NULL, '2025-10-22 20:07:52', 'pendiente', NULL, NULL),
(29, 5, 37, NULL, NULL, 'PLACEHOLDER_37', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:52', NULL, NULL, '2025-10-22 20:07:52', 'pendiente', NULL, NULL),
(30, 5, 30, NULL, NULL, 'PLACEHOLDER_30', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:52', NULL, NULL, '2025-10-22 20:07:52', 'pendiente', NULL, NULL),
(31, 5, 39, NULL, NULL, 'PLACEHOLDER_39', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:07:52', NULL, NULL, '2025-10-22 20:07:52', 'pendiente', NULL, NULL),
(35, 4, 28, NULL, NULL, 'PLACEHOLDER_28', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:08:10', NULL, NULL, '2025-10-22 20:08:10', 'pendiente', NULL, NULL),
(36, 4, 39, NULL, NULL, 'PLACEHOLDER_39', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:08:10', NULL, NULL, '2025-10-22 20:08:10', 'pendiente', NULL, NULL),
(37, 4, 8, NULL, NULL, 'PLACEHOLDER_8', '', 'OTRO', NULL, NULL, NULL, '2025-10-22 15:08:10', NULL, NULL, '2025-10-22 20:08:10', 'pendiente', NULL, NULL),
(59, 1, 41, NULL, NULL, 'dbdrwbwrb', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:23:03', NULL, 0, '2025-10-22 16:23:03', 'aprobado', '2025-10-24', 'vsdbv dwbrw'),
(60, 5, 41, NULL, NULL, 'tee', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:32:34', NULL, 0, '2025-10-22 16:32:34', 'pendiente', '2025-10-28', 'en grvmgry'),
(61, 5, 41, NULL, NULL, 'gvhvh', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:33:18', NULL, 0, '2025-10-22 16:33:18', 'pendiente', '2025-11-01', 'frn n'),
(62, 5, 41, NULL, NULL, 'AVANECE', '', 'PDF', NULL, 'pdf', '', '2025-10-22 21:35:46', NULL, 0, '2025-10-22 16:35:46', 'pendiente', '2025-11-01', 'ettenymy'),
(63, 5, 41, NULL, NULL, 'brfvbre', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:36:31', NULL, 0, '2025-10-22 16:36:31', 'pendiente', '2025-10-24', 'rebfbb'),
(64, 4, 41, NULL, NULL, 'fv  ffbs', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:38:35', NULL, 0, '2025-10-22 16:38:35', 'pendiente', '2025-10-23', 'gndnfdn'),
(65, 5, 41, NULL, NULL, 'zvxdbd', '', 'ENLACE', NULL, 'enlace', '', '2025-10-22 21:38:58', NULL, 0, '2025-10-22 16:38:58', 'pendiente', '2025-10-25', 'zvdsgvdsb'),
(66, 1, 28, NULL, NULL, 'fbrrdbre', '', 'PDF', NULL, 'pdf', '', '2025-10-23 13:14:59', NULL, 0, '2025-10-23 08:14:59', 'rechazado', '2025-10-06', 'NEVNEJDVN'),
(67, 5, 39, NULL, NULL, 'AVANECE', '', 'ENLACE', NULL, 'enlace', '', '2025-10-23 13:15:35', NULL, 0, '2025-10-23 08:15:35', 'pendiente', '2025-10-25', 'ENLACE AL DM'),
(68, 1, 30, NULL, NULL, 'fbrrdbre', '', 'ENLACE', NULL, 'enlace', '', '2025-10-23 13:32:23', NULL, 0, '2025-10-23 08:32:23', 'aprobado', '2025-10-25', 'sdvsdb'),
(69, 1, 30, NULL, NULL, 'AVANECE', '', 'ENLACE', NULL, 'enlace', '', '2025-10-23 13:49:03', NULL, 0, '2025-10-23 08:49:03', 'aprobado', '2025-10-25', 'nh mj'),
(70, 1, 28, NULL, NULL, 'AVANECE', '', 'ENLACE', NULL, 'enlace', '', '2025-10-23 16:00:14', NULL, 0, '2025-10-23 11:00:14', 'aprobado', '2025-10-26', 'dg nf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_lider` bigint(20) UNSIGNED DEFAULT NULL,
  `id_proyecto` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion` int(11) NOT NULL DEFAULT 60,
  `ubicacion` varchar(255) DEFAULT NULL,
  `link_virtual` varchar(255) DEFAULT NULL,
  `codigo_reunion` varchar(255) DEFAULT NULL,
  `recordatorio` varchar(255) NOT NULL DEFAULT 'none',
  `tipo` varchar(255) NOT NULL DEFAULT 'reunion',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `id_lider`, `id_proyecto`, `titulo`, `descripcion`, `fecha_hora`, `duracion`, `ubicacion`, `link_virtual`, `codigo_reunion`, `recordatorio`, `tipo`, `created_at`, `updated_at`) VALUES
(3, 5, 1, 'reuni avances', NULL, '2025-10-29 13:04:00', 60, NULL, NULL, NULL, 'none', 'reunion', '2025-10-23 18:59:32', '2025-10-23 18:59:32'),
(5, 5, 2, 'db dfb', NULL, '2025-10-23 13:04:00', 30, 'virtual', NULL, NULL, 'none', 'planificacion', '2025-10-23 19:15:29', '2025-10-23 19:15:29'),
(6, 1, 1, 'Lanzamiento del Proyecto Alpha', 'Evento de apertura para el nuevo proyecto Alpha.', '2025-10-25 09:00:00', 120, 'Auditorio Principal', NULL, NULL, '2025-10-24 09:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(7, 2, 1, 'Reunión de Seguimiento', 'Revisión de avances y próximos pasos del proyecto.', '2025-10-27 10:00:00', 90, 'Sala 3', NULL, NULL, '2025-10-26 10:00:00', 'Virtual', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(8, 1, 2, 'Capacitación en Seguridad', 'Taller sobre seguridad laboral para aprendices.', '2025-10-30 14:00:00', 180, 'Laboratorio A', NULL, NULL, '2025-10-29 14:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(9, 3, 2, 'Presentación de Resultados', 'Exposición de resultados obtenidos en el trimestre.', '2025-11-02 15:00:00', 60, 'Auditorio B', NULL, NULL, '2025-11-01 15:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(10, 4, 3, 'Charla Motivacional', 'Invitado especial para motivar a los aprendices.', '2025-11-05 08:30:00', 90, 'Sala 1', NULL, NULL, '2025-11-04 08:30:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(11, 5, 3, 'Reunión de Coordinadores', 'Planificación de nuevas actividades.', '2025-11-07 11:00:00', 120, 'Oficina Central', NULL, NULL, '2025-11-06 11:00:00', 'Virtual', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(12, 2, 4, 'Demostración de Proyecto', 'Los aprendices mostrarán prototipos funcionales.', '2025-11-10 09:00:00', 180, 'Taller 2', NULL, NULL, '2025-11-09 09:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(13, 3, 4, 'Taller de Innovación', 'Actividad para fomentar ideas nuevas.', '2025-11-12 13:00:00', 150, 'Laboratorio B', NULL, NULL, '2025-11-11 13:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(14, 1, 5, 'Conferencia Técnica', 'Actualización sobre nuevas tecnologías.', '2025-11-15 10:00:00', 120, 'Auditorio Principal', NULL, NULL, '2025-11-14 10:00:00', 'Virtual', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(15, 4, 5, 'Clausura de Ciclo', 'Cierre del ciclo formativo con entrega de reconocimientos.', '2025-11-18 16:00:00', 90, 'Auditorio Central', NULL, NULL, '2025-11-17 16:00:00', 'Presencial', '2025-10-23 14:46:30', '2025-10-23 14:46:30'),
(16, 5, 4, 'reuni avances', '7jikmijmjm', '2025-10-31 14:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, '30', 'planificacion', '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(17, 5, 4, 'reuni avances', '7jikmijmjm', '2025-10-31 14:00:00', 60, 'virtual', 'https://teams.live.com/meet/9334791416848?p=mO2SOOhyYocduG7zHy', NULL, '30', 'planificacion', '2025-10-23 20:43:34', '2025-10-25 01:27:35'),
(18, 5, 5, 'reuni avances', 'gvbhjkvfgbhnj', '2025-10-23 11:00:00', 30, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'seguimiento', '2025-10-23 20:44:45', '2025-10-23 20:44:45'),
(19, 5, 5, 'reuni avances', 'gvbhjkvfgbhnj', '2025-10-23 11:00:00', 30, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'seguimiento', '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(20, 5, 1, 'reuni avances', NULL, '2025-10-30 17:00:00', 60, 'virtual', 'https://teams.live.com/meet/9371884286688?p=SQLIrUJ2aesIOHmaAC', NULL, '60', 'planificacion', '2025-10-23 21:02:38', '2025-10-25 01:18:10'),
(22, 5, 1, 'reuni avances', 'jnvjqbnaiequ', '2025-10-24 11:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'general', '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(23, 5, 1, 'reuni avances', 'jnvjqbnaiequ', '2025-10-24 11:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, 'none', 'general', '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(24, 5, NULL, 'reunion seguimiento', 'hgfdxc', '2025-11-20 17:00:00', 90, 'sala2', NULL, NULL, 'none', 'capacitacion', '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(25, 5, NULL, 'reunion seguimiento', 'hgfdxc', '2025-11-20 17:00:00', 90, 'sala2', NULL, NULL, 'none', 'capacitacion', '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(26, 5, NULL, 'reuni avances', 'gfnfgn fgn', '2025-10-24 14:00:00', 60, 'lab1', NULL, NULL, '60', 'seguimiento', '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(27, 5, NULL, 'reuni avances', 'gfnfgn fgn', '2025-10-24 14:00:00', 60, 'lab1', NULL, NULL, '60', 'seguimiento', '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(28, 5, NULL, 'reunion seguimiento', 'DVBBB', '2025-11-04 15:00:00', 60, 'otra', NULL, NULL, 'none', 'capacitacion', '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(29, 5, NULL, 'reunion seguimiento', 'DVBBB', '2025-11-04 15:00:00', 60, 'otra', NULL, NULL, 'none', 'capacitacion', '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(30, 5, 4, 'reunion seguimiento', NULL, '2025-10-29 14:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, '15', 'general', '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(31, 5, 4, 'reunion seguimiento', NULL, '2025-10-29 14:00:00', 60, 'virtual', 'https://meet.google.com/vxf-idzq-mty', NULL, '15', 'general', '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(32, 5, 1, 'reuni avances', NULL, '2025-10-27 10:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_ZNb008X3Ue%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reuni+avances', 'buMXvW8fUy', '15', 'revision', '2025-10-24 23:57:44', '2025-10-24 23:57:44'),
(33, 5, NULL, 'reunion seguimiento', NULL, '2025-10-28 14:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_TysBFAcEoi%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reunion+seguimiento', 'yzTipnUaEL', '60', 'capacitacion', '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(34, 5, NULL, 'reunion seguimiento', NULL, '2025-10-28 14:00:00', 60, 'virtual', 'https://teams.live.com/meet/9368178641679?p=9Dnnb65dcUG0oZMBCO', 'ONjR9CQgJi', '60', 'capacitacion', '2025-10-25 00:15:05', '2025-10-25 01:34:14'),
(35, 5, NULL, 'RBRBEQ', NULL, '2025-11-12 10:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_VHU8wJULUo%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=RBRBEQ', 'N02AAlHcMb', '60', 'general', '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(36, 5, NULL, 'RBRBEQ', NULL, '2025-11-12 10:00:00', 60, 'virtual', 'https://teams.live.com/meet/9368178641679?p=9Dnnb65dcUG0oZMBCO', '8Cw0b1B1Kz', '60', 'general', '2025-10-25 00:21:52', '2025-10-25 01:23:23'),
(37, 5, NULL, 'RBRBEQ', NULL, '2025-11-18 17:00:00', 60, 'virtual', 'https://teams.live.com/meet/9368178641679?p=9Dnnb65dcUG0oZMBCO', 'Wdo7BWtZzt', '15', 'general', '2025-10-25 00:33:35', '2025-10-25 01:24:40'),
(38, 5, NULL, 'RBRBEQ', NULL, '2025-11-18 17:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_1AnKWLreaQ%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=RBRBEQ', 'VlGpfy4kUE', '15', 'general', '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(39, 5, NULL, 'reuni avances', NULL, '2025-11-26 14:00:00', 60, 'virtual', NULL, NULL, '15', 'general', '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(40, 5, NULL, 'reuni avances', NULL, '2025-11-26 14:00:00', 60, 'virtual', NULL, NULL, '15', 'general', '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(41, 5, 1, 'reunion seguimiento', NULL, '2025-11-21 15:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_m0CFnUADMa%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reunion+seguimiento', 'KLFeyF2VIQ', '15', 'general', '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(42, 5, 1, 'reunion seguimiento', NULL, '2025-11-21 15:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_jqVdLttuXy%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reunion+seguimiento', '8n7ecqi7R1', '15', 'general', '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(43, 5, 3, 'reunion seguimiento', NULL, '2025-11-13 14:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_fZntVhCyB2%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reunion+seguimiento', 'sMcBSZY2Ot', '15', 'general', '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(44, 5, 3, 'reunion seguimiento', NULL, '2025-11-13 14:00:00', 60, 'virtual', 'https://teams.microsoft.com/l/meetup-join/19%3Ameeting_DYuHv6l4lE%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%225%22%7D&subject=reunion+seguimiento', 'Nv92SKENbH', '15', 'general', '2025-10-25 01:50:27', '2025-10-25 01:50:27');

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
(1, 1, 1, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(2, 1, 2, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(5, 3, 5, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(6, 3, 6, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(8, 5, 8, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(9, 6, 9, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(10, 7, 10, '2025-10-23 14:46:42', '2025-10-23 14:46:42'),
(11, 16, 28, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(12, 16, 8, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(13, 16, 39, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(14, 17, 28, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(15, 17, 8, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(16, 17, 39, '2025-10-23 20:43:34', '2025-10-23 20:43:34'),
(17, 18, 34, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(18, 18, 37, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(19, 18, 30, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(20, 18, 39, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(21, 19, 34, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(22, 19, 37, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(23, 19, 30, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(24, 19, 39, '2025-10-23 20:44:46', '2025-10-23 20:44:46'),
(25, 20, 28, '2025-10-23 21:02:38', '2025-10-23 21:02:38'),
(26, 20, 30, '2025-10-23 21:02:38', '2025-10-23 21:02:38'),
(29, 22, 34, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(30, 22, 28, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(31, 22, 33, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(32, 22, 26, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(33, 22, 21, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(34, 22, 37, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(35, 22, 35, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(36, 22, 8, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(37, 22, 7, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(38, 22, 31, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(39, 22, 38, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(40, 22, 20, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(41, 22, 30, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(42, 22, 25, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(43, 22, 36, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(44, 22, 29, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(45, 22, 32, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(46, 22, 39, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(47, 22, 40, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(48, 22, 27, '2025-10-23 23:15:09', '2025-10-23 23:15:09'),
(49, 23, 34, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(50, 23, 28, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(51, 23, 33, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(52, 23, 26, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(53, 23, 21, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(54, 23, 37, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(55, 23, 35, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(56, 23, 8, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(57, 23, 7, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(58, 23, 31, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(59, 23, 38, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(60, 23, 20, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(61, 23, 30, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(62, 23, 25, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(63, 23, 36, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(64, 23, 29, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(65, 23, 32, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(66, 23, 39, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(67, 23, 40, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(68, 23, 27, '2025-10-23 23:15:10', '2025-10-23 23:15:10'),
(69, 24, 34, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(70, 24, 28, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(71, 24, 33, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(72, 24, 26, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(73, 24, 21, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(74, 24, 37, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(75, 24, 35, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(76, 24, 8, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(77, 24, 7, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(78, 24, 31, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(79, 24, 38, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(80, 24, 20, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(81, 24, 30, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(82, 24, 25, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(83, 24, 36, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(84, 24, 29, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(85, 24, 32, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(86, 24, 39, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(87, 24, 40, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(88, 24, 27, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(89, 25, 34, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(90, 25, 28, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(91, 25, 33, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(92, 25, 26, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(93, 25, 21, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(94, 25, 37, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(95, 25, 35, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(96, 25, 8, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(97, 25, 7, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(98, 25, 31, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(99, 25, 38, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(100, 25, 20, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(101, 25, 30, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(102, 25, 25, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(103, 25, 36, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(104, 25, 29, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(105, 25, 32, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(106, 25, 39, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(107, 25, 40, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(108, 25, 27, '2025-10-24 18:49:06', '2025-10-24 18:49:06'),
(109, 26, 34, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(110, 26, 28, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(111, 26, 33, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(112, 26, 26, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(113, 26, 21, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(114, 26, 37, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(115, 26, 35, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(116, 26, 8, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(117, 26, 7, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(118, 26, 31, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(119, 26, 38, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(120, 26, 20, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(121, 26, 30, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(122, 26, 25, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(123, 26, 36, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(124, 26, 29, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(125, 26, 32, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(126, 26, 39, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(127, 26, 40, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(128, 26, 27, '2025-10-24 18:54:03', '2025-10-24 18:54:03'),
(129, 27, 34, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(130, 27, 28, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(131, 27, 33, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(132, 27, 26, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(133, 27, 21, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(134, 27, 37, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(135, 27, 35, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(136, 27, 8, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(137, 27, 7, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(138, 27, 31, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(139, 27, 38, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(140, 27, 20, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(141, 27, 30, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(142, 27, 25, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(143, 27, 36, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(144, 27, 29, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(145, 27, 32, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(146, 27, 39, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(147, 27, 40, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(148, 27, 27, '2025-10-24 18:54:04', '2025-10-24 18:54:04'),
(149, 28, 34, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(150, 28, 28, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(151, 28, 33, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(152, 28, 26, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(153, 28, 21, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(154, 28, 37, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(155, 28, 35, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(156, 28, 8, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(157, 28, 7, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(158, 28, 31, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(159, 28, 38, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(160, 28, 20, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(161, 28, 30, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(162, 28, 25, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(163, 28, 36, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(164, 28, 29, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(165, 28, 32, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(166, 28, 39, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(167, 28, 40, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(168, 28, 27, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(169, 29, 34, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(170, 29, 28, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(171, 29, 33, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(172, 29, 26, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(173, 29, 21, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(174, 29, 37, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(175, 29, 35, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(176, 29, 8, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(177, 29, 7, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(178, 29, 31, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(179, 29, 38, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(180, 29, 20, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(181, 29, 30, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(182, 29, 25, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(183, 29, 36, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(184, 29, 29, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(185, 29, 32, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(186, 29, 39, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(187, 29, 40, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(188, 29, 27, '2025-10-24 19:00:56', '2025-10-24 19:00:56'),
(189, 30, 28, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(190, 30, 8, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(191, 30, 39, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(192, 31, 28, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(193, 31, 8, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(194, 31, 39, '2025-10-24 23:42:26', '2025-10-24 23:42:26'),
(195, 32, 28, '2025-10-24 23:57:44', '2025-10-24 23:57:44'),
(196, 32, 30, '2025-10-24 23:57:44', '2025-10-24 23:57:44'),
(197, 33, 34, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(198, 33, 28, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(199, 33, 33, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(200, 33, 26, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(201, 33, 21, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(202, 33, 37, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(203, 33, 35, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(204, 33, 8, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(205, 33, 7, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(206, 33, 31, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(207, 33, 38, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(208, 33, 20, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(209, 33, 30, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(210, 33, 25, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(211, 33, 36, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(212, 33, 29, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(213, 33, 32, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(214, 33, 39, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(215, 33, 40, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(216, 33, 27, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(217, 34, 34, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(218, 34, 28, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(219, 34, 33, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(220, 34, 26, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(221, 34, 21, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(222, 34, 37, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(223, 34, 35, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(224, 34, 8, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(225, 34, 7, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(226, 34, 31, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(227, 34, 38, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(228, 34, 20, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(229, 34, 30, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(230, 34, 25, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(231, 34, 36, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(232, 34, 29, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(233, 34, 32, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(234, 34, 39, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(235, 34, 40, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(236, 34, 27, '2025-10-25 00:15:05', '2025-10-25 00:15:05'),
(237, 35, 34, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(238, 35, 28, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(239, 35, 33, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(240, 35, 26, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(241, 35, 21, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(242, 35, 37, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(243, 35, 35, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(244, 35, 8, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(245, 35, 7, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(246, 35, 31, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(247, 35, 38, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(248, 35, 20, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(249, 35, 30, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(250, 35, 25, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(251, 35, 36, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(252, 35, 29, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(253, 35, 32, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(254, 35, 39, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(255, 35, 40, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(256, 35, 27, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(257, 36, 34, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(258, 36, 28, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(259, 36, 33, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(260, 36, 26, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(261, 36, 21, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(262, 36, 37, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(263, 36, 35, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(264, 36, 8, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(265, 36, 7, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(266, 36, 31, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(267, 36, 38, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(268, 36, 20, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(269, 36, 30, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(270, 36, 25, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(271, 36, 36, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(272, 36, 29, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(273, 36, 32, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(274, 36, 39, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(275, 36, 40, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(276, 36, 27, '2025-10-25 00:21:52', '2025-10-25 00:21:52'),
(277, 37, 34, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(278, 37, 28, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(279, 37, 33, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(280, 37, 26, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(281, 37, 21, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(282, 37, 37, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(283, 37, 35, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(284, 37, 8, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(285, 37, 7, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(286, 37, 31, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(287, 37, 38, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(288, 37, 20, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(289, 37, 30, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(290, 37, 25, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(291, 37, 36, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(292, 37, 29, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(293, 37, 32, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(294, 37, 39, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(295, 37, 40, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(296, 37, 27, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(297, 38, 34, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(298, 38, 28, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(299, 38, 33, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(300, 38, 26, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(301, 38, 21, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(302, 38, 37, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(303, 38, 35, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(304, 38, 8, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(305, 38, 7, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(306, 38, 31, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(307, 38, 38, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(308, 38, 20, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(309, 38, 30, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(310, 38, 25, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(311, 38, 36, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(312, 38, 29, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(313, 38, 32, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(314, 38, 39, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(315, 38, 40, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(316, 38, 27, '2025-10-25 00:33:35', '2025-10-25 00:33:35'),
(317, 39, 34, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(318, 39, 28, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(319, 39, 33, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(320, 39, 26, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(321, 39, 21, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(322, 39, 37, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(323, 39, 35, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(324, 39, 8, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(325, 39, 7, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(326, 39, 31, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(327, 39, 38, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(328, 39, 20, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(329, 39, 30, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(330, 39, 25, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(331, 39, 36, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(332, 39, 29, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(333, 39, 32, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(334, 39, 39, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(335, 39, 40, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(336, 39, 27, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(337, 40, 34, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(338, 40, 28, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(339, 40, 33, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(340, 40, 26, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(341, 40, 21, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(342, 40, 37, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(343, 40, 35, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(344, 40, 8, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(345, 40, 7, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(346, 40, 31, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(347, 40, 38, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(348, 40, 20, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(349, 40, 30, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(350, 40, 25, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(351, 40, 36, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(352, 40, 29, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(353, 40, 32, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(354, 40, 39, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(355, 40, 40, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(356, 40, 27, '2025-10-25 00:58:14', '2025-10-25 00:58:14'),
(357, 41, 28, '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(358, 41, 30, '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(359, 42, 28, '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(360, 42, 30, '2025-10-25 01:39:15', '2025-10-25 01:39:15'),
(361, 43, 34, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(362, 43, 28, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(363, 43, 33, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(364, 43, 26, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(365, 43, 21, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(366, 43, 37, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(367, 43, 35, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(368, 43, 8, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(369, 43, 7, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(370, 43, 31, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(371, 43, 38, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(372, 43, 20, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(373, 43, 30, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(374, 43, 25, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(375, 43, 36, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(376, 43, 29, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(377, 43, 32, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(378, 43, 39, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(379, 43, 40, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(380, 43, 27, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(381, 44, 34, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(382, 44, 28, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(383, 44, 33, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(384, 44, 26, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(385, 44, 21, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(386, 44, 37, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(387, 44, 35, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(388, 44, 8, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(389, 44, 7, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(390, 44, 31, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(391, 44, 38, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(392, 44, 20, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(393, 44, 30, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(394, 44, 25, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(395, 44, 36, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(396, 44, 29, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(397, 44, 32, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(398, 44, 39, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(399, 44, 40, '2025-10-25 01:50:27', '2025-10-25 01:50:27'),
(400, 44, 27, '2025-10-25 01:50:27', '2025-10-25 01:50:27');

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
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_lider_semi` bigint(20) UNSIGNED NOT NULL,
  `id_semillero` bigint(20) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
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

<<<<<<< HEAD
INSERT INTO `lideres_semillero` (`id_usuario`, `nombres`, `apellidos`, `tipo_documento`, `documento`, `correo_institucional`, `creado_en`, `actualizado_en`) VALUES
(25, 'Jose', 'mogoñon', 'CE', '123456789', 'correo@hotmail.com', '2025-10-16 15:01:45', '2025-10-16 15:01:45'),
(32, 'a', 'a', 'CC', '1', 'a@pol.es', '2025-10-16 16:44:54', '2025-10-16 16:44:54'),
(38, 'l', 'l', 'CC', '34', 'lider1@gmail.com', '2025-10-16 21:45:51', '2025-10-16 21:45:51'),
(42, 'L', 'Lopez', 'CC', '55', 'l@pol.es', '2025-10-17 15:41:16', '2025-10-17 15:41:16');
=======
INSERT INTO `lideres_semillero` (`id_lider_semi`, `id_semillero`, `nombres`, `apellidos`, `tipo_documento`, `documento`, `correo_institucional`, `creado_en`, `actualizado_en`, `id_tipo_documento`) VALUES
(5, NULL, 'Joaquin cañon', NULL, 'CC', '1012443507', 'test1@gmail.com', '2025-10-15 20:18:13', '2025-10-15 20:18:13', NULL),
(45, NULL, 'Andrés', 'garcia', 'CC', '4652566571', 'hola@hola.com', '2025-10-22 21:30:06', '2025-10-22 21:30:06', NULL);
>>>>>>> origin/main

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lider_general`
--

CREATE TABLE `lider_general` (
  `id_lidergen` bigint(20) UNSIGNED NOT NULL,
  `id_semillero` bigint(20) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Correo_institucional` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lider_general`
--

INSERT INTO `lider_general` (`id_lidergen`, `id_semillero`, `nombres`, `apellidos`, `creado_en`, `actualizado_en`, `Correo_institucional`) VALUES
(19, NULL, 'hansbleidi', NULL, '2025-10-20 18:47:33', '2025-10-20 18:47:33', 'yurani@gmail.com'),
(44, NULL, 'maria', 'garcia', '2025-10-22 21:29:41', '2025-10-22 21:29:41', 'admin@hola.com');

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
<<<<<<< HEAD
(5, '2025_10_17_163819_create_archivos_table', 3);
=======
(5, '2025_10_15_152742_create_lideres_generales_table', 2),
(8, '2025_10_15_160035_add_nombre_to_admins_and_lideres_generales', 3),
(9, '2025_10_15_191526_update_id_tipo_documento_in_aprendices_and_lideres_semillero', 3),
(10, '2025_10_20_163848_create_semilleros_table', 4),
(11, '2025_10_21_000000_create_aprendiz_proyectos_table', 5),
(12, '2025_10_21_151521_add_apellidos_to_users_table', 6),
(13, '2025_10_22_193932_add_nombre_completo_to_aprendices_table', 7),
(15, '2025_10_23_083700_create_eventos_table', 8),
(16, '2025_10_22_202536_add_estado_to_documentos_table', 9),
(17, '2025_10_23_140834_add_ubicacion_recordatorio_to_eventos_table', 10),
(18, '2025_10_23_145619_add_link_virtual_descripcion_to_eventos_table', 11),
(19, '2025_10_24_163144_add_codigo_reunion_to_eventos_table', 12);
>>>>>>> origin/main

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
(1, 1, 1, 'IA para Agricultura', 'Proyecto de optimización de cultivos mediante IA', 'EN_EJECUCION', '2025-01-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(2, 2, 1, 'App de Aprendizaje SENA', 'Aplicación para gestión de aprendizajes', 'EN_FORMULACION', '2025-02-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(3, 3, 1, 'Sistema de Energía Solar', 'Investigación en paneles solares', 'FINALIZADO', '2024-09-01', '2025-02-01', '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(4, 4, 1, 'Monitor de Ciberseguridad', 'Monitoreo de redes locales', 'EN_EJECUCION', '2025-03-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22'),
(5, 5, 1, 'Robot Educativo', 'Robot con sensores para enseñanza STEAM', 'EN_FORMULACION', '2025-04-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22');

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
<<<<<<< HEAD
('HCijWpF0NBP0XiuoTZD8cS6U6ADcKPFRLpu5XFtp', 33, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUdMVWdOMWFmd3NCQnc4QlFSSDMwMUdxZ2Uwdkx5bHE4bE9QMkNrZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMzt9', 1760968614),
('zKVnJzlZ9dRUIf5iEeCgOQZbZQ85HAGfu0LDLJVj', 33, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNkNJb2tWTER3dHREdWh6c2F1M2pya05qU2dVdE8yMlRKM29RS2ZqQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMzt9', 1760737659);
=======
('8i0IQFBBpZPRz88f1q5P0qISeEUSun0qvk4gLGqX', 5, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVENPUHRreHVRY29aRW5rQ2pmenY5RkJhdmEwOVllaWg2RGlJY3FQOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xpZGVyX3NlbWkvY2FsZW5kYXJpbyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbGlkZXJfc2VtaS9ldmVudG9zP2FuaW89MjAyNSZtZXM9MTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1O30=', 1761339108),
('s7dT3q4x1e5bN8c6fBGsPLKljggXPuCmkz1pWDXE', 5, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTUNRdzlkeXJJZjFBcWpiQnBkdTlvejhYSXZXS2ZoQjdTSlhYRWhWayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9saWRlcl9zZW1pL2V2ZW50b3M/YW5pbz0yMDI1Jm1lcz0xMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==', 1761336684);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `name`, `apellidos`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
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
(43, 'Sergio', 'nova', 'hola1@hola.com', NULL, '$2y$12$X6be5Oyv8GOMcJ6XeiR2E.kexPv76F04xDiid9KsY3/u9lW5AHP82', 'ADMIN', NULL, '2025-10-22 21:29:19', '2025-10-22 21:29:19'),
(44, 'maria', 'garcia', 'admin@hola.com', NULL, '$2y$12$B.2OtsvxJicVTNJlOgqawut3RJ8M2sFEAi/0CFfiIJMoYOWftVLIK', 'LIDER GENERAL', NULL, '2025-10-22 21:29:41', '2025-10-22 21:29:41'),
(45, 'Andrés', 'garcia', 'hola@hola.com', NULL, '$2y$12$gDClHnaBJeyAT.p1n6mlR.r.0LBbtL/ejEhvY26v23OcnKORnEWAi', 'LIDER_SEMILLERO', NULL, '2025-10-22 21:30:06', '2025-10-22 21:30:06'),
(46, 'Sergio', 'Morita', 'asd2223@hola.com', NULL, '$2y$12$MBRwPyhaJOUhkX3g1sprD.6X3OZaVMzlM04O8PfHMfoXMuhuHnMme', 'APRENDIZ', NULL, '2025-10-22 21:48:14', '2025-10-22 21:48:14');
>>>>>>> origin/main

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
  ADD PRIMARY KEY (`id_aprendiz`),
  ADD UNIQUE KEY `uk_aprendiz_user` (`user_id`),
  ADD KEY `idx_documento` (`documento`),
  ADD KEY `idx_ficha` (`ficha`),
  ADD KEY `idx_correo_institucional` (`correo_institucional`);

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
  ADD KEY `fk_documento_proyecto` (`id_proyecto`),
  ADD KEY `fk_documento_aprendiz` (`id_aprendiz`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `eventos_id_lider_foreign` (`id_lider`),
  ADD KEY `eventos_codigo_reunion_index` (`codigo_reunion`);

--
-- Indices de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_participantes_id_evento_id_aprendiz_unique` (`id_evento`,`id_aprendiz`),
  ADD KEY `evento_participantes_id_aprendiz_foreign` (`id_aprendiz`);

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
-- Indices de la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD PRIMARY KEY (`id_lider_semi`),
  ADD KEY `fk_lideres_semillero_semillero` (`id_semillero`);

--
-- Indices de la tabla `lider_general`
--
ALTER TABLE `lider_general`
  ADD PRIMARY KEY (`id_lidergen`),
  ADD KEY `fk_lider_general_semillero` (`id_semillero`);

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
  ADD PRIMARY KEY (`id_semillero`);

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
<<<<<<< HEAD
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
=======
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id_aprendiz` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
>>>>>>> origin/main

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

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
<<<<<<< HEAD
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
=======
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
>>>>>>> origin/main

--
-- AUTO_INCREMENT de la tabla `proyecto_user`
--
<<<<<<< HEAD
ALTER TABLE `proyecto_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
=======
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
>>>>>>> origin/main

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
<<<<<<< HEAD
  ADD CONSTRAINT `aprendices_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Filtros para la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD CONSTRAINT `archivos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
=======
  ADD CONSTRAINT `fk_aprendices_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_documentos_aprendices` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_aprendiz`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_documentos_proyectos` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_id_lider_foreign` FOREIGN KEY (`id_lider`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD CONSTRAINT `evento_participantes_id_aprendiz_foreign` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_aprendiz`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_participantes_id_evento_foreign` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE;
>>>>>>> origin/main

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
<<<<<<< HEAD
  ADD CONSTRAINT `lideres_semillero_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
=======
  ADD CONSTRAINT `fk_lideres_semillero_semillero` FOREIGN KEY (`id_semillero`) REFERENCES `semilleros` (`id_semillero`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lideres_semillero_user` FOREIGN KEY (`id_lider_semi`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
>>>>>>> origin/main

--
-- Filtros para la tabla `lider_general`
--
ALTER TABLE `lider_general`
<<<<<<< HEAD
  ADD CONSTRAINT `lider_general_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyecto_user`
--
ALTER TABLE `proyecto_user`
  ADD CONSTRAINT `proyecto_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
=======
  ADD CONSTRAINT `fk_lider_general_semillero` FOREIGN KEY (`id_semillero`) REFERENCES `semilleros` (`id_semillero`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lider_general_user` FOREIGN KEY (`id_lidergen`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
>>>>>>> origin/main
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
