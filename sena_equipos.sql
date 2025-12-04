-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-09-2025 a las 03:14:54
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
-- Base de datos: `sena_equipos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aires_acondicionados`
--

CREATE TABLE `aires_acondicionados` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `tipo_activo` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `claves_duro` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `procesador` varchar(100) DEFAULT NULL,
  `placa` varchar(50) DEFAULT NULL,
  `s_m` varchar(50) DEFAULT NULL,
  `placa_teclado` varchar(50) DEFAULT NULL,
  `serial_teclado` varchar(50) DEFAULT NULL,
  `entrega_teclado` enum('Sí','No') DEFAULT 'No',
  `obs_teclado` text DEFAULT NULL,
  `placa_mouse` varchar(50) DEFAULT NULL,
  `serial_mouse` varchar(50) DEFAULT NULL,
  `entrega_mouse` enum('Sí','No') DEFAULT 'No',
  `obs_mouse` text DEFAULT NULL,
  `placa_monitor` varchar(50) DEFAULT NULL,
  `serial_monitor` varchar(50) DEFAULT NULL,
  `entrega_monitor` enum('Sí','No') DEFAULT 'No',
  `obs_monitor` text DEFAULT NULL,
  `placa_cpu` varchar(50) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `firma_acta` enum('Sí','No') DEFAULT 'No',
  `borrado_seguro` enum('Sí','No') DEFAULT 'No',
  `nombre_borrado` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','En Mantenimiento','Dañado') DEFAULT 'Activo',
  `fecha` date DEFAULT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` date DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_ap`
--

CREATE TABLE `puntos_ap` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_comerciales`
--

CREATE TABLE `puntos_comerciales` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_datos`
--

CREATE TABLE `puntos_datos` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_regulares`
--

CREATE TABLE `puntos_regulares` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_switch`
--

CREATE TABLE `puntos_switch` (
  `id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `placa` varchar(50) DEFAULT NULL,
  `numero_puertos` int(11) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Mantenimiento') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id`, `nombre`, `ubicacion`, `imagen`) VALUES
(1, 'SEDE PRINCIPAL', 'quibdo, Colombia', NULL),
(2, 'SEDE MINERCOL', 'quibdo, Colombia', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('ADMIN','PROVEEDOR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password`, `tipo`) VALUES
(117, 'c', '$2y$10$kXEMdq63HeL90982QUSsj.MehUjjoeAoBh2qD7VYjMpbOaNyg8hAK', 'ADMIN');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aires_acondicionados`
--
ALTER TABLE `aires_acondicionados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_aires_ubicacion` (`ubicacion`),
  ADD KEY `idx_aires_estado` (`estado`),
  ADD KEY `idx_aires_numero` (`numero`);

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`);

--
-- Indices de la tabla `puntos_ap`
--
ALTER TABLE `puntos_ap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_puntos_ap_ubicacion` (`ubicacion`),
  ADD KEY `idx_puntos_ap_estado` (`estado`),
  ADD KEY `idx_puntos_ap_numero` (`numero`);

--
-- Indices de la tabla `puntos_comerciales`
--
ALTER TABLE `puntos_comerciales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_puntos_comerciales_ubicacion` (`ubicacion`),
  ADD KEY `idx_puntos_comerciales_estado` (`estado`),
  ADD KEY `idx_puntos_comerciales_numero` (`numero`);

--
-- Indices de la tabla `puntos_datos`
--
ALTER TABLE `puntos_datos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_puntos_datos_ubicacion` (`ubicacion`),
  ADD KEY `idx_puntos_datos_estado` (`estado`),
  ADD KEY `idx_puntos_datos_numero` (`numero`);

--
-- Indices de la tabla `puntos_regulares`
--
ALTER TABLE `puntos_regulares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_puntos_regulares_ubicacion` (`ubicacion`),
  ADD KEY `idx_puntos_regulares_estado` (`estado`),
  ADD KEY `idx_puntos_regulares_numero` (`numero`);

--
-- Indices de la tabla `puntos_switch`
--
ALTER TABLE `puntos_switch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `idx_puntos_switch_ubicacion` (`ubicacion`),
  ADD KEY `idx_puntos_switch_estado` (`estado`),
  ADD KEY `idx_puntos_switch_marca` (`marca`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aires_acondicionados`
--
ALTER TABLE `aires_acondicionados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `puntos_ap`
--
ALTER TABLE `puntos_ap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `puntos_comerciales`
--
ALTER TABLE `puntos_comerciales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `puntos_datos`
--
ALTER TABLE `puntos_datos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `puntos_regulares`
--
ALTER TABLE `puntos_regulares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `puntos_switch`
--
ALTER TABLE `puntos_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aires_acondicionados`
--
ALTER TABLE `aires_acondicionados`
  ADD CONSTRAINT `aires_acondicionados_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD CONSTRAINT `dispositivos_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_ap`
--
ALTER TABLE `puntos_ap`
  ADD CONSTRAINT `puntos_ap_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_comerciales`
--
ALTER TABLE `puntos_comerciales`
  ADD CONSTRAINT `puntos_comerciales_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_datos`
--
ALTER TABLE `puntos_datos`
  ADD CONSTRAINT `puntos_datos_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_regulares`
--
ALTER TABLE `puntos_regulares`
  ADD CONSTRAINT `puntos_regulares_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_switch`
--
ALTER TABLE `puntos_switch`
  ADD CONSTRAINT `puntos_switch_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
