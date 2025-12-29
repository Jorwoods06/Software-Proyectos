-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2025 a las 04:50:42
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
-- Base de datos: `providencia_gestion_proyectos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','finalizado','eliminado') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `proyecto_id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`, `updated_at`) VALUES
(1, 3, 'analisis del proyecto', 'se realizara el analisis de la necesidad', NULL, NULL, 'pendiente', '2025-09-19 19:42:53', '2025-09-19 19:42:53'),
(2, 5, 'Base de datos de usuarios', 'Realizar un listado de todos los usuarios que tiene correos como dominio protejer y Colegio providencia.', NULL, NULL, 'pendiente', '2025-09-19 21:23:45', '2025-09-19 21:23:45'),
(4, 9, 'evalucion', 'realizar analisis de la propuesta', NULL, NULL, 'pendiente', '2025-11-19 21:15:52', '2025-11-19 21:15:52'),
(6, 10, 'Informarme sobre software de proyectos', 'indagar sobre el funcionamiento correctivo de software de proyectos y como se podria llevar a cabo en este mismo sistema', NULL, NULL, 'pendiente', '2025-12-12 01:59:07', '2025-12-12 01:59:07'),
(7, 10, 'mejorar el sidebar', 'mejorarlo', NULL, NULL, 'pendiente', '2025-12-12 21:38:01', '2025-12-12 21:38:01'),
(8, 5, 'Funcionamiento CRUD', 's', '2025-12-22 12:37:51', NULL, 'pendiente', '2025-12-13 02:00:39', '2025-12-22 17:37:51'),
(9, 13, 'Fase 1 - prueba', 'r', '2025-12-16 17:49:34', NULL, 'pendiente', '2025-12-13 07:05:04', '2025-12-16 22:49:34'),
(10, 13, 'Tareas Desarrollo', 'sa', '2025-12-14 20:25:59', NULL, 'pendiente', '2025-12-13 07:07:48', '2025-12-15 01:26:06'),
(11, 13, 'prueba', 's', '2025-12-14 00:00:00', '2025-12-16 16:21:59', 'pendiente', '2025-12-15 01:21:38', '2025-12-15 01:21:38'),
(12, 13, 'prueba 2', 'prueba', '2025-12-14 00:00:00', NULL, 'pendiente', '2025-12-15 01:29:08', '2025-12-15 01:29:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `tarea_id`, `user_id`, `comentario`, `created_at`) VALUES
(1, 4, 16, 'El dia 22/12/25 se empezo la documentacion del software de proyectos', '2025-12-22 20:11:25'),
(2, 4, 2, 'Dejo adjunto evidencia', '2025-12-23 01:36:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `lider_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `lider_id`, `created_at`, `updated_at`) VALUES
(1, 'Tecnologia Informatica', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(2, 'Academicas', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(3, 'Cadena de Abastecimiento', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(4, 'Renueva', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(5, 'Calidad', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(6, 'Comercial', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(7, 'Mantenimiento', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(8, 'Formacion', NULL, NULL, '2025-09-11 20:22:59', '2025-09-12 02:13:56'),
(9, 'Produccion', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(10, 'Talento Humano', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(11, 'Administracion', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(12, 'Financiero', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(13, 'CDI', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59'),
(14, 'Ingerieria y Desarrollo de Producto ', NULL, NULL, '2025-09-11 20:22:59', '2025-09-12 02:14:11'),
(15, 'Planeacion y Consolidacion', NULL, NULL, '2025-09-11 20:22:59', '2025-09-11 20:22:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencias`
--

CREATE TABLE `evidencias` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evidencias`
--

INSERT INTO `evidencias` (`id`, `tarea_id`, `archivo`, `tipo`, `created_at`) VALUES
(1, 4, 'evidencias/tarea_4_informe-de-equipos-localizados-en-el-archivador_1766437377_jigh6jle.docx', 'documento', '2025-12-23 02:02:58'),
(7, 4, 'evidencias/tarea_4_software-1-e1550080097569_1766979038_Z36YP1R1.jpg', 'imagen', '2025-12-29 08:30:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_config`
--

CREATE TABLE `notificacion_config` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificacion_config`
--

INSERT INTO `notificacion_config` (`id`, `clave`, `valor`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'horas_antes_vencimiento', '24', 'Horas antes de la fecha de vencimiento para enviar notificación de tarea próxima a vencer', '2025-12-29 01:37:12', '2025-12-29 01:37:12'),
(2, 'notificar_admin_tareas_vencidas', '1', 'Notificar a administradores cuando hay tareas vencidas (1 = sí, 0 = no)', '2025-12-29 01:37:12', '2025-12-29 01:37:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'crear proyecto', 'Puede crear nuevos proyectos', '2025-08-25 00:32:49', '2025-09-14 02:39:45'),
(2, 'editar proyecto', 'Puede editar proyectos existentes', '2025-08-25 00:32:49', '2025-09-14 02:39:50'),
(3, 'eliminar proyecto', 'Puede eliminar proyectos', '2025-08-25 00:32:49', '2025-09-14 02:39:55'),
(4, 'ver proyecto', 'Puede ver proyectos', '2025-08-25 00:32:49', '2025-09-14 02:40:00'),
(5, 'crear actividades', 'Puede crear actividades en proyectos', '2025-08-25 00:32:49', '2025-09-14 02:40:07'),
(6, 'editar actividades', 'Puede editar actividades', '2025-08-25 00:32:49', '2025-09-14 02:40:14'),
(7, 'eliminar actividades', 'Puede eliminar actividades', '2025-08-25 00:32:49', '2025-09-14 02:40:19'),
(8, 'ver actividades', 'Puede ver actividades', '2025-08-25 00:32:49', '2025-09-14 02:40:26'),
(9, 'crear tarea', 'Puede crear tareas en actividades', '2025-08-25 00:32:49', '2025-09-14 02:40:33'),
(10, 'editar tarea', 'Puede editar tareas', '2025-08-25 00:32:49', '2025-09-14 02:40:38'),
(11, 'eliminar tarea', 'Puede eliminar tareas', '2025-08-25 00:32:49', '2025-09-14 02:40:42'),
(12, 'ver tarea', 'Puede ver tareas', '2025-08-25 00:32:49', '2025-09-14 02:40:46'),
(13, 'invitar usuarios a este proyecto', 'Permite invitar usuarios de otros departamentos a el proyecto deseado', '2025-09-17 14:48:52', '2025-09-17 14:48:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','finalizado','cancelado') DEFAULT 'pendiente',
  `color` varchar(7) DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `color`, `departamento_id`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'prueba 1 de edicion2', 'prueba de edición 123.', '2025-09-09', '2025-09-24', 'pendiente', '#D63384', 1, 2, '2025-09-18 04:43:20', '2025-12-14 19:44:18'),
(4, 'ensayo', 'proyecto de prueba', '2025-09-19', '2025-09-30', 'cancelado', '#DC3545', 1, 2, '2025-09-19 19:36:22', '2025-12-14 19:44:18'),
(5, 'Migración de Google a Microsoft', 'Este proyecto se lleva a cabo con el objetivo de centralizar toda nuestra información y aplicaciones en una sola plataforma, integradas bajo una misma cuenta. Entre los beneficios, destacamos la unificación de toda la paquetería de oficina, lo que nos permitirá trabajar de manera más eficiente y completamente integrada..', '2025-09-22', '2025-09-30', 'pendiente', '#FD7E14', 1, 7, '2025-09-19 21:17:57', '2025-12-14 19:44:18'),
(6, 'ffffbbbbbzzzzc666', 'fffffxsssss', '2025-10-08', '2025-10-30', 'cancelado', '#FFC107', 1, 2, '2025-10-08 17:18:54', '2025-12-14 19:44:18'),
(7, 'proyecto', 'proyectoddd', '2025-10-08', '2025-10-17', 'cancelado', '#20C997', 1, 2, '2025-10-08 17:30:36', '2025-12-14 19:44:18'),
(8, 'gestion de proyectos', 'prueba.', '2025-10-28', '2025-10-31', 'cancelado', '#198754', 1, 2, '2025-10-28 18:05:17', '2025-12-14 19:44:18'),
(9, 'gestion de proyectos', 'presentacion de proyectos de forma que se pueda realizar seguimiento de las tareas y cumplimiento y revision de tareas', '2025-11-19', '2025-11-30', 'pendiente', '#0DCAF0', 1, 2, '2025-11-19 18:47:09', '2025-12-14 19:44:18'),
(10, 'Terminar Proyecto de Proyectos 🦅🔥', 'xd', '2025-12-11', '2025-12-31', 'cancelado', '#6610F2', 1, 2, '2025-12-12 01:58:05', '2025-12-14 19:44:18'),
(11, 'Prueba Proyectos', 'xd', '2025-12-12', '2025-12-20', 'cancelado', '#E83E8C', 1, 16, '2025-12-13 01:44:08', '2025-12-14 19:44:18'),
(13, 'Terminar Proyecto de Proyectos 🦅🔥', 's', '2025-12-12', '2025-12-31', 'pendiente', '#0DCAF0', 1, 16, '2025-12-13 07:02:48', '2025-12-14 19:44:18'),
(14, 'prueba usuario sin rol admin', 'ej', '2025-12-28', '2025-12-31', 'pendiente', '#FF6B6B', 1, 17, '2025-12-29 06:01:17', '2025-12-29 06:01:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_user_permiso`
--

CREATE TABLE `proyecto_user_permiso` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `tipo` enum('allow','deny') NOT NULL DEFAULT 'allow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecto_user_permiso`
--

INSERT INTO `proyecto_user_permiso` (`id`, `proyecto_id`, `user_id`, `permiso_id`, `tipo`) VALUES
(1, 13, 17, 5, 'allow'),
(2, 13, 17, 9, 'allow'),
(3, 13, 17, 6, 'allow'),
(4, 13, 17, 10, 'allow'),
(5, 13, 17, 7, 'allow'),
(6, 13, 17, 3, 'allow'),
(7, 13, 17, 11, 'allow'),
(8, 13, 17, 13, 'allow'),
(9, 13, 17, 8, 'allow'),
(10, 13, 17, 4, 'allow'),
(11, 13, 17, 12, 'allow'),
(12, 5, 17, 8, 'allow'),
(13, 5, 17, 4, 'allow'),
(14, 5, 17, 12, 'allow'),
(15, 5, 18, 5, 'allow'),
(16, 5, 18, 1, 'allow'),
(17, 5, 18, 9, 'allow'),
(18, 5, 18, 6, 'allow'),
(19, 5, 18, 2, 'allow'),
(20, 5, 18, 10, 'allow'),
(21, 5, 18, 7, 'allow'),
(22, 5, 18, 3, 'allow'),
(23, 5, 18, 11, 'allow'),
(24, 5, 18, 13, 'allow'),
(25, 5, 18, 8, 'allow'),
(26, 5, 18, 4, 'allow'),
(27, 5, 18, 12, 'allow');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_usuario`
--

CREATE TABLE `proyecto_usuario` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rol_proyecto` enum('lider','colaborador','visor') DEFAULT 'colaborador',
  `estado_invitacion` enum('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'aceptada',
  `token_invitacion` varchar(64) DEFAULT NULL,
  `fecha_invitacion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecto_usuario`
--

INSERT INTO `proyecto_usuario` (`id`, `proyecto_id`, `user_id`, `rol_proyecto`, `estado_invitacion`, `token_invitacion`, `fecha_invitacion`, `created_at`, `updated_at`) VALUES
(2, 3, 2, 'lider', 'aceptada', NULL, NULL, '2025-09-18 04:43:20', '2025-09-18 04:43:20'),
(3, 4, 2, 'lider', 'aceptada', NULL, NULL, '2025-09-19 19:36:22', '2025-09-19 19:36:22'),
(4, 5, 7, 'lider', 'aceptada', NULL, NULL, '2025-09-19 21:17:57', '2025-09-19 21:17:57'),
(5, 6, 2, 'lider', 'aceptada', NULL, NULL, '2025-10-08 17:18:54', '2025-10-08 17:18:54'),
(6, 7, 2, 'lider', 'aceptada', NULL, NULL, '2025-10-08 17:30:36', '2025-10-08 17:30:36'),
(7, 8, 2, 'lider', 'aceptada', NULL, NULL, '2025-10-28 18:05:17', '2025-10-28 18:05:17'),
(8, 9, 2, 'lider', 'aceptada', NULL, NULL, '2025-11-19 18:47:09', '2025-11-19 18:47:09'),
(9, 10, 2, 'lider', 'aceptada', NULL, NULL, '2025-12-12 01:58:06', '2025-12-12 01:58:06'),
(10, 11, 16, 'lider', 'aceptada', NULL, NULL, '2025-12-13 01:44:08', '2025-12-13 01:44:08'),
(12, 13, 16, 'lider', 'aceptada', NULL, NULL, '2025-12-13 07:02:48', '2025-12-13 07:02:48'),
(13, 13, 2, 'colaborador', 'aceptada', NULL, NULL, '2025-12-13 07:02:48', '2025-12-13 07:02:48'),
(14, 13, 17, 'colaborador', 'aceptada', NULL, NULL, '2025-12-16 22:14:44', '2025-12-16 22:14:44'),
(15, 14, 17, 'lider', 'aceptada', NULL, NULL, '2025-12-29 06:01:17', '2025-12-29 06:01:17'),
(16, 5, 17, 'colaborador', 'aceptada', NULL, NULL, '2025-12-29 06:17:05', '2025-12-29 06:17:05'),
(17, 5, 18, 'colaborador', 'pendiente', 'c8be7ca7185b8128e7432d5376616e5ba8a55652fb7e1db6841b0343fffca45a', '2025-12-29 06:45:58', '2025-12-29 06:45:58', '2025-12-29 06:45:58'),
(18, 13, 18, 'colaborador', 'aceptada', '8144c31481112c506f94873545c702a7a49d6e1a74d21534203103984f3a1b8f', '2025-12-29 06:56:02', '2025-12-29 06:56:02', '2025-12-29 06:59:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'administrador', 'Acceso total al sistema', '2025-08-25 00:32:49', '2025-10-09 17:04:18'),
(2, 'Lider', 'Gestión de proyectos , actividades y tareas asignadas', '2025-08-25 00:32:49', '2025-09-14 01:30:35'),
(3, 'Colaborador', 'Participa en actividades y tareas asignadas', '2025-08-25 00:32:49', '2025-09-14 01:29:28'),
(4, 'Lector', 'Solo puede visualizar la información', '2025-08-25 00:32:49', '2025-09-14 01:30:01'),
(5, 'TI', NULL, '2025-09-14 09:06:51', '2025-09-14 09:06:51'),
(6, 'prueba', NULL, '2025-09-14 09:40:10', '2025-09-14 09:40:10'),
(7, 'pp', NULL, '2025-09-14 10:27:59', '2025-09-14 10:27:59'),
(8, '1', NULL, '2025-09-14 12:30:56', '2025-09-14 12:30:56'),
(9, '2', NULL, '2025-09-14 12:31:10', '2025-09-14 12:31:10'),
(10, '22', NULL, '2025-09-14 12:31:17', '2025-09-14 12:31:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permiso`
--

INSERT INTO `rol_permiso` (`id`, `rol_id`, `permiso_id`) VALUES
(2, 1, 1),
(8, 1, 3),
(11, 1, 4),
(1, 1, 5),
(7, 1, 7),
(10, 1, 8),
(3, 1, 9),
(9, 1, 11),
(12, 1, 12),
(58, 1, 13),
(17, 2, 1),
(20, 2, 2),
(23, 2, 4),
(16, 2, 5),
(19, 2, 6),
(22, 2, 8),
(18, 2, 9),
(21, 2, 10),
(24, 2, 12),
(34, 3, 4),
(33, 3, 8),
(31, 3, 9),
(35, 3, 12),
(39, 4, 4),
(38, 4, 8),
(40, 4, 12),
(41, 5, 1),
(42, 5, 2),
(43, 5, 3),
(44, 5, 4),
(45, 5, 5),
(46, 5, 6),
(47, 5, 7),
(48, 5, 8),
(49, 5, 9),
(50, 5, 10),
(51, 5, 11),
(52, 5, 12),
(54, 6, 1),
(53, 6, 2),
(57, 8, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `actividad_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','completado','eliminado') DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta') DEFAULT 'media',
  `responsable_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `actividad_id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `prioridad`, `responsable_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 9, 'Programar', NULL, NULL, '2025-12-12 23:59:59', 'pendiente', 'media', NULL, NULL, '2025-12-13 07:10:54', '2025-12-15 18:03:23'),
(2, 9, 'desplegar', NULL, NULL, '2025-12-12 23:59:59', 'pendiente', 'media', NULL, NULL, '2025-12-13 07:11:42', '2025-12-14 19:44:18'),
(3, 4, 'Estudiar', NULL, NULL, NULL, 'pendiente', 'media', NULL, NULL, '2025-12-13 07:31:24', '2025-12-13 07:31:24'),
(4, 9, 'Documentacion sobre software de proyectos', 'La presente tarea consiste en elaborar una documentación detallada sobre software de gestión de proyectos. El objetivo es investigar y recopilar información relevante sobre diferentes herramientas utilizadas para planificar, organizar, coordinar y supervisar proyectos en distintos ámbitos. La documentación debe incluir aspectos como: funciones principales, ventajas y desventajas, ejemplos de software disponibles en el mercado, y la manera en que estas herramientas facilitan la gestión eficiente de proyectos', NULL, '2025-12-31 23:59:59', 'pendiente', 'baja', NULL, NULL, '2025-12-13 07:32:23', '2025-12-23 00:56:09'),
(5, NULL, 'dormir', NULL, NULL, '2025-12-12 23:59:59', 'completado', 'media', NULL, 16, '2025-12-13 08:00:44', '2025-12-14 19:44:18'),
(6, NULL, 'volar', NULL, NULL, NULL, 'completado', 'media', NULL, 16, '2025-12-13 08:08:22', '2025-12-23 18:45:39'),
(7, 10, 'Añadir funciones de edicion/eliminacion', NULL, NULL, '2025-12-13 23:59:59', 'completado', 'media', NULL, NULL, '2025-12-13 21:14:43', '2025-12-15 02:02:01'),
(8, 9, 's', NULL, NULL, NULL, 'eliminado', 'media', NULL, NULL, '2025-12-14 22:16:36', '2025-12-15 01:14:17'),
(9, 9, 't', NULL, NULL, NULL, 'completado', 'media', NULL, NULL, '2025-12-14 22:16:43', '2025-12-15 19:44:53'),
(10, 9, 's', NULL, NULL, '2025-12-31 23:59:59', 'pendiente', 'media', NULL, NULL, '2025-12-14 22:17:01', '2025-12-23 00:46:01'),
(11, 10, 'Crear paginadora reutilizable', NULL, NULL, NULL, 'completado', 'media', NULL, NULL, '2025-12-14 22:20:18', '2025-12-15 21:31:29'),
(12, 10, 'modificar el sistema de fechas, que ponga fecha inicio y fin', NULL, NULL, NULL, 'completado', 'media', NULL, NULL, '2025-12-14 22:20:44', '2025-12-15 17:28:06'),
(14, 10, 'corregir estados de tareas', NULL, NULL, NULL, 'completado', 'media', NULL, NULL, '2025-12-14 22:21:11', '2025-12-15 21:15:07'),
(15, 10, 'revisar flujo entre diferentes usuario', NULL, NULL, NULL, 'pendiente', 'media', NULL, NULL, '2025-12-14 22:21:42', '2025-12-15 01:31:50'),
(16, 9, 'prueba', 'Pruebas descripcion', '2025-12-14 14:51:00', '2025-12-15 08:51:59', 'pendiente', 'media', NULL, NULL, '2025-12-15 00:51:52', '2025-12-23 00:55:27'),
(17, 10, 'ejemplo', NULL, '2025-12-15 00:00:00', '2025-12-18 07:42:59', 'completado', 'media', NULL, NULL, '2025-12-15 17:42:30', '2025-12-15 21:31:51'),
(18, NULL, 'realizar reunion', NULL, '2025-12-15 00:00:00', '2025-12-22 07:43:59', 'completado', 'media', NULL, 16, '2025-12-15 17:43:11', '2025-12-23 18:45:37'),
(19, 10, 'cambiar nombre actividad a fases', NULL, '2025-12-15 00:00:00', NULL, 'completado', 'media', NULL, NULL, '2025-12-15 19:50:27', '2025-12-15 21:15:00'),
(20, 10, 'limitar proyecto en el sidebar, que muestre los ultimos 5 proyectos editados', NULL, '2025-12-15 00:00:00', NULL, 'pendiente', 'media', NULL, NULL, '2025-12-15 19:51:20', '2025-12-15 21:31:42'),
(21, 10, 'cambiar front de actividades, reducir padding', NULL, '2025-12-15 00:00:00', NULL, 'completado', 'media', NULL, NULL, '2025-12-15 19:51:49', '2025-12-15 21:12:05'),
(22, 10, 'probar paginador', NULL, '2025-12-15 00:00:00', '2025-12-22 17:46:59', 'completado', 'media', NULL, NULL, '2025-12-15 21:25:02', '2025-12-23 00:46:48'),
(23, 10, 'paginador', NULL, '2025-12-15 00:00:00', NULL, 'completado', 'media', NULL, NULL, '2025-12-15 21:25:25', '2025-12-15 21:25:50'),
(24, NULL, 'r', NULL, '2025-12-15 00:00:00', NULL, 'completado', 'media', NULL, 16, '2025-12-16 00:50:37', '2025-12-23 18:45:34'),
(25, 10, 'Prueba Proyectos', NULL, '2025-12-16 00:00:00', '2025-12-22 17:46:59', 'pendiente', 'media', NULL, NULL, '2025-12-16 22:15:14', '2025-12-23 00:46:28'),
(26, 11, 'prueba', NULL, '2025-12-16 00:00:00', NULL, 'pendiente', 'media', NULL, NULL, '2025-12-16 22:18:19', '2025-12-16 22:18:19'),
(27, 11, 'r', NULL, '2025-12-16 00:00:00', NULL, 'pendiente', 'media', NULL, NULL, '2025-12-16 22:19:13', '2025-12-16 22:19:13'),
(28, NULL, 'Hacer dashboard por proyectos creados', NULL, '2025-12-23 14:39:34', NULL, 'completado', 'media', NULL, 16, '2025-12-23 19:39:35', '2025-12-28 05:56:14'),
(29, NULL, 'Generar notificaciones', NULL, '2025-12-23 14:39:49', NULL, 'completado', 'media', NULL, 16, '2025-12-23 19:39:49', '2025-12-29 06:59:31'),
(30, NULL, 'Revisar paddings, margenes y size&#039;s de letras', NULL, '2025-12-23 14:40:09', NULL, 'pendiente', 'media', NULL, 16, '2025-12-23 19:40:09', '2025-12-23 19:40:09'),
(31, NULL, 'Guardar las evidencias en el bucket', NULL, '2025-12-23 14:40:48', NULL, 'completado', 'media', NULL, 16, '2025-12-23 19:40:48', '2025-12-29 08:31:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_usuario`
--

CREATE TABLE `tarea_usuario` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea_usuario`
--

INSERT INTO `tarea_usuario` (`id`, `tarea_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 16, '2025-12-13 07:10:54', '2025-12-13 07:10:54'),
(2, 2, 16, '2025-12-13 07:11:42', '2025-12-13 07:11:42'),
(3, 2, 2, '2025-12-13 07:11:42', '2025-12-13 07:11:42'),
(4, 3, 2, '2025-12-13 07:31:24', '2025-12-13 07:31:24'),
(5, 4, 2, '2025-12-13 07:32:23', '2025-12-13 07:32:23'),
(6, 7, 16, '2025-12-13 21:14:44', '2025-12-13 21:14:44'),
(7, 7, 2, '2025-12-13 21:14:44', '2025-12-13 21:14:44'),
(8, 8, 2, '2025-12-14 22:16:36', '2025-12-14 22:16:36'),
(9, 9, 2, '2025-12-14 22:16:43', '2025-12-14 22:16:43'),
(10, 10, 16, '2025-12-14 22:17:01', '2025-12-14 22:17:01'),
(11, 10, 2, '2025-12-14 22:17:01', '2025-12-14 22:17:01'),
(12, 11, 16, '2025-12-14 22:20:18', '2025-12-14 22:20:18'),
(13, 12, 16, '2025-12-14 22:20:44', '2025-12-14 22:20:44'),
(15, 14, 16, '2025-12-14 22:21:11', '2025-12-14 22:21:11'),
(16, 15, 16, '2025-12-14 22:21:42', '2025-12-14 22:21:42'),
(17, 16, 2, '2025-12-15 00:51:52', '2025-12-15 00:51:52'),
(18, 17, 16, '2025-12-15 17:42:30', '2025-12-15 17:42:30'),
(19, 17, 2, '2025-12-15 17:42:30', '2025-12-15 17:42:30'),
(20, 19, 16, '2025-12-15 19:50:27', '2025-12-15 19:50:27'),
(21, 20, 16, '2025-12-15 19:51:20', '2025-12-15 19:51:20'),
(22, 21, 16, '2025-12-15 19:51:49', '2025-12-15 19:51:49'),
(23, 22, 16, '2025-12-15 21:25:02', '2025-12-15 21:25:02'),
(24, 23, 2, '2025-12-15 21:25:25', '2025-12-15 21:25:25'),
(25, 25, 17, '2025-12-16 22:15:14', '2025-12-16 22:15:14'),
(26, 25, 16, '2025-12-16 22:15:27', '2025-12-16 22:15:27'),
(27, 25, 2, '2025-12-16 22:15:27', '2025-12-16 22:15:27'),
(28, 26, 17, '2025-12-16 22:18:19', '2025-12-16 22:18:19'),
(29, 27, 16, '2025-12-16 22:19:13', '2025-12-16 22:19:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trazabilidad`
--

CREATE TABLE `trazabilidad` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `detalle` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trazabilidad`
--

INSERT INTO `trazabilidad` (`id`, `proyecto_id`, `user_id`, `accion`, `detalle`, `fecha`) VALUES
(2, 3, 2, 'Creó el proyecto: prueba', NULL, '2025-09-18 04:43:20'),
(3, 3, 2, 'Editó el proyecto: prueba', NULL, '2025-09-18 05:28:28'),
(4, 3, 2, 'Actualizó el proyecto: prueba 1 de edicion', NULL, '2025-09-18 06:03:42'),
(5, 3, 2, 'Actualizó el proyecto: prueba 1 de edicion2', 'Modificaciones:\n- nombre: \'prueba 1 de edicion\' → \'prueba 1 de edicion2\'\n- descripcion: \'prueba de edición 1\' → \'prueba de edición 12\'\n- fecha_inicio: \'2025-09-18\' → \'2025-09-09\'\nJustificación cambio de fechas: ediciones completas\n', '2025-09-18 06:09:14'),
(6, 4, 2, 'Creó el proyecto: ensayo', NULL, '2025-09-19 19:36:22'),
(7, 5, 2, 'Creó el proyecto: Migración de Google a Microsoft', NULL, '2025-09-19 21:17:57'),
(8, 6, 2, 'Actualizó el proyecto: ffffbbbbbzzzzc', 'Modificaciones:\n- nombre: \'ffffbbbbb\' → \'ffffbbbbbzzzzc\'\n', '2025-10-08 17:30:01'),
(9, 7, 2, 'Creó el proyecto: proyecto', NULL, '2025-10-08 17:30:36'),
(10, 7, 7, 'Actualizó el proyecto: proyecto', 'Modificaciones:\n- descripcion: \'proyecto\' → \'proyectoddd\'\n', '2025-10-08 17:35:49'),
(11, 6, 2, 'Actualizó el proyecto: ffffbbbbbzzzzc666', 'Modificaciones:\n- nombre: \'ffffbbbbbzzzzc\' → \'ffffbbbbbzzzzc666\'\n', '2025-10-09 21:06:13'),
(12, 6, 7, 'Actualizó el proyecto: ffffbbbbbzzzzc666', 'Modificaciones:\n- fecha_fin: \'2025-10-29\' → \'2025-10-30\'\nJustificación cambio de fechas: calculo de tiempo modificado\n', '2025-10-09 21:11:49'),
(13, 6, 2, 'Actualizó el proyecto: ffffbbbbbzzzzc666', 'Modificaciones:\n- descripcion: \'fffffx\' → \'fffffxsssss\'\n', '2025-10-09 21:56:32'),
(14, 6, 2, 'Canceló el proyecto: ffffbbbbbzzzzc666', NULL, '2025-10-09 22:23:39'),
(15, 7, 2, 'Canceló el proyecto: proyecto', NULL, '2025-10-10 00:52:28'),
(16, 3, 2, 'Actualizó el proyecto: prueba 1 de edicion2', 'Modificaciones:\n- descripcion: \'prueba de edición 12\' → \'prueba de edición 123\'\n', '2025-10-27 04:05:32'),
(17, 4, 2, 'Canceló el proyecto: ensayo', NULL, '2025-10-27 04:06:33'),
(18, 4, 2, 'Canceló el proyecto: ensayo', NULL, '2025-10-27 04:10:50'),
(19, 8, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-10-28 18:05:17'),
(20, 5, 2, 'Creó el proyecto: Migración de Google a Microsoft', NULL, '2025-10-28 18:06:40'),
(21, 5, 2, 'Creó el proyecto: Migración de Google a Microsoft', NULL, '2025-11-09 07:30:39'),
(22, 5, 2, 'Actualizó el proyecto: Migración de Google a Microsoft', 'Modificaciones:\n- descripcion: \'Este proyecto se lleva a cabo con el objetivo de centralizar toda nuestra información y aplicaciones en una sola plataforma, integradas bajo una misma cuenta. Entre los beneficios, destacamos la unificación de toda la paquetería de oficina, lo que nos permitirá trabajar de manera más eficiente y completamente integrada.\' → \'Este proyecto se lleva a cabo con el objetivo de centralizar toda nuestra información y aplicaciones en una sola plataforma, integradas bajo una misma cuenta. Entre los beneficios, destacamos la unificación de toda la paquetería de oficina, lo que nos permitirá trabajar de manera más eficiente y completamente integrada..\'\n', '2025-11-09 07:30:46'),
(23, 3, 2, 'Creó el proyecto: prueba 1 de edicion2', NULL, '2025-11-09 07:30:54'),
(24, 3, 2, 'Actualizó el proyecto: prueba 1 de edicion2', 'Modificaciones:\n- descripcion: \'prueba de edición 123\' → \'prueba de edición 123.\'\n', '2025-11-09 07:31:03'),
(25, 8, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-11-09 07:31:35'),
(26, 8, 2, 'Actualizó el proyecto: gestion de proyectos', 'Modificaciones:\n- descripcion: \'prueba\' → \'prueba.\'\n', '2025-11-09 07:31:39'),
(27, 8, 2, 'Canceló el proyecto: gestion de proyectos', NULL, '2025-11-09 07:31:55'),
(28, 9, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-11-19 18:47:09'),
(29, 9, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-11-19 18:47:15'),
(30, 9, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-11-19 18:47:31'),
(31, 9, 2, 'Creó el proyecto: gestion de proyectos', NULL, '2025-11-19 18:47:53'),
(32, 9, 2, 'Actualizó el proyecto: gestion de proyectos', 'Modificaciones:\n- descripcion: \'presentacion de proyectos de forma que se pueda realizar seguimiento de las tareas y cumplimiento\' → \'presentacion de proyectos de forma que se pueda realizar seguimiento de las tareas y cumplimiento y revision de tareas\'\n', '2025-11-19 18:48:08'),
(33, 5, 16, 'Creó el proyecto: Migración de Google a Microsoft', NULL, '2025-12-12 01:13:29'),
(34, 10, 2, 'Creó el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-12 01:58:06'),
(35, 10, 2, 'Creó el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-12 01:58:13'),
(36, 10, 16, 'Creó el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-12 19:32:58'),
(37, 3, 16, 'Creó el proyecto: prueba 1 de edicion2', NULL, '2025-12-13 00:13:04'),
(38, 11, 16, 'Creó el proyecto: Prueba Proyectos', NULL, '2025-12-13 01:44:08'),
(39, 11, 16, 'Canceló el proyecto: Prueba Proyectos', NULL, '2025-12-13 04:59:29'),
(40, 10, 16, 'Canceló el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-13 07:01:28'),
(43, 13, 16, 'Creó el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-13 07:02:48'),
(44, 13, 2, 'Creó el proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-15 01:12:41'),
(45, 9, 16, 'Creó el proyecto: gestion de proyectos', NULL, '2025-12-16 22:02:01'),
(46, 13, 16, 'Invitó a Pruebas colaborador al proyecto con rol colaborador', 'Con permisos específicos asignados', '2025-12-16 22:14:44'),
(47, 14, 17, 'Creó el proyecto: prueba usuario sin rol admin', NULL, '2025-12-29 06:01:17'),
(48, 5, 16, 'Invitó a Pruebas colaborador al proyecto con rol colaborador', 'Con permisos específicos asignados', '2025-12-29 06:17:05'),
(49, 5, 16, 'Invitó a Jorge jr al proyecto con rol colaborador', 'Con permisos específicos asignados', '2025-12-29 06:46:00'),
(50, 13, 16, 'Invitó a Jorge jr al proyecto con rol colaborador', NULL, '2025-12-29 06:56:05'),
(51, 13, 18, 'Aceptó la invitación al proyecto: Terminar Proyecto de Proyectos 🦅🔥', NULL, '2025-12-29 06:59:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `departamento` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `email`, `password`, `departamento`, `created_at`, `updated_at`, `estado`) VALUES
(2, 'Yeffer Cuesta', 'ycuesta@colegioprovidencia.edu.co', '0e78f0deb4948191f322583c36dad6c988313669de42d70a01a3d4648ac79ceb4f3b3446b95545b3faf714e678a5cae9f6fb6b16bb95e34da282df973712d24f', 1, '2025-08-25 02:47:21', '2025-12-14 18:32:52', 'activo'),
(7, 'Leiner Hinestroza', 'lhinestroza@colegioprovidencia.edu.co', '588345061882b82a09e74ab6002aaa78189001fdefa401e4af36ac5edc9423ac57634d7b868c35dcf931f6d2f40ea78651be7d5ee4a03aff70530ad0d3951c1f', 1, '2025-09-13 18:18:48', '2025-09-18 04:13:55', 'activo'),
(10, 'SELECT * FROM users', 'prueba1@gmail.com', 'ca3ef1c21dbddc52879b085668841571b8f0da37da2bffbc47b713a28474450e9ca4681e6b35ce872001d1d50e85ce69294c202218748e787602b477e032acb6', 12, '2025-10-07 17:26:57', '2025-10-26 22:52:46', 'activo'),
(14, 'prueba de servicio user', 'pruebauser@gmail.com', 'f544d84458f57178aa5ac7c30e6788d05e02569ee731fa7e0607e3879de76042d918265caa1740827a2075f562c15f2866bcecf62ea0194f023e90023c2fd966', 11, '2025-11-09 03:26:43', '2025-11-09 03:26:43', 'activo'),
(15, 'usuario de pruebas de roles', 'usuario_prueba@gmail.com', '78d01695043d2c2fa35561ab3f4b663aaf8332cac666f0d59124a0ace3b49f4e5f003997c7168c67a5dac2bf68a54c786d91d30763c173edda3c799b3eae4977', 1, '2025-11-09 03:33:39', '2025-11-09 03:34:42', 'activo'),
(16, 'Jorge Mulato', 'jgaleano@colegioprovidencia.edu.co', '0e78f0deb4948191f322583c36dad6c988313669de42d70a01a3d4648ac79ceb4f3b3446b95545b3faf714e678a5cae9f6fb6b16bb95e34da282df973712d24f', 1, '2025-12-11 19:46:42', '2025-12-15 12:40:04', 'activo'),
(17, 'Pruebas colaborador', 'colaborador@gmail.com', '0e78f0deb4948191f322583c36dad6c988313669de42d70a01a3d4648ac79ceb4f3b3446b95545b3faf714e678a5cae9f6fb6b16bb95e34da282df973712d24f', 1, '2025-12-14 18:53:14', '2025-12-29 00:42:08', 'activo'),
(18, 'Jorge jr', 'dg244049@gmail.com', '0e78f0deb4948191f322583c36dad6c988313669de42d70a01a3d4648ac79ceb4f3b3446b95545b3faf714e678a5cae9f6fb6b16bb95e34da282df973712d24f', 4, '2025-12-29 01:44:04', '2025-12-29 01:44:28', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_permiso`
--

CREATE TABLE `user_permiso` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `tipo` enum('allow','deny') NOT NULL DEFAULT 'allow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_permiso`
--

INSERT INTO `user_permiso` (`id`, `user_id`, `permiso_id`, `tipo`) VALUES
(38, 14, 1, 'deny'),
(39, 14, 2, 'deny'),
(40, 14, 4, 'allow'),
(41, 14, 5, 'allow'),
(42, 14, 6, 'allow'),
(43, 14, 8, 'allow'),
(44, 14, 9, 'allow'),
(45, 14, 10, 'allow'),
(46, 14, 12, 'allow'),
(56, 10, 1, 'allow'),
(57, 10, 2, 'allow'),
(58, 10, 4, 'allow'),
(59, 10, 5, 'allow'),
(60, 10, 6, 'allow'),
(61, 10, 8, 'allow'),
(62, 10, 9, 'allow'),
(63, 10, 10, 'allow'),
(64, 10, 12, 'allow'),
(81, 15, 1, 'allow'),
(82, 15, 2, 'allow'),
(83, 15, 3, 'allow'),
(84, 15, 4, 'allow'),
(85, 15, 5, 'allow'),
(86, 15, 6, 'allow'),
(87, 15, 8, 'allow'),
(88, 15, 9, 'allow'),
(89, 15, 10, 'allow'),
(90, 15, 12, 'allow'),
(91, 17, 4, 'allow'),
(92, 17, 8, 'allow'),
(93, 17, 9, 'allow'),
(94, 17, 12, 'allow'),
(95, 2, 1, 'allow'),
(96, 2, 2, 'allow'),
(97, 2, 3, 'allow'),
(98, 2, 4, 'allow'),
(99, 2, 5, 'allow'),
(100, 2, 6, 'allow'),
(101, 2, 7, 'allow'),
(102, 2, 8, 'allow'),
(103, 2, 9, 'allow'),
(104, 2, 10, 'allow'),
(105, 2, 11, 'allow'),
(106, 2, 12, 'allow'),
(107, 2, 13, 'allow'),
(108, 17, 1, 'allow'),
(109, 17, 2, 'allow'),
(110, 17, 3, 'allow'),
(111, 17, 5, 'allow'),
(112, 17, 6, 'allow'),
(113, 17, 7, 'allow'),
(114, 17, 10, 'allow'),
(115, 17, 11, 'allow'),
(116, 17, 13, 'allow'),
(117, 18, 4, 'allow'),
(118, 18, 8, 'allow'),
(119, 18, 9, 'allow'),
(120, 18, 12, 'allow');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `rol_id`) VALUES
(9, 7, 2),
(22, 10, 1),
(23, 10, 5),
(15, 14, 2),
(16, 14, 4),
(26, 16, 1),
(27, 16, 5),
(28, 17, 3),
(29, 17, 4),
(30, 18, 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarea_id` (`tarea_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_departamentos_lider` (`lider_id`);

--
-- Indices de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarea_id` (`tarea_id`);

--
-- Indices de la tabla `notificacion_config`
--
ALTER TABLE `notificacion_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`),
  ADD UNIQUE KEY `clave_unique` (`clave`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departamento_id` (`departamento_id`),
  ADD KEY `fk_proyectos_created_by` (`created_by`);

--
-- Indices de la tabla `proyecto_user_permiso`
--
ALTER TABLE `proyecto_user_permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `proj_user_perm_unique` (`proyecto_id`,`user_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `proyecto_usuario`
--
ALTER TABLE `proyecto_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_invitacion` (`token_invitacion`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rol_id` (`rol_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actividad_id` (`actividad_id`),
  ADD KEY `responsable_id` (`responsable_id`),
  ADD KEY `idx_actividad_estado` (`actividad_id`,`estado`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `tarea_usuario`
--
ALTER TABLE `tarea_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tarea_usuario_unique` (`tarea_id`,`user_id`),
  ADD KEY `tarea_id` (`tarea_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_departamento` (`departamento`);

--
-- Indices de la tabla `user_permiso`
--
ALTER TABLE `user_permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permiso_unique` (`user_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`rol_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `evidencias`
--
ALTER TABLE `evidencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `notificacion_config`
--
ALTER TABLE `notificacion_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `proyecto_user_permiso`
--
ALTER TABLE `proyecto_user_permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `proyecto_usuario`
--
ALTER TABLE `proyecto_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `tarea_usuario`
--
ALTER TABLE `tarea_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `user_permiso`
--
ALTER TABLE `user_permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `fk_departamentos_lider` FOREIGN KEY (`lider_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evidencias`
--
ALTER TABLE `evidencias`
  ADD CONSTRAINT `evidencias_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyectos_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `proyecto_user_permiso`
--
ALTER TABLE `proyecto_user_permiso`
  ADD CONSTRAINT `fk_pup_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pup_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pup_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecto_usuario`
--
ALTER TABLE `proyecto_usuario`
  ADD CONSTRAINT `proyecto_usuario_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proyecto_usuario_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tareas_ibfk_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tareas_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarea_usuario`
--
ALTER TABLE `tarea_usuario`
  ADD CONSTRAINT `tarea_usuario_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarea_usuario_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  ADD CONSTRAINT `trazabilidad_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trazabilidad_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_departamento` FOREIGN KEY (`departamento`) REFERENCES `departamentos` (`id`);

--
-- Filtros para la tabla `user_permiso`
--
ALTER TABLE `user_permiso`
  ADD CONSTRAINT `user_permiso_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permiso_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
