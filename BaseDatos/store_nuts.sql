-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2024 a las 22:50:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12




--
-- Base de datos: `hardware_nuts_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `marca` VARCHAR(50) NOT NULL,
  `puntuacion` INT(1) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  -- Clave primaria auto-incremental
  `id_quote` int(11) NOT NULL,  -- Campo que puede contener duplicados
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),  -- Clave primaria
  INDEX (`id_quote`)  -- Agregar un índice en id_quote para referencia
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tipo_persona` enum('Admin','Permanente','Periódico','Casual','Nuevo') NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `numeroDocumento` varchar(20) NOT NULL,  -- Cambiar a varchar para permitir diferentes formatos
  `tipoDocumento` int(5) NOT NULL,
  `tipoContribuyente` int(5) NOT NULL,
  `numeroTelefono` varchar(20) NOT NULL,   -- Cambiar a varchar por las mismas razones
  `departamento` varchar(50) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `codigoPostal` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `numeroDocumento` varchar(20) NOT NULL,  -- Cambiar a varchar para permitir diferentes formatos
  `tipoDocumento` int(5) NOT NULL,
  `tipoContribuyente` int(5) NOT NULL,
  `numeroTelefono` varchar(20) NOT NULL,   -- Cambiar a varchar por las mismas razones
  `departamento` varchar(50) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `codigoPostal` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Tabla de carrito de compras
CREATE TABLE `bag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  -- Clave primaria auto-incremental para bag
  `quote_id` int(11) DEFAULT NULL,  -- Referencia a id_quote de quotations
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),  -- Clave primaria para bag
  FOREIGN KEY (`quote_id`) REFERENCES `quotations`(`id_quote`) ON DELETE CASCADE,  -- Referencia a id_quote
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




--
-- Filtros para la tabla `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quotations_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Insersion de inventario por default
INSERT INTO `products` (`name`, `price`, `marca`, `puntuacion`, `code`, `cantidad`)
VALUES
('Cemento para construcción Argos Tipo HE de 42.5Kg', 45000.00, 'Argos', 3, '1234567890', 1),
('Cemento Multi-Propósito Yura de alta durabilidad 42.5Kg', 35000.00, 'Yura', 4, '54645646646554654', 1),
('Cemento de uso general APU de 42.5Kg', 35000.00, 'Andino', 5, '54645646646554654', 1),
('Cemento para construcción Holcim Tipo 1 de 50Kg', 35000.00, 'Holcim', 4, '54645646646554654', 1),
('Pintura para Interior Lavable 2.5 Galones Blanco', 125000.00, 'Kolor', 4, '54645646646554654', 1),
('Pintura para Interior Súper Lavable Max Blanco 2.5 Galones', 125000.00, 'Pintuco', 2, '54645646646554654', 1),
('Combo Taladro Percutor 12 pulg 550W + Pulidora 4-12 pulg 820W Black+Decker', 125000.00, 'Black&Decker', 4, '54645646646554654', 1),
('Taladro Percutor Atornillador Inalámbrico 12 Pulgada 18V Black And White', 150000.00, 'Black&Decker', 0, '54645646646554654', 1),
('Taladro 12 Percutor 550W 2800 Rpm Vvr', 150000.00, 'Black&Decker', 4, '54645646646554654', 1);
