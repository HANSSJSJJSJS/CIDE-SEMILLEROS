-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2025 a las 17:34:12
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
  `apellidos` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_usuario`, `nombres`, `apellidos`, `creado_en`, `actualizado_en`) VALUES
(43, 'Sergio', 'nova', '2025-10-22 21:29:19', '2025-10-22 21:29:19'),
(47, 'maria', 'nova', '2025-10-22 23:53:38', '2025-10-22 23:53:38'),
(49, 'LOREE', 'nova', '2025-10-23 02:40:11', '2025-10-23 02:40:11'),
(69, 'luis', 'garcia', '2025-10-30 00:29:35', '2025-10-30 00:29:35');

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
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aprendices`
--

INSERT INTO `aprendices` (`id_aprendiz`, `user_id`, `nombres`, `apellidos`, `nombre_completo`, `ficha`, `programa`, `tipo_documento`, `documento`, `celular`, `correo_institucional`, `correo_personal`, `contacto_nombre`, `contacto_celular`, `creado_en`, `actualizado_en`, `estado`) VALUES
(7, NULL, 'Joaquin cañon', NULL, 'Joaquin cañon ', '35435345', 'adso', 'T.I', '8344873278', '3215678976', 'test2@gmail.com', 'example@gmail.com', 'gsdfd', '3456789642', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(8, NULL, 'Hansbleidi Cardenas', NULL, 'Hansbleidi Cardenas ', '2848527', 'ADSO', 'CC', '1071548288', '3053970242', 'hans@soy.sena.edu.co', 'test3@gmail.com', NULL, NULL, '2025-10-15 20:50:17', '2025-11-05 20:02:55', 'Activo'),
(20, NULL, 'Laura Martínez', NULL, 'Laura Martínez ', '38454893', 'Animacion 3D', 'C.E', '8237498234', '3470909094', 'laura@sena.edu.co', 'IKJEDWN@HSBXJ.COM', 'djnbcuibn', '3129098765', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(21, NULL, 'Carlos Pérez', NULL, 'Carlos Pérez ', '77788888', 'Adso', 'C.C', '76876876', '3700907888', 'carlos@sena.edu.co', 'hfbejhr@fdnjdn.com', 'ebfciewuab', '7263t43', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(25, NULL, 'Laura Rodríguez', NULL, 'Laura Rodríguez ', '7247747', 'Animacion 3D', 'C.C', '72364264', '32145567888', 'laura.rod@example.com', 'vdvcb@gjgjgj.com', 'uhbvusdb', '3457890078', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(26, NULL, 'Carlos Gómez', NULL, 'Carlos Gómez ', '35435345', 'Adso', 'T.I', '7373363663', '3456789009', 'carlos.gomez@example.com', 'gsggs@ifiuewf.com', 'hahhah', '3213456789', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(27, NULL, 'Valentina Ruiz', NULL, 'Valentina Ruiz ', '73478364', 'Animacion 3D', 'C.C', '7468584863', '3215678976', 'valentina.ruiz@example.com', 'example@gnmail.com', 'hahsabahs', '3467674356', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(28, NULL, 'Andrés Pérez', NULL, 'Andrés Pérez ', '62347826', 'ADSO', 'C.C', '847276487', '3456789023', 'andres.perez@example.com', 'dvasvfjh@dbd.com', '3216789045', '6472474127', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(29, NULL, 'María Castro', NULL, 'María Castro ', '35435345', 'animacion 3D', 'C.C', '7689367363', '3214789076', 'maria.castro@example.com', 'example123@gmail.com', 'hasahgvbwi', '3456789012', '2025-10-21 15:46:36', '2025-11-05 20:02:55', 'Activo'),
(30, NULL, 'Laura Martínez Pérez', NULL, 'Laura Martínez Pérez ', '2435345', 'Tecnología en Análisis y Desarrollo de Sistemas de Información', 'CC', '1001234567', '3004567890', 'laura.martinez@sena.edu.co', 'laura.perez@gmail.com', 'Carlos Pérez', '3102345678', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(31, NULL, 'Juan David Torres', NULL, 'Juan David Torres ', '2435346', 'Gestión Empresarial', 'TI', '1002234568', '3014567891', 'juan.torres@sena.edu.co', 'jdavidtorres@yahoo.com', 'Martha Torres', '3103345679', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(32, NULL, 'Mariana Gómez Ríos', NULL, 'Mariana Gómez Ríos ', '2435347', 'Diseño Gráfico Digital', 'CC', '1003234569', '3024567892', 'mariana.gomez@sena.edu.co', 'marianagomez@hotmail.com', 'Luis Ríos', '3104345680', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(33, NULL, 'Carlos Andrés Suárez', NULL, 'Carlos Andrés Suárez ', '2435348', 'Contabilidad y Finanzas', 'CE', '1004234570', '3034567893', 'carlos.suarez@sena.edu.co', 'carsuarez@gmail.com', 'Sandra Suárez', '3105345681', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(34, NULL, 'Ana Milena López', NULL, 'Ana Milena López ', '2435349', 'Gestión del Talento Humano', 'CC', '1005234571', '3044567894', 'ana.lopez@sena.edu.co', 'anamilena@gmail.com', 'Diego López', '3106345682', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(35, NULL, 'Esteban Ramírez Torres', NULL, 'Esteban Ramírez Torres ', '2435350', 'Producción Multimedia', 'TI', '1006234572', '3054567895', 'esteban.ramirez@sena.edu.co', 'eramirez@hotmail.com', 'Natalia Torres', '3107345683', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(36, NULL, 'Luisa Fernanda Salazar', NULL, 'Luisa Fernanda Salazar ', '2435351', 'Seguridad y Salud en el Trabajo', 'CC', '1007234573', '3064567896', 'luisa.salazar@sena.edu.co', 'lusalazar@gmail.com', 'Camilo Salazar', '3108345684', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(37, NULL, 'David Alejandro Méndez', NULL, 'David Alejandro Méndez ', '2435352', 'Mecatrónica', 'CE', '1008234574', '3074567897', 'david.mendez@sena.edu.co', 'damendez@gmail.com', 'Paola Méndez', '3109345685', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(38, NULL, 'Juliana Herrera Osorio', NULL, 'Juliana Herrera Osorio ', '2435353', 'Marketing Digital', 'CC', '1009234575', '3084567898', 'juliana.herrera@sena.edu.co', 'jherrera@gmail.com', 'Hernán Herrera', '3110345686', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(39, NULL, 'Santiago López Vargas', NULL, 'Santiago López Vargas ', '2435354', 'Desarrollo de Videojuegos', 'TI', '1010234576', '3094567899', 'santiago.lopez@sena.edu.co', 'slopezvargas@gmail.com', 'Andrea Vargas', '3111345687', '2025-10-21 19:41:11', '2025-11-05 20:02:55', 'Activo'),
(40, 46, 'Sergio', 'Morita', 'Sergio Morita', '28485274', 'Adsoa', 'TI', '15457845', '54648961', 'asjo@hotmail.com', 'asd2223@hola.com', 'sias', NULL, '2025-10-22 21:48:14', '2025-11-05 20:02:55', 'Activo'),
(41, 68, 'deivit', 'Agudelo', 'deivit Agudelo', '28485274', 'Adso', 'CC', '15457845', '54648961', 'lorem@hsena.com', 'joaquin_canon@soy.sena.edu.co.com', NULL, NULL, '2025-10-29 23:41:00', '2025-11-05 20:02:55', 'Activo');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_lider_semi` bigint(20) UNSIGNED DEFAULT NULL,
  `id_admin` bigint(20) UNSIGNED DEFAULT NULL,
  `id_proyecto` bigint(20) UNSIGNED DEFAULT NULL,
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

--
-- Volcado de datos para la tabla `evento_participantes`
--

INSERT INTO `evento_participantes` (`id`, `id_evento`, `id_aprendiz`, `id_lider_semi`, `created_at`, `updated_at`) VALUES
(1, 2, 34, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(2, 2, 28, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(3, 2, 33, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(4, 2, 26, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(5, 2, 21, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(6, 2, 37, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(7, 2, 41, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(8, 2, 35, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(9, 2, 8, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(10, 2, 7, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(11, 2, 31, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(12, 2, 38, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(13, 2, 20, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(14, 2, 30, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(15, 2, 25, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(16, 2, 36, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(17, 2, 29, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(18, 2, 32, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(19, 2, 39, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(20, 2, 40, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(21, 2, 27, NULL, '2025-11-06 01:59:54', '2025-11-06 01:59:54'),
(22, 3, 34, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(23, 3, 28, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(24, 3, 33, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(25, 3, 26, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(26, 3, 21, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(27, 3, 37, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(28, 3, 41, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(29, 3, 35, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(30, 3, 8, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(31, 3, 7, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(32, 3, 31, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(33, 3, 38, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(34, 3, 20, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(35, 3, 30, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(36, 3, 25, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(37, 3, 36, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(38, 3, 29, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(39, 3, 32, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(40, 3, 39, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(41, 3, 40, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(42, 3, 27, NULL, '2025-11-06 02:02:18', '2025-11-06 02:02:18'),
(43, 4, 34, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(44, 4, 28, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(45, 4, 33, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(46, 4, 26, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(47, 4, 21, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(48, 4, 37, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(49, 4, 41, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(50, 4, 35, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(51, 4, 8, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(52, 4, 7, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(53, 4, 31, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(54, 4, 38, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(55, 4, 20, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(56, 4, 30, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(57, 4, 25, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(58, 4, 36, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(59, 4, 29, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(60, 4, 32, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(61, 4, 39, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(62, 4, 40, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40'),
(63, 4, 27, NULL, '2025-11-06 02:02:40', '2025-11-06 02:02:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencias`
--

CREATE TABLE `evidencias` (
  `id_evidencia` bigint(20) UNSIGNED NOT NULL,
  `proyecto_id` bigint(20) UNSIGNED NOT NULL,
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
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_lider_semi` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `tipo_documento` varchar(5) DEFAULT NULL,
  `documento` varchar(40) NOT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lideres_semillero`
--

INSERT INTO `lideres_semillero` (`id_lider_semi`, `nombres`, `apellidos`, `tipo_documento`, `documento`, `correo_institucional`, `creado_en`, `actualizado_en`) VALUES
(57, 'Carlos Hernando', 'Niño Rivera', 'CC', '000000001', 'chninor@sena.edu.co', '2025-10-28 01:55:05', '2025-10-28 01:55:05'),
(58, 'Karol Vanesa', 'Hernández', 'CC', '000000002', 'khernandez@sena.edu.co', '2025-10-28 01:56:09', '2025-10-28 01:56:09'),
(59, 'William Rolando', 'Rodríguez', 'CC', '000000003', 'wrodriguezr@sena.edu.co', '2025-10-28 01:56:59', '2025-10-28 01:56:59'),
(60, 'Cesar', 'Moreno', 'CC', '000000004', 'cmorenogu@sena.edu.co', '2025-10-28 02:03:45', '2025-10-28 02:03:45'),
(61, 'Marly Julieth', 'Hernández Sánchez', 'CC', '000000005', 'marhermandezs@sena.edu.co', '2025-10-28 02:04:42', '2025-10-28 02:04:42'),
(63, 'Lina Angélica', 'Ubaque', 'CC', '000000007', 'lubaqueb@sena.edu.co', '2025-10-28 02:06:03', '2025-10-28 02:06:03'),
(64, 'Diana Marcela', 'Acosta Torres', 'CC', '000000008', 'dacostat@sena.edu.co', '2025-10-28 02:06:41', '2025-10-28 02:06:41'),
(65, 'Arlix Carolina', 'Aragón', 'CC', '000000010', 'aaragonc@sena.edu.co', '2025-10-28 02:07:18', '2025-10-28 02:07:18'),
(66, 'Carlos Andrés', 'Sáenz', 'CC', '000000012', 'casaenz@sena.edu.co', '2025-10-28 23:41:56', '2025-10-28 23:41:56'),
(67, 'Harol', 'Pardos', 'CC', '1544547', 'sergio@admin1.com', '2025-10-29 00:24:23', '2025-10-29 19:47:29'),
(70, 'Hansbleidi', 'Cardenas', 'CC', '1071548288', 'yurani12@gmail.com', '2025-11-05 18:21:44', '2025-11-05 18:21:44');

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
(10, '2025_10_20_163848_create_semilleros_table', 4),
(11, '2025_10_21_000000_create_aprendiz_proyectos_table', 5),
(12, '2025_10_21_151521_add_apellidos_to_users_table', 6),
(13, '2025_10_17_163819_create_archivos_table', 7),
(14, '2025_10_17_200240_create_proyecto_user_table', 8),
(15, '2025_10_20_172439_add_estado_to_users_table', 9),
(16, '2025_10_20_create_proyectos_table', 9),
(17, 'create_proyectos_table', 9),
(18, '2025_10_22_193932_add_nombre_completo_to_aprendices_table', 10),
(19, '2025_10_22_202536_add_estado_to_documentos_table', 10),
(20, '2025_10_23_083700_create_eventos_table', 9),
(21, '2025_10_23_140834_add_ubicacion_recordatorio_to_eventos_table', 10),
(22, '2025_10_23_145619_add_link_virtual_descripcion_to_eventos_table', 10),
(23, '2025_10_23_154412_create_evidencias_table', 11),
(24, '2025_10_24_133708_add_estado_to_documentos_table', 12),
(25, '2025_10_24_150148_add_documentos_requeridos_to_proyectos_table', 12),
(26, '2025_10_24_163144_add_codigo_reunion_to_eventos_table', 13),
(27, '2025_10_24_170619_add_estado_to_archivos_table', 13),
(28, '2025_10_24_191739_add_proyecto_id_to_archivos_table', 14),
(29, '2025_10_24_194710_add_proyecto_id_to_archivos_table', 15),
(30, '2025_10_27_200800_add_id_usuario_to_evidencias_table', 16),
(31, '2025_10_28_000100_create_evento_participantes_table', 16),
(32, '2025_10_28_000200_add_unique_index_eventos_datetime', 17),
(33, '2025_10_28_150500_add_optional_fields_to_eventos_table', 17),
(34, '2025_10_28_154300_make_linea_investigacion_nullable_in_eventos_table', 17),
(35, '2025_10_28_154500_alter_linea_investigacion_nullable_mysql', 17),
(36, '2025_11_05_000200_fix_evento_participantes_id_auto_increment', 17);

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
(5, 5, 'Robot Educativo', 'Robot con sensores para enseñanza STEAM', 3, 'EN_FORMULACION', '2025-04-01', NULL, '2025-10-20 20:02:22', '2025-10-20 20:02:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `categoria` enum('plantillas','manuales','otros') NOT NULL DEFAULT 'otros',
  `descripcion` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id`, `nombre_archivo`, `archivo`, `categoria`, `descripcion`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Plantilla rx56', 'recursos/0uC1zAChhaK5Xfk8m91zBs8xylzmABiIKf0Mmjuq.pdf', 'manuales', 'Plantilla rx56', 4, '2025-11-10 20:57:39', '2025-11-10 20:57:39');

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
(9, 'Grupo de Estudio de Desarrollo de Software (GEDS)', 'Telecomunicaciones y Tecnologías Virtuales', 60, '2025-10-27 18:08:12', '2025-10-28 02:08:33'),
(10, 'Investigación de Mercados para las Mipymes (INVERPYMES)', 'Comercio y Servicios para el Desarrollo Empresarial', 61, '2025-10-27 18:08:12', '2025-10-28 02:09:20'),
(11, 'Materiales, Procesos de Manufactura y Automatización (MAPRA)', 'Diseño, Ingeniería y Mecatrónica', 66, '2025-10-27 18:08:12', '2025-10-28 23:42:15'),
(12, 'Micronanotec', 'Integración de tecnologías convergentes para el mejoramiento de la calidad de vida', 63, '2025-10-27 18:08:12', '2025-10-28 02:10:07'),
(13, 'Desarrollo de Videojuegos Serios', 'Telecomunicaciones y Tecnologías Virtuales', 64, '2025-10-27 18:08:12', '2025-10-28 02:07:55'),
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
('8xnehNTihwvL8wH2pr2dD3PEd31miTmYajQMRO1m', 70, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib05tUmZBQ2RuQk5qZHk3a29QTkZVRmVKY2FqTkdZRFlQYjlaOW9mbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9saWRlcl9zZW1pL2NhbGVuZGFyaW8iO3M6NToicm91dGUiO3M6MjE6ImxpZGVyX3NlbWkuY2FsZW5kYXJpbyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjcwO30=', 1762787021),
('nTVAlZgSAtwExl7cX3d4SxJuJ7IVbGByOlfCbGy4', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidTBBVmpvVGh4UXNOTlpnQXZKSkNoS2JRS0xKaFlVMEY5RXFEbE94eCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3JlY3Vyc29zIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQvc3RhdHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1762792424);

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_lc` varchar(255) GENERATED ALWAYS AS (lcase(`email`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `apellidos`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(4, 'Joaquin cañon', NULL, 'test@gmail.com', NULL, '$2y$12$ESFF/wMQPumWmeMHt1/Ij.sOpCKgD1xnximeFE4zvCwctCLudRpt.', 'ADMIN', NULL, '2025-10-16 01:10:41', '2025-10-16 01:10:41'),
(8, 'Joaquin cañon', NULL, 'test3@gmail.com', NULL, '$2y$12$CysY7mh6WuCxIc.j4vORxuqAEPzjDJr0lxxqSo.Q.8B0Q9caCicLW', 'APRENDIZ', NULL, '2025-10-16 01:50:17', '2025-10-16 01:50:17'),
(9, 'hansita', NULL, 'hanscard@20gmail.com', NULL, '$2y$12$BKJsJ8LlRHORZj/c4gIBqeU1u9Zt3lPlAiyOFjX23Ac084uYZpXR.', 'ADMIN', NULL, '2025-10-16 19:17:57', '2025-10-16 19:17:57'),
(20, 'Laura Martínez', NULL, 'laura@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(21, 'Carlos Pérez', NULL, 'carlos@sena.edu.co', NULL, '$2y$12$abcdefghijklmnopqrstuv', 'APRENDIZ', NULL, '2025-10-20 20:01:37', '2025-10-20 20:01:37'),
(26, 'Carlos Gómez', NULL, 'carlos.gomez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash2', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(27, 'Valentina Ruiz', NULL, 'valentina.ruiz@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash3', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(28, 'Andrés Pérez', NULL, 'andres.perez@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash4', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-21 15:08:35'),
(29, 'María', 'Morita', 'maria.castro@example.com', '2025-10-21 15:08:35', '$2y$10$examplehash5', 'APRENDIZ', NULL, '2025-10-21 15:08:35', '2025-10-22 23:46:58'),
(43, 'Sergio', 'nova', 'hola1@hola.com', NULL, '$2y$12$X6be5Oyv8GOMcJ6XeiR2E.kexPv76F04xDiid9KsY3/u9lW5AHP82', 'ADMIN', NULL, '2025-10-22 21:29:19', '2025-10-22 21:29:19'),
(45, 'Andrés', 'garcia', 'hola@hola.com', NULL, '$2y$12$gDClHnaBJeyAT.p1n6mlR.r.0LBbtL/ejEhvY26v23OcnKORnEWAi', 'LIDER_SEMILLERO', NULL, '2025-10-22 21:30:06', '2025-10-22 21:30:06'),
(46, 'Sergio', 'Morita', 'asd2223@hola.com', NULL, '$2y$12$MBRwPyhaJOUhkX3g1sprD.6X3OZaVMzlM04O8PfHMfoXMuhuHnMme', 'APRENDIZ', NULL, '2025-10-22 21:48:14', '2025-10-22 23:46:41'),
(47, 'maria', 'nova', 'danielcf97@hotmail.com', NULL, '$2y$12$gIcmOXdncQNHiCYKuHoUKufcEKdTgJjE4xjAAU4BQBp7JHGi1.gRO', 'ADMIN', NULL, '2025-10-22 23:53:38', '2025-10-22 23:53:38'),
(48, 'Sergio', 'nova', 'daniel7@hotmail.com', NULL, '$2y$12$3j3LhORhr9ggHeZLCNDNvePrJvy.CGFEOlwM6whleyKac8XsKKqBm', 'ADMIN', NULL, '2025-10-23 02:39:02', '2025-10-23 02:39:02'),
(49, 'LOREE', 'nova', 'lui1@hotmail.com', NULL, '$2y$12$vkzG43/t8XiXhxA6KIIhIem3Rc9VTzevb5beLUN95vX6d4rzrUdiu', 'ADMIN', NULL, '2025-10-23 02:40:11', '2025-10-23 02:40:11'),
(50, 'ñorcus', 'pardo', 'norcus@hola.com', NULL, '$2y$12$ZOJVqJKIm9TgY19QJsEMuuYaug1iL6hloDYUYScCi3l/vDKIQtSuG', 'ADMIN', NULL, '2025-10-24 00:29:42', '2025-10-24 00:29:42'),
(51, 'LOREEna', 'mogoyon', 'loreadmin@hola.com', NULL, '$2y$12$gV9quZG5HruIW0FcKDUqcO.KMB2jr/phmGjNgBaSdYuQIlQ5vp8Oi', 'ADMIN', NULL, '2025-10-24 00:30:08', '2025-10-24 00:30:08'),
(54, 'maria', 'Morita', 'hoaprela@hola.com', NULL, '$2y$12$D6qRyFHBWW5FwmZEDI.6sel6F8tFEY0dkcmYhdk9nqYl9VgWTAU4e', 'APRENDIZ', NULL, '2025-10-24 00:31:38', '2025-10-24 00:31:38'),
(55, 'Test User', NULL, 'test@example.com', '2025-10-27 21:52:19', '$2y$12$MWjLtw9w/Araa.FitOcv.OyfUFlBVXvQeipNfNkPQaEzKYfgzJnnu', 'APRENDIZ', 'UxHPINOx1R', '2025-10-27 21:52:20', '2025-10-27 21:52:20'),
(56, 'Sergio', 'Morita', 'sergio@sena.com', NULL, '$2y$12$IZH2smkp0bK.KMJN1hVgxOWHruIuyfshhW8wIMELGbl1twC9klCsK', 'LIDER_SEMILLERO', NULL, '2025-10-28 01:35:08', '2025-10-28 01:35:08'),
(57, 'Carlos Hernando', 'Niño Rivera', 'chninor@sena.edu.co', NULL, '$2y$12$2u3HSnrhTaf1Szhm/Zszt.9Rx0NxPeqD71Brg2RV8fmhz0LLDOaYC', 'LIDER_SEMILLERO', NULL, '2025-10-28 01:55:05', '2025-10-28 01:55:05'),
(58, 'Karol Vanesa', 'Hernández', 'khernandez@sena.edu.co', NULL, '$2y$12$K0hG1LjlEGqRIPFlXihubu26Y8rGxnrwA07OcxX.xGzYDivps6WoS', 'LIDER_SEMILLERO', NULL, '2025-10-28 01:56:09', '2025-10-28 01:56:09'),
(59, 'William Rolando', 'Rodríguez', 'wrodriguezr@sena.edu.co', NULL, '$2y$12$jFM7Upw4h3TD4DKZySV3d.QT2xwS1sQkQ4WchZoLVPTOhvn1MBPR6', 'LIDER_SEMILLERO', NULL, '2025-10-28 01:56:59', '2025-10-28 01:56:59'),
(60, 'Cesar', 'Moreno', 'cmorenogu@sena.edu.co', NULL, '$2y$12$EWRQGOJoPb3S2lsIcIfpaOja9/fyNjcC8RYwZWSPS69Psv4tFS.m.', 'LIDER_SEMILLERO', NULL, '2025-10-28 02:03:45', '2025-10-28 02:03:45'),
(61, 'Marly Julieth', 'Hernández Sánchez', 'marhermandezs@sena.edu.co', NULL, '$2y$12$3COInLSxSGW0VKA.qyyBVebMxPUOB7vUw2H4COtGodVanGqkPYByy', 'LIDER_SEMILLERO', NULL, '2025-10-28 02:04:42', '2025-10-28 02:04:42'),
(63, 'Lina Angélica', 'Ubaque', 'lubaqueb@sena.edu.co', NULL, '$2y$12$haGM4Gz72sf/v8wBzflhCeaE5yRwCFAACQlR0KH185D3iC1MrGToW', 'LIDER_SEMILLERO', NULL, '2025-10-28 02:06:03', '2025-10-28 02:06:03'),
(64, 'Diana Marcela', 'Acosta Torres', 'dacostat@sena.edu.co', NULL, '$2y$12$FjzqHVGBfWlCq7IEliyo/OmMhWHJq/8DGF3Q88WPXK8EcigrhiB5e', 'LIDER_SEMILLERO', NULL, '2025-10-28 02:06:41', '2025-10-28 02:06:41'),
(65, 'Arlix Carolina', 'Aragón', 'aaragonc@sena.edu.co', NULL, '$2y$12$VftA7JvPDUz2PaH11KMsvOX/mZNQ9rq1QspsFx9s1xngxJl9Ol622', 'LIDER_SEMILLERO', NULL, '2025-10-28 02:07:18', '2025-10-28 02:07:18'),
(66, 'Carlos Andrés', 'Sáenz', 'casaenz@sena.edu.co', NULL, '$2y$12$cMQuRFhEsAMGtz0tjcjIbOSxBNXgUvnecHFuTw1iiT4IsrgTLAOj2', 'LIDER_SEMILLERO', NULL, '2025-10-28 23:41:56', '2025-10-28 23:41:56'),
(67, 'Harol', 'Pardos', 'sergio@admin1.com', NULL, '$2y$12$oqJyGYOnWOqd6WLDIOOaHOXybBykL2OIOGYhs77dUPf3sPo4BhpRq', 'LIDER_SEMILLERO', NULL, '2025-10-29 00:24:23', '2025-10-29 19:47:29'),
(68, 'deivit', 'Agudelo', 'joaquin_canon@soy.sena.edu.co.com', NULL, '$2y$12$0TM.ctVut0gIBuJAA9u94Ord22JMauSbNSg/WayCvtkOx.GqZFOj6', 'APRENDIZ', NULL, '2025-10-29 23:40:59', '2025-10-29 23:40:59'),
(69, 'luis', 'garcia', 'lideradmin@hola.com', NULL, '$2y$12$xgChGVNvHOBXhGjD98dmU.k94pgDEn7akThvCIKVtwhSg7uvy3e0.', 'ADMIN', NULL, '2025-10-30 00:29:35', '2025-10-30 00:29:35'),
(70, 'Hansbleidi', 'Cardenas', 'yurani12@gmail.com', NULL, '$2y$12$IaEEeNepnPujOXZk6c5fQeYMB.38rLT6MDZsuBa.xq6ZfBV3sx.Ka', 'LIDER_SEMILLERO', NULL, '2025-11-05 18:21:44', '2025-11-05 18:21:44');

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
  ADD KEY `idx_documento` (`documento`),
  ADD KEY `idx_ficha` (`ficha`),
  ADD KEY `idx_correo_institucional` (`correo_institucional`);

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
  ADD UNIQUE KEY `eventos_lider_fecha_unique` (`id_lider_semi`,`fecha_hora`),
  ADD KEY `idx_eventos_fecha` (`fecha_hora`),
  ADD KEY `idx_eventos_admin_fecha` (`id_admin`,`fecha_hora`),
  ADD KEY `idx_eventos_lider_fecha` (`id_lider_semi`,`fecha_hora`),
  ADD KEY `eventos_codigo_reunion_index` (`codigo_reunion`);
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
  ADD KEY `evidencias_id_usuario_index` (`id_usuario`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id_aprendiz` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  MODIFY `id_semillero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

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
  ADD CONSTRAINT `fk_eventos_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento_participantes`
--
ALTER TABLE `evento_participantes`
  ADD CONSTRAINT `fk_part_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD CONSTRAINT `evidencias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD CONSTRAINT `fk_lideres_semillero_user` FOREIGN KEY (`id_lider_semi`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `fk_recursos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD CONSTRAINT `fk_semillero_lider` FOREIGN KEY (`id_lider_semi`) REFERENCES `lideres_semillero` (`id_lider_semi`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
