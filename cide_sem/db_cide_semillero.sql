-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2025 a las 16:41:16
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
-- Base de datos: `sena_semilleros`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `ficha` varchar(30) NOT NULL,
  `programa` varchar(160) NOT NULL,
  `id_tipo_documento` tinyint(3) UNSIGNED NOT NULL,
  `documento` varchar(40) NOT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `correo_personal` varchar(160) DEFAULT NULL,
  `contacto_nombre` varchar(160) DEFAULT NULL,
  `contacto_celular` varchar(30) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id_aprendiz` int(10) UNSIGNED NOT NULL,
  `fecha_ingreso` date DEFAULT curdate(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lideres_semillero`
--

CREATE TABLE `lideres_semillero` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `id_tipo_documento` tinyint(3) UNSIGNED NOT NULL,
  `documento` varchar(40) NOT NULL,
  `contacto_nombre` varchar(160) DEFAULT NULL,
  `contacto_celular` varchar(30) DEFAULT NULL,
  `correo_institucional` varchar(160) DEFAULT NULL,
  `correo_personal` varchar(160) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semilleros`
--

CREATE TABLE `semilleros` (
  `id_semillero` int(10) UNSIGNED NOT NULL,
  `nombre_semillero` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_lider_usuario` int(10) UNSIGNED NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documento`
--

CREATE TABLE `tipos_documento` (
  `id_tipo_documento` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_documento`
--

INSERT INTO `tipos_documento` (`id_tipo_documento`, `nombre`) VALUES
(1, 'CC'),
(3, 'CE'),
(5, 'NIT'),
(4, 'PA'),
(2, 'TI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_proyecto`
--

CREATE TABLE `tipos_proyecto` (
  `id_tipo_proyecto` tinyint(3) UNSIGNED NOT NULL,
  `categoria` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_proyecto`
--

INSERT INTO `tipos_proyecto` (`id_tipo_proyecto`, `categoria`) VALUES
(1, 'Sin categoría');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `correo` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ADMINISTRADOR','LIDER_GENERAL','LIDER_SEMILLERO','APRENDIZ') NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_aprendiz_tipo_doc` (`id_tipo_documento`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documento_proyecto` (`id_proyecto`);

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
  ADD UNIQUE KEY `uq_grupo_aprendiz` (`id_grupo`,`id_aprendiz`),
  ADD KEY `fk_ga_aprendiz_perfil` (`id_aprendiz`);

--
-- Indices de la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_lider_tipo_doc` (`id_tipo_documento`);

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
  ADD PRIMARY KEY (`id_semillero`),
  ADD UNIQUE KEY `nombre_semillero` (`nombre_semillero`),
  ADD KEY `fk_semillero_liderperfil` (`id_lider_usuario`);

--
-- Indices de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  ADD PRIMARY KEY (`id_tipo_documento`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_proyecto`
--
ALTER TABLE `tipos_proyecto`
  ADD PRIMARY KEY (`id_tipo_proyecto`),
  ADD UNIQUE KEY `categoria` (`categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupo_aprendices`
--
ALTER TABLE `grupo_aprendices`
  MODIFY `id_grupo_aprendiz` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `semilleros`
--
ALTER TABLE `semilleros`
  MODIFY `id_semillero` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  MODIFY `id_tipo_documento` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_proyecto`
--
ALTER TABLE `tipos_proyecto`
  MODIFY `id_tipo_proyecto` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD CONSTRAINT `fk_aprendiz_tipo_doc` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aprendiz_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_documento_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupo_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo_aprendices`
--
ALTER TABLE `grupo_aprendices`
  ADD CONSTRAINT `fk_ga_aprendiz_perfil` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ga_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lideres_semillero`
--
ALTER TABLE `lideres_semillero`
  ADD CONSTRAINT `fk_lider_tipo_doc` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lider_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyecto_semillero` FOREIGN KEY (`id_semillero`) REFERENCES `semilleros` (`id_semillero`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_proyecto_tipo` FOREIGN KEY (`id_tipo_proyecto`) REFERENCES `tipos_proyecto` (`id_tipo_proyecto`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `semilleros`
--
ALTER TABLE `semilleros`
  ADD CONSTRAINT `fk_semillero_liderperfil` FOREIGN KEY (`id_lider_usuario`) REFERENCES `lideres_semillero` (`id_usuario`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
