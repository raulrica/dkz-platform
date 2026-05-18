-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para dkz_platform
CREATE DATABASE IF NOT EXISTS `dkz_platform` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `dkz_platform`;

-- Volcando estructura para tabla dkz_platform.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.categorias: ~2 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nombre`) VALUES
	(1, 'Fotógrafo'),
	(2, 'Videógrafo');

-- Volcando estructura para tabla dkz_platform.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `necesidad` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.clientes: ~1 rows (aproximadamente)
INSERT INTO `clientes` (`id`, `usuario_id`, `necesidad`) VALUES
	(1, 3, '');

-- Volcando estructura para tabla dkz_platform.portfolio
CREATE TABLE IF NOT EXISTS `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profesional_id` int(11) NOT NULL,
  `tipo` enum('link','archivo') NOT NULL,
  `valor` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profesional_id` (`profesional_id`),
  CONSTRAINT `portfolio_ibfk_1` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.portfolio: ~2 rows (aproximadamente)
INSERT INTO `portfolio` (`id`, `profesional_id`, `tipo`, `valor`) VALUES
	(1, 1, 'link', 'https://miportfolio.com'),
	(2, 3, 'link', 'https://carlos.com');

-- Volcando estructura para tabla dkz_platform.profesionales
CREATE TABLE IF NOT EXISTS `profesionales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `valoracion_media` decimal(2,1) DEFAULT 0.0,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `categoria_id` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `profesionales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profesionales_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.profesionales: ~3 rows (aproximadamente)
INSERT INTO `profesionales` (`id`, `usuario_id`, `descripcion`, `valoracion_media`, `estado`, `categoria_id`, `foto`) VALUES
	(1, 1, 'Soy fotógrafo profesional', 1.0, 'aprobado', 1, NULL),
	(2, 2, 'Busco fotógrafo para eventos', 0.0, 'aprobado', 1, NULL),
	(3, 4, 'Videógrafo profesional', 0.0, 'pendiente', 2, 'foto_6a0446fc3e5e6.png');

-- Volcando estructura para tabla dkz_platform.skills
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profesional_id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profesional_id` (`profesional_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.skills: ~4 rows (aproximadamente)
INSERT INTO `skills` (`id`, `profesional_id`, `nombre`) VALUES
	(1, 1, 'Lightroom'),
	(2, 1, 'Drone'),
	(3, 3, 'Premiere'),
	(4, 3, 'After Effects');

-- Volcando estructura para tabla dkz_platform.solicitudes
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `profesional_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_necesidad` date DEFAULT NULL,
  `estado` enum('pendiente','aceptada','rechazada','completada') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `profesional_id` (`profesional_id`),
  CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.solicitudes: ~1 rows (aproximadamente)
INSERT INTO `solicitudes` (`id`, `cliente_id`, `profesional_id`, `descripcion`, `fecha_necesidad`, `estado`, `created_at`) VALUES
	(8, 1, 1, 'Busco fotógrafo para eventos', '2026-05-22', 'pendiente', '2026-05-13 09:19:21');

-- Volcando estructura para tabla dkz_platform.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('profesional','cliente') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.usuarios: ~4 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `telefono`, `password`, `tipo`, `created_at`) VALUES
	(1, 'guillermo', 'dkz', 'guille@dkz.com', '6546161', '$2y$10$dEd1EemD4AI8/xlCNR04Guok6W77MGJwxsnrDlJiG0QRdBBQVyl.2', 'profesional', '2026-05-11 07:20:16'),
	(2, 'María', 'López', 'maria@test.com', '611111111', '$2y$10$WL8UC1wyGdVG54zNiqx2nuJGyj0kfdznF5npT4q1SyIk57w1R5cHm', 'profesional', '2026-05-13 09:15:35'),
	(3, 'maria', 'lopez', 'marialopez@gmail.com', '6546161', '$2y$10$/b7oIUMnBoNTDZBSquiZZuRigw7LlBJFpb87cA.bAWkL6UCJwAsIW', 'cliente', '2026-05-13 09:19:17'),
	(4, 'Carlos', 'Martínez', 'carlos@test.com', '622222222', '$2y$10$4EJwRY6VkPiEG0pNRI2ThuNS3dRrz08NRUbfXgowymSabqALLMaIi', 'profesional', '2026-05-13 09:40:12');

-- Volcando estructura para tabla dkz_platform.valoraciones
CREATE TABLE IF NOT EXISTS `valoraciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profesional_id` int(11) NOT NULL,
  `puntuacion` tinyint(4) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `administrador` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `profesional_id` (`profesional_id`),
  CONSTRAINT `valoraciones_ibfk_1` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla dkz_platform.valoraciones: ~1 rows (aproximadamente)
INSERT INTO `valoraciones` (`id`, `profesional_id`, `puntuacion`, `comentario`, `administrador`, `created_at`) VALUES
	(1, 1, 1, 'lolo', 'serafín', '2026-05-13 07:56:28');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
