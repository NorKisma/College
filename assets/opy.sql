-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 17, 2026 at 09:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `opy`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `is_payable` tinyint(1) DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `graduation_date` date DEFAULT NULL,
  `graduation_reason` enum('exam_performance','attendance_performance','both') NOT NULL,
  `exam_score` decimal(6,2) DEFAULT NULL,
  `attendance_percentage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id`, `student_id`, `class_id`, `section_id`, `graduation_date`, `graduation_reason`, `exam_score`, `attendance_percentage`, `created_at`, `updated_at`) VALUES
(28, 1, 3, 1, '2026-01-21', 'exam_performance', 91.33, 50.00, '2026-01-21 14:29:32', '2026-02-02 18:25:25.000000');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_criteria`
--

CREATE TABLE `alumni_criteria` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `min_exam_score` decimal(6,2) DEFAULT 0.00,
  `min_attendance_percentage` decimal(5,2) DEFAULT 0.00,
  `require_both` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_criteria`
--

INSERT INTO `alumni_criteria` (`id`, `class_id`, `section_id`, `min_exam_score`, `min_attendance_percentage`, `require_both`, `created_at`, `updated_at`) VALUES
(7, 16, 3, 0.00, 10.00, 0, '2026-01-21 14:28:01', '2026-01-21 14:28:01'),
(8, 16, 3, 0.00, 10.00, 0, '2026-01-21 14:28:01', '2026-01-21 14:28:01'),
(9, 3, 1, 10.00, 0.00, 0, '2026-01-21 14:29:16', '2026-01-21 14:29:16'),
(10, 3, 1, 10.00, 0.00, 0, '2026-01-21 14:29:16', '2026-01-21 14:29:16');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `app_name` varchar(150) DEFAULT 'Admin Panel',
  `logo_path` varchar(255) DEFAULT NULL,
  `show_type` enum('text','logo') DEFAULT 'text',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `language_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `app_name`, `logo_path`, `show_type`, `updated_at`, `language_id`) VALUES
(1, 'Guriga Cilmiga', 'views/admin/uploads/logo/1768131077_69638a058fc1d_download-removebg-preview.png', 'text', '2026-02-02 21:29:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  `status` enum('available','in-use','damaged','disposed') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject_id` int(233) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `class_id`, `section_id`, `date`, `status`, `remarks`, `recorded_by`, `recorded_at`, `subject_id`) VALUES
(17, 2, 3, 1, '2025-12-17', 'absent', '', 'Admin Mohamed  Abdi', '2025-12-17 14:29:21', 8),
(18, 4, 3, 1, '2025-12-17', 'present', '', 'Admin Mohamed  Abdi', '2025-12-17 14:29:21', 8),
(21, 2, 3, 1, '2025-12-17', 'absent', '', 'Admin Moham', '2025-12-17 14:43:30', 9),
(22, 4, 3, 1, '2025-12-17', 'present', '', 'Admin Moham', '2025-12-17 14:43:30', 9),
(24, 3, NULL, 1, '2025-12-18', 'late', '', 'Teacher mohamed aadam', '2025-12-18 14:38:13', 8),
(25, 3, NULL, 1, '2025-12-18', 'present', '', 'Admin Mohamed  Abdi', '2025-12-18 14:43:54', 6),
(38, 5, NULL, 1, '2025-12-20', 'present', '', 'Admin Mohamed  Abdi', '2025-12-20 14:54:32', 8),
(39, 3, NULL, 1, '2025-12-20', 'excused', '', 'Admin Mohamed  Abdi', '2025-12-20 14:54:32', 8),
(43, 1, 3, 1, '2025-12-16', 'present', '', 'Teacher ID 3', '2026-01-19 13:49:26', 8),
(45, 1, 3, 1, '2025-12-24', 'absent', '', 'Admin Mohamed  Abdi', '2025-12-24 12:03:59', 9),
(46, 2, 3, 1, '2025-12-24', 'late', '', 'Admin Mohamed  Abdi', '2025-12-24 12:03:59', 9),
(47, 4, 3, 1, '2025-12-24', 'present', '', 'Admin Mohamed  Abdi', '2025-12-24 12:03:59', 9),
(48, 6, NULL, 2, '2025-12-24', 'late', 'wuu gambiyay...', 'Admin Mohamed  Abdi', '2025-12-24 12:06:10', 9),
(51, 8, 4, 1, '2026-01-18', 'present', '', 'Admin Mohamed  Abdi', '2026-01-18 16:47:20', 8),
(52, 10, 4, 1, '2026-01-18', 'present', '', 'Admin Mohamed  Abdi', '2026-01-18 16:47:20', 8),
(53, 5, 2, 1, '2026-01-18', 'present', '', 'Admin Mohamed  Abdi', '2026-01-18 16:49:15', 8),
(54, 3, 2, 1, '2026-01-18', 'late', '', 'Admin Mohamed  Abdi', '2026-01-18 16:49:15', 8),
(111, 15, 1, 1, '2025-12-22', 'present', '', 'Teacher ID 3', '2026-01-19 13:48:05', 8),
(112, 11, 1, 1, '2025-12-22', 'present', '', 'Teacher ID 3', '2026-01-19 13:48:05', 8),
(113, 13, 1, 1, '2025-12-22', 'excused', '', 'Teacher ID 3', '2026-01-19 13:48:05', 8),
(114, 6, 1, 1, '2025-12-22', 'absent', '', 'Teacher ID 3', '2026-01-19 13:48:05', 8),
(143, 11, 1, 1, '2025-12-15', 'present', '', 'Teacher ID 3', '2026-01-19 14:42:24', 8),
(144, 15, 1, 1, '2025-12-15', 'present', '', 'Teacher ID 3', '2026-01-19 14:42:24', 8),
(145, 13, 1, 1, '2025-12-15', 'late', '', 'Teacher ID 3', '2026-01-19 14:42:24', 8),
(146, 6, 1, 1, '2025-12-15', 'present', '', 'Teacher ID 3', '2026-01-19 14:42:24', 8),
(147, 11, 1, 1, '2026-01-19', 'present', '', 'Admin Mohamed  Abdi', '2026-01-19 15:04:28', 8),
(148, 15, 1, 1, '2026-01-19', 'present', '', 'Admin Mohamed  Abdi', '2026-01-19 15:04:28', 8),
(149, 13, 1, 1, '2026-01-19', 'present', '', 'Admin Mohamed  Abdi', '2026-01-19 15:04:28', 8),
(150, 6, 1, 1, '2026-01-19', 'present', '', 'Admin Mohamed  Abdi', '2026-01-19 15:04:28', 8),
(151, 11, 1, 1, '2026-01-18', 'present', '', 'Teacher ID 3', '2026-01-19 16:20:13', 8),
(152, 15, 1, 1, '2026-01-18', 'excused', '', 'Teacher ID 3', '2026-01-19 16:20:13', 8),
(153, 13, 1, 1, '2026-01-18', 'absent', '', 'Teacher ID 3', '2026-01-19 16:20:13', 8),
(154, 6, 1, 1, '2026-01-18', 'late', '', 'Teacher ID 3', '2026-01-19 16:20:13', 8),
(155, 1, 8, 1, '2026-01-22', 'present', '', 'Teacher ID 3', '2026-01-22 17:10:30', 8),
(156, 2, 8, 1, '2026-01-22', 'present', '', 'Teacher ID 3', '2026-01-22 17:10:30', 8),
(157, 7, 8, 1, '2026-01-22', 'absent', '', 'Teacher ID 3', '2026-01-22 17:10:30', 8),
(158, 4, 8, 1, '2026-01-22', 'absent', '', 'Teacher ID 3', '2026-01-22 17:10:30', 8),
(159, 15, 1, 1, '2026-02-02', 'present', '', 'Teacher ID 3', '2026-02-02 21:26:51', 8),
(160, 11, 1, 1, '2026-02-02', 'present', '', 'Teacher ID 3', '2026-02-02 21:26:51', 8),
(161, 13, 1, 1, '2026-02-02', 'present', '', 'Teacher ID 3', '2026-02-02 21:26:51', 8),
(162, 6, 1, 1, '2026-02-02', 'present', '', 'Teacher ID 3', '2026-02-02 21:26:51', 8),
(163, 16, 9, 3, '2026-02-15', 'absent', '', 'Admin mohamed Abdi wahid', '2026-02-15 06:16:58', 8);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_history`
--

CREATE TABLE `attendance_history` (
  `id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `old_status` varchar(20) NOT NULL,
  `new_status` varchar(20) NOT NULL,
  `recorded_by` varchar(100) NOT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_history`
--

INSERT INTO `attendance_history` (`id`, `attendance_id`, `student_id`, `old_status`, `new_status`, `recorded_by`, `changed_at`) VALUES
(1, 24, 3, 'present', 'late', 'Admin', '2025-12-18 20:13:13'),
(2, 24, 3, 'late', 'excused', 'Admin', '2025-12-18 20:16:09'),
(3, 24, 3, 'excused', 'present', 'Admin', '2025-12-18 20:20:41'),
(4, 24, 3, 'present', 'absent', 'Admin', '2025-12-18 20:20:55'),
(5, 24, 3, 'absent', 'late', 'Admin Mohamed  Abdi', '2025-12-18 21:07:41'),
(6, 17, 2, 'present', 'absent', 'Admin Mohamed  Abdi', '2025-12-18 22:06:00'),
(7, 18, 4, 'present', 'present', 'Admin Mohamed  Abdi', '2025-12-18 22:06:00'),
(8, 38, 5, 'present', 'present', 'Admin Mohamed  Abdi', '2025-12-22 17:34:46'),
(9, 39, 3, 'present', 'excused', 'Admin Mohamed  Abdi', '2025-12-22 17:34:46'),
(10, 40, 1, 'present', 'absent', 'Admin Mohamed  Abdi', '2025-12-22 19:26:21'),
(11, 41, 2, 'absent', 'absent', 'Admin Mohamed  Abdi', '2025-12-22 19:26:21'),
(12, 42, 4, 'present', 'absent', 'Admin Mohamed  Abdi', '2025-12-22 19:26:21'),
(13, 40, 1, 'absent', 'present', 'Admin Mohamed  Abdi', '2025-12-22 19:27:10'),
(14, 41, 2, 'absent', 'present', 'Admin Mohamed  Abdi', '2025-12-22 19:27:10'),
(15, 42, 4, 'absent', 'present', 'Admin Mohamed  Abdi', '2025-12-22 19:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 1, 'SUBJECT_CREATE', 'Created subject: Hadith (ID: 2)', '2025-12-07 18:43:21'),
(2, 1, 'SUBJECT_UPDATE', 'Updated subject: Hadith (ID: 2)', '2025-12-07 19:00:43'),
(3, 1, 'SUBJECT_DELETE', 'Deleted subject ID: 1', '2025-12-07 19:00:47'),
(4, 1, 'SUBJECT_CREATE', 'Created subject: Tafsiir (ID: 3)', '2025-12-09 09:59:25'),
(5, 1, 'SUBJECT_CREATE', 'Created subject: Math (ID: 5)', '2025-12-13 10:43:54'),
(6, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject 5 to class 1 with teacher 3', '2025-12-13 11:31:27'),
(7, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject 5 to class 2 with teacher 2', '2025-12-13 11:31:37'),
(8, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 11', '2025-12-13 11:32:59'),
(9, 1, 'SUBJECT_UPDATE', 'Updated subject: Math (ID: 5)', '2025-12-13 11:36:23'),
(10, 1, 'SUBJECT_CREATE', 'Created subject: Tarbiyo (ID: 6)', '2025-12-13 11:42:22'),
(11, 1, 'SUBJECT_CREATE', 'Created subject: xisaa (ID: 7)', '2025-12-13 11:51:15'),
(12, 1, 'SUBJECT_CREATE', 'Created subject: Tafsiir (ID: 1)', '2025-12-13 12:06:19'),
(13, 1, 'SUBJECT_CREATE', 'Created subject: Tafsiir (ID: 2)', '2025-12-13 12:25:50'),
(14, 1, 'SUBJECT_CREATE', 'Created subject: Tafsiir (ID: 3)', '2025-12-13 12:26:37'),
(15, 1, 'SUBJECT_CREATE', 'Created subject: Math (ID: 4)', '2025-12-13 12:28:03'),
(16, 1, 'SUBJECT_DELETE', 'Deleted subject ID: 4', '2025-12-13 12:33:53'),
(17, 1, 'SUBJECT_DELETE', 'Deleted subject ID: 3', '2025-12-13 12:33:55'),
(18, 1, 'SUBJECT_CREATE', 'Created subject: Math (ID: 5)', '2025-12-13 12:34:07'),
(19, 1, 'SUBJECT_DELETE', 'Deleted subject ID: 5', '2025-12-13 14:48:24'),
(20, 1, 'SUBJECT_CREATE', 'Created subject: Tafsiir (ID: 6)', '2025-12-13 14:48:34'),
(21, 1, 'SUBJECT_CREATE', 'Created subject: Tarbiyo (ID: 7)', '2025-12-13 15:05:32'),
(22, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject 7 to class 2 with teacher 3', '2025-12-14 16:58:22'),
(23, 1, 'SUBJECT_DELETE', 'Deleted subject ID 7 and its assignments', '2025-12-14 17:06:29'),
(24, 1, 'SUBJECT_CREATE', 'Created subject: Math (ID: 8)', '2025-12-14 18:14:53'),
(25, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject 8 to class 1 with teacher 3', '2025-12-14 18:15:26'),
(26, 1, 'SUBJECT_UPDATE', 'Updated subject: Math (ID: 8)', '2025-12-14 18:46:02'),
(27, 1, 'SUBJECT_UPDATE', 'Updated subject & schedule (Subject ID: 8, Assignment ID: 0)', '2025-12-14 18:49:11'),
(28, 1, 'SUBJECT_UPDATE', 'Updated subject & schedule (Subject ID: 6, Assignment ID: 0)', '2025-12-14 18:51:06'),
(29, 1, 'SUBJECT_UPDATE', 'Updated subject & schedule (Subject ID: 8, Assignment ID: 16)', '2025-12-14 18:53:23'),
(30, 1, 'SUBJECT_UPDATE', 'Updated subject & schedule (Subject ID: 8, Assignment ID: 16)', '2025-12-14 19:07:22'),
(31, 1, 'USER_CREATE', 'Created user: JohnDoe (ID: 6)', '2025-12-14 20:45:26'),
(32, 1, 'USER_CREATE', 'Created user: Ak (ID: 7)', '2025-12-14 20:51:18'),
(33, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 16', '2025-12-15 10:08:18'),
(34, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject 8 to class 1 with teacher 3', '2025-12-15 10:08:48'),
(35, 1, 'SUBJECT_CREATE', 'Created subject: Mathematics (ID: 9)', '2025-12-15 15:11:15'),
(36, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject Mathematics to class Form-3 with teacher mohamed aadam', '2025-12-15 15:12:18'),
(37, 1, 'SUBJECT_UPDATE', 'Updated subject Mathematics & schedule (Class: Form-3, Teacher: mohamed aadam)', '2025-12-15 15:15:07'),
(38, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject Mathematics to class Form-1 with teacher jaamac farxaan', '2025-12-16 11:20:47'),
(39, 1, 'ATTENDANCE_ADD', 'Admin marked attendance for class ID 1 on 2025-12-16', '2025-12-16 19:38:47'),
(40, 1, 'ATTENDANCE_ADD', 'Admin marked attendance for class ID 3 on 2025-12-16', '2025-12-16 20:16:57'),
(41, 1, 'ATTENDANCE_ADD', 'Admin marked attendance for class ID 2 on 2025-12-16', '2025-12-16 20:20:37'),
(42, 1, 'ATTENDANCE_DELETE', 'Deleted attendance session: Date=2025-12-20, ClassID=3, SubjectID=8, RecordedBy=Admin Mohamed  Abdi, RowsDeleted=3', '2025-12-20 17:18:32'),
(43, 1, 'ATTENDANCE_DELETE', 'Deleted attendance session: Date=2025-12-20, ClassID=2, SubjectID=8, RecordedBy=Admin Mohamed  Abdi, RowsDeleted=0', '2025-12-20 17:25:15'),
(44, 1, 'ATTENDANCE_DELETE', 'Deleted attendance session: Date=2025-12-20, ClassID=2, SubjectID=8, RecordedBy=Admin Mohamed  Abdi, RowsDeleted=0', '2025-12-20 17:25:20'),
(45, 1, 'ATTENDANCE_DELETE', 'Deleted attendance session for class Form-2, subject Tafsiir by Admin Mohamed  Abdi on 2025-12-20', '2025-12-20 17:41:16'),
(46, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-2, subject Math by Admin Mohamed  Abdi on 2025-12-20', '2025-12-20 17:54:32'),
(47, 1, 'SUBJECT_UPDATE', 'Updated subject Mathematics & schedule (Class: Form-1, Teacher: jaamac farxaan)', '2025-12-20 18:36:44'),
(48, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 23', '2025-12-20 19:12:33'),
(49, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 24', '2025-12-20 19:32:44'),
(50, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 22', '2025-12-20 19:32:51'),
(51, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 26', '2025-12-20 19:32:58'),
(52, 1, 'SUBJECT_UPDATE', 'Updated subject Math & schedule (Class: Form-1, Teacher: jaamac farxaan)', '2025-12-20 19:33:34'),
(53, 1, 'SUBJECT_UPDATE', 'Updated subject Tafsiir & schedule (Class: Form-1, Teacher: Ahmed Hassan)', '2025-12-20 19:33:51'),
(54, 1, 'SUBJECT_UPDATE', 'Updated subject Tafsiir & schedule (Class: Form-1, Teacher: Ahmed Hassan)', '2025-12-20 19:48:49'),
(55, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 29', '2025-12-20 20:13:26'),
(56, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 18', '2025-12-21 10:47:05'),
(57, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-3, subject Math by Admin Mohamed  Abdi on 2025-12-22', '2025-12-22 19:02:26'),
(58, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-3, subject Mathematics by Admin Mohamed  Abdi on 2025-12-24', '2025-12-24 15:03:59'),
(59, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-1, subject Mathematics by Admin Mohamed  Abdi on 2025-12-24', '2025-12-24 15:06:10'),
(60, 1, 'USER_CREATE', 'Created user: hanad (ID: 8)', '2026-01-07 18:32:33'),
(61, 1, 'USER_DELETE', 'Deleted user: hanad (ID: 8)', '2026-01-16 12:56:55'),
(62, 1, 'USER_DELETE', 'Deleted user: Ak (ID: 7)', '2026-01-16 12:56:58'),
(63, 1, 'USER_DELETE', 'Deleted user: JohnDoe (ID: 6)', '2026-01-16 12:57:01'),
(64, 1, 'USER_DELETE', 'Deleted user: admin (ID: 5)', '2026-01-16 12:57:03'),
(65, 1, 'USER_CREATE', 'Created user: hanad (ID: 9)', '2026-01-16 12:58:08'),
(66, 1, 'USER_CREATE', 'Created user: haand2 (ID: 12)', '2026-01-16 13:00:27'),
(67, 1, 'SUBJECT_DELETE', 'Deleted subject ID 8 and its assignments', '2026-01-17 16:08:25'),
(68, 1, 'SUBJECT_DELETE', 'Deleted subject ID 8 and its assignments', '2026-01-17 16:08:34'),
(69, 1, 'SUBJECT_DELETE', 'Deleted subject ID 8 and its assignments', '2026-01-17 16:08:56'),
(70, 1, 'SUBJECT_CREATE', 'Created subject: ioio (ID: 10)', '2026-01-17 16:09:23'),
(71, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject ioio to class Form-2 with teacher alim oska', '2026-01-17 16:10:08'),
(72, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 and its assignments', '2026-01-17 16:10:21'),
(73, 1, 'SUBJECT_DELETE', 'Deleted subject ID 8 and its assignments', '2026-01-17 16:11:04'),
(74, 1, 'SUBJECT_DELETE', 'Deleted subject ID 8 and its assignments', '2026-01-17 16:11:09'),
(75, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 and its assignments', '2026-01-17 16:11:15'),
(76, 1, 'SUBJECT_ASSIGN_CLASS', 'Assigned subject ioio to class Form-2 with teacher alim oska', '2026-01-17 16:17:15'),
(77, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 and its assignments', '2026-01-17 16:17:32'),
(78, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 (assignments not deleted)', '2026-01-17 16:39:44'),
(79, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 (assignments not deleted)', '2026-01-17 16:39:51'),
(80, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 (assignments not deleted)', '2026-01-17 16:40:15'),
(81, 1, 'SUBJECT_DELETE', 'Deleted subject ID 10 (assignments not deleted)', '2026-01-17 17:00:05'),
(82, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 38', '2026-01-17 17:54:11'),
(83, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-4, subject Math by Admin Mohamed  Abdi on 2026-01-18', '2026-01-18 19:42:30'),
(84, 1, 'ATTENDANCE_DELETE', 'Deleted attendance session for class Form-4, subject Math by Admin Mohamed  Abdi on 2026-01-18', '2026-01-18 19:43:41'),
(85, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-4, subject Math by Admin Mohamed  Abdi on 2026-01-18', '2026-01-18 19:47:20'),
(86, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-2, subject Math by Admin Mohamed  Abdi on 2026-01-18', '2026-01-18 19:49:15'),
(87, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 21', '2026-01-19 14:45:20'),
(88, 1, 'SUBJECT_REMOVE_ASSIGNMENT', 'Removed class assignment ID: 42', '2026-01-19 15:02:32'),
(89, 1, 'ATTENDANCE_SAVE', 'Saved attendance for class Form-1, subject Math by Admin Mohamed  Abdi on 2026-01-19', '2026-01-19 18:04:28'),
(90, 1, 'USER_CREATE', 'Created user: John (ID: 13)', '2026-01-21 18:19:14'),
(91, 1, 'EXPENSE_UPDATE', 'Updated expense ID: 3 - tkj3thuisss (Amount: $40, Category: Supplies)', '2026-01-25 16:19:11'),
(92, 1, 'EXPENSE_CREATE', 'Created expense: tyyy (Amount: $45, Category: hgg)', '2026-01-25 16:19:44'),
(93, 1, 'USER_DELETE', 'Deleted user: John (ID: 13)', '2026-02-02 21:20:33'),
(94, 3, 'ATTENDANCE_SAVE', 'Saved attendance for class class waynel, subject Math by Admin mohamed Abdi wahid on 2026-02-15', '2026-02-15 09:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `section_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `created_at`, `section_id`) VALUES
(1, 'Form-1', '2025-12-09 08:06:16', 1),
(2, 'Form-2', '2025-12-09 08:06:16', 1),
(3, 'Form-31', '2025-12-09 16:06:08', 1),
(4, 'Form-4', '2025-12-20 15:00:36', 1),
(8, 'Form-3', '2026-01-21 11:47:05', 1),
(9, 'class waynel', '2026-01-21 12:17:25', 3),
(16, 'class waynelk', '2026-01-21 12:52:29', 3);

-- --------------------------------------------------------

--
-- Table structure for table `class_fees`
--

CREATE TABLE `class_fees` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `fee_category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_fees`
--

INSERT INTO `class_fees` (`id`, `class_id`, `fee_category_id`, `amount`, `month`, `year`, `due_date`, `created_at`) VALUES
(18, 3, 1, 15.00, 1, '2026', NULL, '2026-01-14 14:40:29'),
(19, 2, 1, 15.00, 1, '2026', NULL, '2026-01-14 15:05:35'),
(20, 2, 1, 15.00, 12, '2025', NULL, '2026-01-14 15:13:52'),
(21, 1, 1, 20.00, 1, '2026', NULL, '2026-01-17 11:24:28'),
(22, 6, 1, 15.00, 1, '2026', NULL, '2026-01-17 11:44:52'),
(23, 6, 1, 15.00, 11, '2025', NULL, '2026-01-17 11:46:05'),
(24, 7, 1, 6.00, 1, '2026', NULL, '2026-01-17 12:43:07'),
(25, 3, 1, 20.00, 12, '2025', NULL, '2026-01-18 09:59:47'),
(26, 8, 1, 20.00, 11, '2025', NULL, '2026-01-25 15:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `class_notes`
--

CREATE TABLE `class_notes` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_notes`
--

INSERT INTO `class_notes` (`id`, `class_id`, `teacher_id`, `note`, `created_at`) VALUES
(4, 1, 3, 'fheriuhe rufgsfdsufyg', '2026-02-03 17:23:40');

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

CREATE TABLE `class_students` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL DEFAULT 1,
  `academic_year` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_students`
--

INSERT INTO `class_students` (`id`, `class_id`, `student_id`, `batch_id`, `academic_year`, `created_at`, `is_active`) VALUES
(1, 3, 1, 1, '2025/2026', '2025-11-13 11:48:23', 0),
(2, 3, 2, 1, '2025/2026', '2025-12-13 11:46:36', 0),
(3, 2, 3, 1, '2025/2026', '2025-12-13 14:44:36', 1),
(4, 3, 4, 1, '2025/2026', '2025-12-16 15:56:24', 0),
(5, 2, 5, 1, '2025/2026', '2025-12-20 09:57:18', 1),
(6, 1, 6, 1, '2025/2026', '2025-12-22 16:16:48', 1),
(7, 3, 7, 1, '2025/2026', '2025-12-27 19:41:29', 0),
(8, 4, 8, 1, '2025/2026', '2025-12-28 13:53:54', 1),
(18, 4, 10, 1, '2026/2027', '2026-01-12 12:37:57', 1),
(19, 1, 11, 1, '2026/2027', '2026-01-14 14:20:17', 1),
(21, 1, 13, 1, '2026/2027', '2026-01-17 11:18:55', 1),
(24, 1, 15, 1, '2026/2027', '2026-01-18 09:53:48', 1),
(33, 8, 1, 1, '2025/2026', '2026-01-21 11:47:05', 1),
(34, 8, 2, 1, '2025/2026', '2026-01-21 11:47:05', 1),
(35, 8, 4, 1, '2025/2026', '2026-01-21 11:47:05', 1),
(36, 8, 7, 1, '2025/2026', '2026-01-21 11:47:05', 1),
(40, 9, 16, 1, '2026/2027', '2026-01-21 12:18:17', 0),
(46, 16, 16, 1, '2026/2027', '2026-01-21 12:52:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_subjects`
--

INSERT INTO `class_subjects` (`id`, `class_id`, `subject_id`, `day_of_week`, `start_time`, `end_time`, `created_at`, `teacher_id`) VALUES
(12, 1, 6, 'Monday', '07:30:00', '08:20:00', '2025-12-13 11:50:20', 1),
(13, 2, 6, 'Monday', '06:00:00', '09:00:00', '2025-12-13 11:50:38', 3),
(14, 3, 6, 'Monday', '08:00:00', '11:00:00', '2025-12-13 11:50:49', 2),
(20, 3, 9, 'Wednesday', '17:15:00', '18:31:00', '2025-12-15 12:12:18', 2),
(28, 4, 9, 'Monday', '00:00:00', '00:00:00', '2025-12-20 16:35:29', 3),
(39, 3, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-17 14:56:54', 6),
(40, 4, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-18 13:48:56', 3),
(41, 2, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-18 14:37:20', 3),
(43, 1, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-19 12:03:09', 3),
(44, 1, 9, 'Monday', '00:00:00', '00:00:00', '2026-01-19 12:28:08', 3),
(45, 9, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-21 12:22:19', 1),
(46, 8, 8, 'Monday', '00:00:00', '00:00:00', '2026-01-22 17:06:39', 3),
(47, 8, 9, 'Monday', '00:00:00', '00:00:00', '2026-01-23 15:53:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_subject_schedule`
--

CREATE TABLE `class_subject_schedule` (
  `id` int(11) NOT NULL,
  `class_subject_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_subject_schedule`
--

INSERT INTO `class_subject_schedule` (`id`, `class_subject_id`, `day_of_week`, `start_time`, `end_time`, `created_at`) VALUES
(5, 28, 'Monday', '07:35:00', '08:35:00', '2025-12-20 16:35:29'),
(6, 28, 'Tuesday', '07:35:00', '09:40:00', '2025-12-20 16:35:29'),
(12, 13, 'Monday', '09:10:00', '10:10:00', '2025-12-21 07:08:42'),
(20, 14, 'Monday', '16:30:00', '17:40:00', '2025-12-22 11:46:29'),
(36, 39, 'Monday', '07:50:00', '08:50:00', '2026-01-17 14:56:54'),
(38, 40, 'Sunday', '13:50:00', '14:50:00', '2026-01-18 13:49:25'),
(40, 41, 'Sunday', '17:00:00', '19:35:00', '2026-01-18 15:49:03'),
(44, 44, 'Monday', '18:27:00', '19:28:00', '2026-01-19 12:28:08'),
(45, 43, 'Monday', '16:00:00', '17:03:00', '2026-01-19 12:28:17'),
(46, 45, 'Friday', '17:22:00', '18:22:00', '2026-01-21 12:22:19'),
(47, 46, 'Thursday', '20:00:00', '21:05:00', '2026-01-22 17:06:39'),
(48, 47, 'Friday', '18:45:00', '19:50:00', '2026-01-23 15:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `class_teachers`
--

CREATE TABLE `class_teachers` (
  `id` int(11) NOT NULL,
  `teacher_user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_students`
--

CREATE TABLE `department_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `roll_no` varchar(50) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `status` enum('enrolled','completed','dropped','transferred') DEFAULT 'enrolled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `exam_type` varchar(100) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `academic_year` varchar(9) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `section_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `exam_type`, `class_id`, `academic_year`, `is_deleted`, `section_id`, `start_date`, `end_date`, `created_at`, `status`) VALUES
(13, 'Midterm Exam', 'midterm', NULL, '', 0, 2, '2026-01-06', NULL, '2026-01-06 10:42:00', 'active'),
(14, 'Midterm Exam', 'midterm', NULL, '', 0, 1, '2026-01-06', NULL, '2026-01-06 10:44:24', 'active'),
(15, 'Midterm Exam', 'midterm', 3, '2025-2026', 0, 1, '2026-01-07', NULL, '2026-01-07 12:30:34', 'active'),
(16, 'Midterm Exam', 'midterm', NULL, '2025-2026', 0, 3, '2026-01-17', NULL, '2026-01-17 13:01:30', 'active'),
(24, 'final', 'Final', 1, '2026-2027', 0, 1, '2026-01-01', '2026-01-15', '2026-01-20 14:54:36', 'active'),
(25, 'miderm', 'Midterm', 4, '2026-2027', 0, 1, '2026-01-13', '2026-01-22', '2026-01-20 15:36:10', 'active'),
(28, 'Quiz (2026-2027)', 'Quiz', 1, '2026-2027', 0, 1, '2026-01-21', '2026-01-29', '2026-01-21 08:51:33', 'active'),
(29, 'Final Exam', 'final', 8, '2025-2026', 0, 1, '2026-01-23', NULL, '2026-01-23 13:23:30', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `obtained_marks` decimal(6,2) DEFAULT NULL,
  `status` enum('present','absent') DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `exam_id`, `student_id`, `class_id`, `section_id`, `subject_id`, `obtained_marks`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 14, 6, 1, 1, 8, 57.00, 'present', '', '2026-01-06 12:33:43', '2026-01-07 11:31:42'),
(2, 13, 9, 5, 2, 8, 90.00, 'present', '', '2026-01-06 12:37:08', '2026-01-06 12:37:09'),
(3, 14, 6, 1, 1, 9, 89.00, 'present', NULL, '2026-01-06 13:05:04', '2026-01-06 13:27:07'),
(4, 14, 6, 1, 1, 6, 0.00, 'absent', NULL, '2026-01-06 13:06:51', '2026-01-06 13:27:44'),
(5, 15, 1, 3, 1, 8, 98.00, 'present', NULL, '2026-01-12 11:05:05', '2026-01-12 11:05:05'),
(6, 15, 1, 3, 1, 9, 87.00, 'present', NULL, '2026-01-12 11:05:05', '2026-01-12 11:05:05'),
(7, 15, 1, 3, 1, 6, 89.00, 'present', NULL, '2026-01-12 11:05:05', '2026-01-12 11:05:05'),
(8, 24, 15, 1, 1, 8, 100.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(9, 24, 15, 1, 1, 9, 98.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(10, 24, 11, 1, 1, 8, 89.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(11, 24, 11, 1, 1, 9, 88.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(12, 24, 13, 1, 1, 8, 87.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(13, 24, 13, 1, 1, 9, 86.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(14, 24, 6, 1, 1, 8, 85.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(15, 24, 6, 1, 1, 9, 85.00, 'present', NULL, '2026-01-20 15:13:51', '2026-01-20 15:13:51'),
(16, 25, 8, 4, 1, 8, NULL, 'absent', NULL, '2026-01-20 15:44:27', '2026-01-20 16:16:19'),
(17, 25, 8, 4, 1, 9, 77.00, 'present', NULL, '2026-01-20 15:44:27', '2026-01-20 16:16:19'),
(18, 25, 10, 4, 1, 8, NULL, 'present', NULL, '2026-01-20 15:44:27', '2026-01-20 16:16:19'),
(19, 25, 10, 4, 1, 9, 70.00, 'present', NULL, '2026-01-20 15:44:27', '2026-01-20 16:16:19'),
(20, 29, 1, 8, 1, 8, 89.00, 'present', NULL, '2026-01-23 13:24:33', '2026-01-23 15:55:33'),
(21, 29, 2, 8, 1, 8, 77.00, 'present', NULL, '2026-01-23 13:24:33', '2026-01-23 15:55:33'),
(22, 29, 7, 8, 1, 8, 90.00, 'present', NULL, '2026-01-23 13:24:33', '2026-01-23 15:55:33'),
(23, 29, 4, 8, 1, 8, 86.00, 'present', NULL, '2026-01-23 13:24:33', '2026-01-23 15:55:33'),
(24, 29, 1, 8, 1, 9, 80.00, 'present', NULL, '2026-01-23 15:55:19', '2026-01-23 15:55:33'),
(25, 29, 2, 8, 1, 9, 88.00, 'present', NULL, '2026-01-23 15:55:19', '2026-01-23 15:55:33'),
(26, 29, 7, 8, 1, 9, 89.00, 'present', NULL, '2026-01-23 15:55:19', '2026-01-23 15:55:33'),
(27, 29, 4, 8, 1, 9, 90.00, 'present', NULL, '2026-01-23 15:55:19', '2026-01-23 15:55:33'),
(28, 28, 15, 1, 1, 8, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(29, 28, 15, 1, 1, 9, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(30, 28, 11, 1, 1, 8, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(31, 28, 11, 1, 1, 9, 99.00, 'present', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(32, 28, 13, 1, 1, 8, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(33, 28, 13, 1, 1, 9, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(34, 28, 6, 1, 1, 8, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44'),
(35, 28, 6, 1, 1, 9, NULL, 'absent', NULL, '2026-02-04 04:15:44', '2026-02-04 04:15:44');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subjects`
--

CREATE TABLE `exam_subjects` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `max_marks` decimal(6,2) NOT NULL,
  `pass_marks` decimal(6,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_subjects`
--

INSERT INTO `exam_subjects` (`id`, `exam_id`, `subject_id`, `max_marks`, `pass_marks`, `created_at`) VALUES
(1, 7, 8, 100.00, 60.00, '2026-01-06 10:26:12'),
(2, 8, 8, 100.00, 60.00, '2026-01-06 10:26:50'),
(3, 9, 6, 100.00, 60.00, '2026-01-06 10:27:21'),
(4, 9, 8, 100.00, 60.00, '2026-01-06 10:27:21'),
(5, 9, 9, 100.00, 60.00, '2026-01-06 10:27:21'),
(6, 10, 6, 100.00, 60.00, '2026-01-06 10:31:13'),
(7, 10, 8, 100.00, 60.00, '2026-01-06 10:31:13'),
(8, 10, 9, 100.00, 60.00, '2026-01-06 10:31:13'),
(9, 12, 6, 100.00, 60.00, '2026-01-06 10:38:55'),
(10, 12, 8, 100.00, 60.00, '2026-01-06 10:38:55'),
(11, 12, 9, 100.00, 60.00, '2026-01-06 10:38:55'),
(12, 13, 8, 100.00, 50.00, '2026-01-06 10:42:00'),
(13, 14, 6, 100.00, 50.00, '2026-01-06 10:44:24'),
(14, 14, 8, 100.00, 50.00, '2026-01-06 10:44:24'),
(15, 14, 9, 100.00, 50.00, '2026-01-06 10:44:24'),
(16, 15, 6, 100.00, 50.00, '2026-01-07 12:30:34'),
(17, 15, 8, 100.00, 50.00, '2026-01-07 12:30:34'),
(18, 15, 9, 100.00, 50.00, '2026-01-07 12:30:34'),
(19, 16, 8, 100.00, 60.00, '2026-01-17 13:01:30'),
(20, 17, 8, 10.00, 70.00, '2026-01-20 12:32:47'),
(21, 17, 9, 100.00, 70.00, '2026-01-20 12:32:47'),
(22, 18, 8, 100.00, 70.00, '2026-01-20 12:58:00'),
(23, 19, 8, 100.00, 70.00, '2026-01-20 13:37:30'),
(24, 20, 8, 100.00, 70.00, '2026-01-20 13:43:41'),
(25, 21, 8, 100.00, 70.00, '2026-01-20 13:45:37'),
(26, 21, 9, 100.00, 70.00, '2026-01-20 13:45:37'),
(27, 22, 8, 100.00, 70.00, '2026-01-20 14:00:28'),
(28, 22, 9, 100.00, 70.00, '2026-01-20 14:11:57'),
(29, 23, 8, 100.00, 70.00, '2026-01-20 14:27:47'),
(30, 23, 9, 100.00, 70.00, '2026-01-20 14:27:47'),
(31, 24, 8, 100.00, 60.00, '2026-01-20 14:54:36'),
(32, 24, 9, 100.00, 60.00, '2026-01-20 14:54:36'),
(33, 25, 8, 100.00, 70.00, '2026-01-20 15:36:10'),
(34, 25, 9, 100.00, 70.00, '2026-01-20 15:36:10'),
(35, 26, 8, 60.00, 20.00, '2026-01-20 17:09:46'),
(36, 26, 9, 60.00, 20.00, '2026-01-20 17:09:46'),
(38, 28, 8, 100.00, 70.00, '2026-01-21 08:51:33'),
(39, 28, 9, 100.00, 70.00, '2026-01-21 08:51:33'),
(40, 29, 8, 100.00, 70.00, '2026-01-23 13:23:30'),
(41, 29, 9, 100.00, 70.00, '2026-01-23 15:54:51');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'Other',
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recorded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `title`, `category`, `amount`, `expense_date`, `created_at`, `recorded_by`) VALUES
(4, 'tyyy', 'hgg', 45.00, '2026-01-25', '2026-01-25 13:19:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `fee_category_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_categories`
--

CREATE TABLE `fee_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_categories`
--

INSERT INTO `fee_categories` (`id`, `name`, `description`) VALUES
(1, 'Fee salary', 'the money paid by every month on every student.'),
(4, 'exam moneys', 'mmoo');

-- --------------------------------------------------------

--
-- Table structure for table `hostel_allocations`
--

CREATE TABLE `hostel_allocations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `bed_no` varchar(20) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `status` enum('active','vacated') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostel_blocks`
--

CREATE TABLE `hostel_blocks` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostel_rooms`
--

CREATE TABLE `hostel_rooms` (
  `id` int(11) NOT NULL,
  `block_id` int(11) DEFAULT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT 1,
  `available_beds` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `status` enum('unpaid','partial','paid','overdue') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `is_active`) VALUES
(1, '1', 'English', 1),
(2, '2', 'Arabic', 1),
(3, '3', 'Somali', 0);

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library_books`
--

CREATE TABLE `library_books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `isbn` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `publisher` varchar(200) DEFAULT NULL,
  `year_published` year(4) DEFAULT NULL,
  `copies_total` int(11) DEFAULT 1,
  `copies_available` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library_issues`
--

CREATE TABLE `library_issues` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_slideshow`
--

CREATE TABLE `login_slideshow` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `position` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_slideshow`
--

INSERT INTO `login_slideshow` (`id`, `image_path`, `position`, `is_active`, `created_at`) VALUES
(2, 'views/admin/uploads/slideshow/1768121619_59-592011_-hd-png-download.png', 0, 1, '2026-01-11 08:53:39'),
(4, 'views/admin/uploads/slideshow/1768123072_1.jpg', 4, 1, '2026-01-11 09:17:52'),
(5, 'views/admin/uploads/slideshow/1768123072_2.jpg', 2, 1, '2026-01-11 09:17:52'),
(6, 'views/admin/uploads/slideshow/1768123072_2.webp', 3, 1, '2026-01-11 09:17:52'),
(7, 'views/admin/uploads/slideshow/1768123072_3.jpg', 1, 1, '2026-01-11 09:17:52'),
(13, 'views/admin/uploads/slideshow/1768126016_696376400ef03_4559-pills-1296x728-header.webp', 5, 1, '2026-01-11 10:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `marks_obtained` decimal(6,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('present','absent') DEFAULT 'present',
  `percentage` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('admin') DEFAULT 'admin',
  `message_group_id` int(11) NOT NULL,
  `recipient_type` enum('teacher','parent','both') NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `message_title` varchar(255) NOT NULL,
  `message_body` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `sender_type`, `message_group_id`, `recipient_type`, `recipient_id`, `message_title`, `message_body`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(32, 1, 'admin', 1768468929, 'teacher', 1, 'Key fixes made', 'EJFHERFH', 0, NULL, '2026-01-15 09:22:09', NULL),
(33, 1, 'admin', 1768468929, 'teacher', 3, 'Key fixes made', 'EJFHERFH', 1, '2026-02-02 21:14:25', '2026-01-15 09:22:09', '2026-02-02 21:14:25'),
(34, 1, 'admin', 1768468929, 'teacher', 2, 'Key fixes made', 'EJFHERFH', 0, NULL, '2026-01-15 09:22:09', NULL),
(36, 1, 'admin', 1768565730, 'parent', 3, 'ioio', 'khjkhij', 1, '2026-01-16 16:17:27', '2026-01-16 12:15:30', '2026-01-16 16:17:27'),
(37, 1, 'admin', 1768565730, 'parent', 7, 'ioio', 'khjkhij', 1, '2026-01-16 12:15:44', '2026-01-16 12:15:30', '2026-01-16 12:15:44'),
(38, 1, 'admin', 1768582745, 'parent', 3, 'tyeruityeiru', 'ueyru teruiebyerhkngj ghrt8r7t894 erijgr', 1, '2026-01-21 17:19:34', '2026-01-16 16:59:05', '2026-01-21 17:19:34'),
(39, 1, 'admin', 1768582745, 'parent', 7, 'tyeruityeiru', 'ueyru teruiebyerhkngj ghrt8r7t894 erijgr', 1, '2026-01-16 16:59:46', '2026-01-16 16:59:05', '2026-01-16 16:59:46'),
(40, 1, 'admin', 1768582745, 'parent', 8, 'tyeruityeiru', 'ueyru teruiebyerhkngj ghrt8r7t894 erijgr', 0, NULL, '2026-01-16 16:59:05', NULL),
(41, 1, 'admin', 1768986241, 'teacher', 1, 'Imtixaanka kama danbeyskalllluiyuiyui:', 'waa idin salaamay', 0, NULL, '2026-01-21 09:04:01', NULL),
(42, 1, 'admin', 1768986241, 'teacher', 3, 'Imtixaanka kama danbeyskalllluiyuiyui:', 'waa idin salaamay', 1, '2026-02-02 21:14:24', '2026-01-21 09:04:01', '2026-02-02 21:14:24'),
(43, 1, 'admin', 1768987753, 'teacher', 1, 'ioioi', 'jgbyufyiu', 0, NULL, '2026-01-21 09:29:13', NULL),
(44, 1, 'admin', 1768987753, 'teacher', 3, 'ioioi', 'jgbyufyiu', 1, '2026-02-02 21:14:22', '2026-01-21 09:29:13', '2026-02-02 21:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `login_pin` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `name`, `phone`, `email`, `address`, `occupation`, `login_pin`, `created_at`) VALUES
(3, 'bashiir', '0612323456', 'ja@gmail.com', '', 'engineer', '8877', '2026-01-16 10:23:50'),
(7, 'i', '1234512345', 'j@gmai.com', '', '', '9898', '2026-01-16 10:30:58'),
(8, 'liban', '0612323400', 'hanad27@gmail.com', '', 'dekada', '7777', '2026-01-16 16:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `month` varchar(20) DEFAULT NULL,
  `basic_pay` decimal(12,2) DEFAULT NULL,
  `allowances` decimal(12,2) DEFAULT NULL,
  `deductions` decimal(12,2) DEFAULT NULL,
  `net_pay` decimal(12,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `question_type` enum('mcq','short','long','truefalse','file') DEFAULT 'short',
  `max_marks` decimal(6,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'admin', 'the main member', '2025-11-29 12:03:12'),
(2, 'member', 'This is a member admin and not have access for everything...', '2025-12-01 10:58:22'),
(3, 'Finance', 'Only finance tasks allowed...', '2026-01-21 15:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `payable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Payable, 0 = Free/Scholarship'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `payable`) VALUES
(1, 'Secondary', 1),
(2, 'primary', 1),
(3, 'H jm', 1);

-- --------------------------------------------------------

--
-- Table structure for table `section_fees`
--

CREATE TABLE `section_fees` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `fee_category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section_fees`
--

INSERT INTO `section_fees` (`id`, `section_id`, `fee_category_id`, `amount`, `created_at`) VALUES
(1, 2, 1, 15.00, '2025-12-27 11:13:01'),
(2, 1, 1, 20.00, '2025-12-27 11:13:01'),
(3, 3, 1, 6.00, '2026-01-13 13:30:03');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) DEFAULT NULL,
  `value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_no` varchar(100) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(244) NOT NULL,
  `admission_date` date DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT 'male',
  `address` text DEFAULT NULL,
  `guardian_name` varchar(200) DEFAULT NULL,
  `guardian_phone` varchar(50) DEFAULT NULL,
  `guardian_email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `admission_date`, `dob`, `gender`, `address`, `guardian_name`, `guardian_phone`, `guardian_email`, `created_at`) VALUES
(1, 'ak', '2025-12-18', '2003-07-15', 'male', 'kismayo', 'hassan ali farax', '1234512345', 'hanad25656@gmail.com', '2025-12-10 15:05:01'),
(2, 'hasssan', '2025-12-13', '2025-12-13', 'male', 'isku filan', 'salmaan farax', '0612323456', 'jo@gmail.com', '2025-12-13 11:46:36'),
(3, 'salma daahir farax', '2025-12-13', '2004-02-19', 'female', 'isku filan', 'dahir farax', '0612323400', 'hanad27@gmail.com', '2025-12-13 14:44:36'),
(4, 'Xaliima yusuf salax', '2025-12-16', '2000-09-16', 'female', 'calanleey', 'yusuf salax', '0612323456', 'j@gmail.com', '2025-12-16 15:56:24'),
(5, 'mohamed adan salax', '2025-12-20', '2003-07-08', 'male', '', 'adan salax', '0612323455', '', '2025-12-20 09:57:18'),
(6, 'suheib ahmed ali', '2025-12-01', '2004-01-20', 'male', 'Guulwade', 'ahmed ali', '0610091887', '', '2025-12-22 16:16:48'),
(7, 'salmaan xaaji farax', '2025-12-18', '2003-06-18', 'male', 'isku filan', 'xaaji farax', '0612323444', '', '2025-12-27 19:41:29'),
(8, 'Rayan cherki mustafi', '2025-12-09', '2004-05-03', 'male', 'Calan-leey', 'Hamad cherki', '0611299089', '', '2025-10-01 13:51:26'),
(9, 'mohmed jamal nuur', '2026-01-01', '2000-01-01', 'male', '', 'jamal nuur', '0615411234', '', '2026-01-06 12:36:48'),
(10, 'zakariya Hanad Ahmed', '2026-01-12', '2005-07-07', 'male', 'Isku filan', 'Hanad Ahmed', '0617565655', 'cop@gmai.com', '2026-01-12 12:37:57'),
(11, 'Rafiiq', '2026-01-14', '2026-01-14', 'male', '', 'Hanad Ahmed', '0617565655', 'cop@gmai.com', '2026-01-14 14:20:17'),
(13, 'Salma Adan Yusuf', '2026-01-17', '2026-01-01', 'female', '', '', '0980980987', '', '2026-01-17 11:18:55'),
(14, 'op', '2026-01-17', '2026-01-01', 'male', '', '', '7777777777', '', '2026-01-17 11:37:22'),
(15, 'Rafiiq', '2026-01-17', '2010-01-01', 'male', '', '', '999999999', '', '2026-01-17 12:42:44'),
(16, 'xaaji usatd', '2026-01-21', '2026-01-01', 'male', '', '', '999999999', '', '2026-01-21 12:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `student_discounts`
--

CREATE TABLE `student_discounts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_category_id` int(11) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_fees`
--

CREATE TABLE `student_fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_fee_id` int(11) NOT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `discount_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_fees`
--

INSERT INTO `student_fees` (`id`, `student_id`, `class_fee_id`, `original_amount`, `paid_amount`, `status`, `payment_date`, `created_at`, `discount_percentage`, `discount_amount`, `discount_reason`) VALUES
(33, 2, 18, 15.00, 15.00, 'paid', '2026-01-14', '2026-01-14 14:40:29', 0.00, 0.00, NULL),
(35, 3, 20, 15.00, 15.00, 'paid', '2025-12-14', '2026-01-14 15:13:52', 0.00, 0.00, NULL),
(36, 5, 19, 15.00, 10.00, 'partial', '2026-01-14', '2026-01-14 15:30:39', 0.00, 0.00, NULL),
(37, 13, 21, 20.00, 15.00, 'partial', '2026-01-17', '2026-01-17 11:24:28', 0.00, 0.00, NULL),
(38, 14, 22, 15.00, 15.00, 'paid', '2026-01-17', '2026-01-17 11:44:52', 0.00, 0.00, NULL),
(39, 14, 23, 15.00, 15.00, 'paid', '2025-11-13', '2026-01-17 11:46:05', 0.00, 0.00, NULL),
(40, 15, 24, 6.00, 6.00, 'paid', '2026-01-17', '2026-01-17 12:43:07', 0.00, 0.00, NULL),
(41, 1, 18, 20.00, 20.00, 'paid', '2026-01-18', '2026-01-18 09:38:05', 0.00, 0.00, ''),
(42, 1, 25, 20.00, 15.00, 'partial', '2025-12-04', '2026-01-18 09:59:47', 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `credit_hours` decimal(4,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `teacher_id`, `code`, `name`, `credit_hours`, `created_at`) VALUES
(6, NULL, 'TAF001', 'Tafsiir', 1.00, '2025-12-13 11:48:34'),
(8, NULL, 'JS001', 'Math', 1.00, '2025-12-14 15:14:53'),
(9, NULL, 'M001', 'Mathematics', 1.75, '2025-12-15 12:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `login_pin` varchar(255) NOT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `language_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `employee_code`, `first_name`, `last_name`, `gender`, `phone`, `email`, `login_pin`, `qualification`, `hire_date`, `status`, `salary`, `created_at`, `language_id`) VALUES
(1, '1123', 'Ahmed', 'Hassan', 'male', '+1234567890', 'sirathussein0@gmail.com', '990099', 'Degree', '2025-12-09', 'active', NULL, '2025-12-09 08:18:37', 1),
(2, '1133', 'mohamed', 'aadam ali', 'male', '655665600', 'moha@gmail.com', '', 'degree of master', '0000-00-00', 'active', NULL, '2025-12-13 08:30:25', 1),
(3, '1144', 'jaamac', 'farxaan', 'male', '655611600', 'farax@gmail.com', '999999', 'degree of master, degree of bachelor', '2025-10-30', 'active', 420.00, '2025-12-13 08:31:07', 3),
(6, '1130', 'alim', 'oska', 'male', '1234512345', 'admin@gmail.com', '', 'degree of master, degree of bachelor', '2025-12-26', 'active', 400.00, '2025-12-26 08:05:48', 1),
(9, '1212', 'Hamdi', 'Ahmed', 'female', '1231231231', 'xamdi@gmail.com', '223344', 'Degree', '2026-01-25', 'active', 350.00, '2026-01-25 11:27:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

CREATE TABLE `theme` (
  `id` int(11) NOT NULL,
  `mode` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`id`, `mode`, `updated_at`) VALUES
(1, 1, '2026-02-15 11:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `timetable_slots`
--

CREATE TABLE `timetable_slots` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `day_of_week` tinyint(4) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `translation_key` varchar(100) NOT NULL,
  `translation_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `language_id`, `translation_key`, `translation_value`) VALUES
(1, 1, 'page_title', 'Dashboard'),
(2, 1, 'dashboard', 'Dashboard'),
(3, 1, 'classes', 'Classes'),
(4, 1, 'review', 'Review'),
(5, 1, 'subjects_timetable', 'Subjects & Timetable'),
(6, 1, 'exams', 'Exams'),
(7, 1, 'notes', 'Notes'),
(8, 1, 'messages', 'Messages'),
(9, 1, 'profile', 'Profile'),
(10, 1, 'logout', 'Logout'),
(11, 1, 'error_loading_page', 'Error loading page. Please try again.'),
(12, 2, 'page_title', 'لوحة التحكم'),
(13, 2, 'dashboard', 'لوحة التحكم'),
(14, 2, 'classes', 'الفصول'),
(15, 2, 'review', 'مراجعة'),
(16, 2, 'subjects_timetable', 'المواد والجدول'),
(17, 2, 'exams', 'الامتحانات'),
(18, 2, 'notes', 'الملاحظات'),
(19, 2, 'messages', 'الرسائل'),
(20, 2, 'profile', 'الملف الشخصي'),
(21, 2, 'logout', 'تسجيل خروج'),
(22, 2, 'error_loading_page', 'حدث خطأ في تحميل الصفحة. يرجى المحاولة مرة أخرى.'),
(23, 3, 'page_title', 'Dashboard'),
(24, 3, 'dashboard', 'Dashboard'),
(25, 3, 'classes', 'Fasallada'),
(26, 3, 'review', 'Dib u eegis'),
(27, 3, 'subjects_timetable', 'Mawduucyada & Jadwalka'),
(28, 3, 'exams', 'Imtixaanada'),
(29, 3, 'notes', 'Qoraallo'),
(30, 3, 'messages', 'Fariimaha'),
(31, 3, 'profile', 'Profile'),
(32, 3, 'logout', 'Ka bax'),
(33, 3, 'error_loading_page', 'Qalad ayaa ka dhacay in la soo dejiyo bogga. Fadlan isku day mar kale.'),
(457, 1, 'report_title', 'Teacher Dashboard'),
(458, 1, 'system_name', 'Smart Attendance System'),
(459, 1, 'teacher_dashboard', 'Teacher Dashboard'),
(460, 1, 'welcome_back', 'Welcome back'),
(461, 1, 'total_subjects', 'Total Subjects'),
(462, 1, 'total_classes', 'Total Classes'),
(463, 1, 'total_students', 'Total Students'),
(464, 1, 'total_attendances', 'Total Attendances'),
(465, 1, 'busiest_day', 'Busiest Day'),
(466, 1, 'attendance_analytics', 'Attendance Analytics'),
(467, 1, 'start_date', 'Start Date'),
(468, 1, 'end_date', 'End Date'),
(469, 1, 'apply_filter', 'Apply Filter'),
(470, 1, 'todays_schedule', 'Today\'s Schedule'),
(471, 1, 'live', 'Live'),
(472, 1, 'no_subjects_scheduled', 'No subjects scheduled for today.'),
(473, 1, 'ongoing', 'Ongoing'),
(474, 1, 'completed', 'Completed'),
(475, 1, 'attendance_overview', 'Attendance Overview'),
(476, 1, 'present', 'Present'),
(477, 1, 'absent', 'Absent'),
(478, 1, 'late', 'Late'),
(479, 1, 'class_wise_attendance', 'Class-Wise Attendance'),
(480, 1, 'classes_no_attendance', 'Classes Today (No Attendance Yet)'),
(481, 1, 'all_attendance_recorded', 'All today\'s classes have attendance recorded.'),
(482, 1, 'ongoing_now', 'Ongoing Now'),
(483, 1, 'ends_at', 'Ends at'),
(484, 1, 'time_remaining', 'Time Remaining'),
(485, 1, 'up_next', 'Up Next'),
(486, 1, 'starts_at', 'Starts at'),
(487, 1, 'set_reminder', 'Set Reminder'),
(488, 1, 'student_performance', 'Student Performance'),
(489, 1, 'top_students', 'Top Students'),
(490, 1, 'times', 'times'),
(491, 1, 'need_attention', 'Need Attention'),
(492, 1, 'todays_attendance_summary', 'Today\'s Attendance Summary'),
(493, 1, 'no_attendance_recorded', 'No attendance recorded today.'),
(494, 1, 'excused', 'Excused'),
(495, 1, 'marked_by_you', 'Marked by you'),
(496, 1, 'most_late_classes', 'Most Late Classes'),
(497, 1, 'quick_actions', 'Quick Actions'),
(498, 1, 'add_attendance', 'Add Attendance'),
(499, 1, 'export_data', 'Export Data'),
(500, 1, 'view_reports', 'View Reports'),
(501, 1, 'system', 'System'),
(502, 1, 'last_refresh', 'Last Refresh'),
(503, 1, 'today_at', 'Today at'),
(504, 2, 'report_title', 'لوحة تحكم المعلم'),
(505, 2, 'system_name', 'نظام الحضور الذكي'),
(506, 2, 'teacher_dashboard', 'لوحة تحكم المعلم'),
(507, 2, 'welcome_back', 'مرحبًا بعودتك'),
(508, 2, 'total_subjects', 'إجمالي المواد'),
(509, 2, 'total_classes', 'إجمالي الفصول'),
(510, 2, 'total_students', 'إجمالي الطلاب'),
(511, 2, 'total_attendances', 'إجمالي الحضور'),
(512, 2, 'busiest_day', 'أكثر الأيام انشغالاً'),
(513, 2, 'attendance_analytics', 'تحليلات الحضور'),
(514, 2, 'start_date', 'تاريخ البداية'),
(515, 2, 'end_date', 'تاريخ النهاية'),
(516, 2, 'apply_filter', 'تطبيق التصفية'),
(517, 2, 'todays_schedule', 'جدول اليوم'),
(518, 2, 'live', 'مباشر'),
(519, 2, 'no_subjects_scheduled', 'لا توجد مواد مجدولة لهذا اليوم.'),
(520, 2, 'ongoing', 'جاري'),
(521, 2, 'completed', 'مكتمل'),
(522, 2, 'attendance_overview', 'نظرة عامة على الحضور'),
(523, 2, 'present', 'حاضر'),
(524, 2, 'absent', 'غائب'),
(525, 2, 'late', 'متأخر'),
(526, 2, 'class_wise_attendance', 'الحضور حسب الفصل'),
(527, 2, 'classes_no_attendance', 'الفصول اليوم (بدون حضور)'),
(528, 2, 'all_attendance_recorded', 'تم تسجيل حضور جميع فصول اليوم.'),
(529, 2, 'ongoing_now', 'جاري الآن'),
(530, 2, 'ends_at', 'ينتهي الساعة'),
(531, 2, 'time_remaining', 'الوقت المتبقي'),
(532, 2, 'up_next', 'التالي'),
(533, 2, 'starts_at', 'يبدأ الساعة'),
(534, 2, 'set_reminder', 'تعيين تذكير'),
(535, 2, 'student_performance', 'أداء الطلاب'),
(536, 2, 'top_students', 'أفضل الطلاب'),
(537, 2, 'times', 'مرة'),
(538, 2, 'need_attention', 'يحتاج إلى اهتمام'),
(539, 2, 'todays_attendance_summary', 'ملخص حضور اليوم'),
(540, 2, 'no_attendance_recorded', 'لم يتم تسجيل حضور اليوم.'),
(541, 2, 'excused', 'معذور'),
(542, 2, 'marked_by_you', 'مسجل بواسطتك'),
(543, 2, 'most_late_classes', 'أكثر الفصول تأخراً'),
(544, 2, 'quick_actions', 'إجراءات سريعة'),
(545, 2, 'add_attendance', 'إضافة حضور'),
(546, 2, 'export_data', 'تصدير البيانات'),
(547, 2, 'view_reports', 'عرض التقارير'),
(548, 2, 'system', 'النظام'),
(549, 2, 'last_refresh', 'آخر تحديث'),
(550, 2, 'today_at', 'اليوم الساعة'),
(551, 3, 'report_title', 'Dashboard-ka Baraha'),
(552, 3, 'system_name', 'Nidaamka Imtixaanka Smart'),
(553, 3, 'teacher_dashboard', 'Dashboard-ka Baraha'),
(554, 3, 'welcome_back', 'Soo dhawoow'),
(555, 3, 'total_subjects', 'Wadarta Mawduucyada'),
(556, 3, 'total_classes', 'Wadarta Fasallada'),
(557, 3, 'total_students', 'Wadarta Ardayda'),
(558, 3, 'total_attendances', 'Wadarta Imtixaannada'),
(559, 3, 'busiest_day', 'Maalinta Ugu Shaqo badan'),
(560, 3, 'attendance_analytics', 'Falanqaynta Imtixaanka'),
(561, 3, 'start_date', 'Taariiqda Bilowga'),
(562, 3, 'end_date', 'Taariiqda Dhamaadka'),
(563, 3, 'apply_filter', 'Codsiga Shaandhaynta'),
(564, 3, 'todays_schedule', 'Jadwalka Maanta'),
(565, 3, 'live', 'Toos'),
(566, 3, 'no_subjects_scheduled', 'Mawduuc lama ballan qorin maanta.'),
(567, 3, 'ongoing', 'Socda'),
(568, 3, 'completed', 'Dhammaystiran'),
(569, 3, 'attendance_overview', 'Dulmar Imtixaanka'),
(570, 3, 'present', 'Xaadir'),
(571, 3, 'absent', 'Maqan'),
(572, 3, 'late', 'Soo daahey'),
(573, 3, 'class_wise_attendance', 'Imtixaanka Fasalka'),
(574, 3, 'classes_no_attendance', 'Fasallada Maanta (Aan la sixin xuduurka)'),
(575, 3, 'all_attendance_recorded', 'Dhammaan fasallada maanta waa la saxay Xuduurka.'),
(576, 3, 'ongoing_now', 'Socda Hadda'),
(577, 3, 'ends_at', 'Ku dhamaada'),
(578, 3, 'time_remaining', 'Wakhti Hadhay'),
(579, 3, 'up_next', 'Ku xiga'),
(580, 3, 'starts_at', 'Ku bilaabanta'),
(581, 3, 'set_reminder', 'Deji Xusuusin'),
(582, 3, 'student_performance', 'Bandhiga Ardayda'),
(583, 3, 'top_students', 'Ardayda Ugu Fiican'),
(584, 3, 'times', 'mar'),
(585, 3, 'need_attention', 'U baahan dib u eegis'),
(586, 3, 'todays_attendance_summary', 'Koobida Xuduurka Maanta'),
(587, 3, 'no_attendance_recorded', 'Imtixaanka maanta lama qorin.'),
(588, 3, 'excused', 'La fasaxay'),
(589, 3, 'marked_by_you', 'La calaamaday adiga'),
(590, 3, 'most_late_classes', 'Fasallada Ugu Dabacsan'),
(591, 3, 'quick_actions', 'Dib u eegis fudud'),
(592, 3, 'add_attendance', 'Ku dar Imtixaanka'),
(593, 3, 'export_data', 'Dhoofinta Xogta'),
(594, 3, 'view_reports', 'Eeg Warbixinnada'),
(595, 3, 'system', 'Nidaamka'),
(596, 3, 'last_refresh', 'Cusbooneysiida Ugu Dambeeyay'),
(597, 3, 'today_at', 'Maanta Saacadda'),
(967, 1, 'monitor_manage_classes', 'Monitor and manage all your classes in one place'),
(968, 1, 'my_classes', 'My Classes'),
(969, 1, 'no_classes_assigned', 'No classes assigned'),
(970, 1, 'no_classes_assigned_desc', 'You haven\'t been assigned to any classes yet.'),
(971, 1, 'request_class', 'Request a Class'),
(972, 1, 'attendance_percent', 'Attendance %'),
(973, 1, 'trend', 'Trend'),
(974, 1, 'new_students_30d', 'New (30d)'),
(975, 1, 'good', 'Good'),
(976, 1, 'average', 'Average'),
(977, 1, 'critical', 'Critical'),
(978, 1, 'stable', 'Stable'),
(979, 1, 'improving', 'Improving'),
(980, 1, 'declining', 'Declining'),
(981, 1, 'todays_subjects', 'Today\'s Subjects'),
(982, 1, 'attendance_taken', 'Attendance Taken'),
(983, 1, 'missing_attendance', 'Missing Attendance'),
(984, 1, 'attendance_breakdown', 'Attendance Breakdown'),
(985, 1, 'frequent_issues', 'Frequent Absences/Late'),
(986, 1, 'issues', 'issues'),
(987, 1, 'no_frequent_issues', 'No frequent issues'),
(988, 1, 'next_class_today', 'Next Class Today'),
(989, 1, 'at_time', 'at'),
(990, 1, 'attention_needed', 'Attention Needed'),
(991, 1, 'attendance_missing_today', 'Attendance missing for today\'s classes'),
(992, 1, 'critical_attendance_level', 'Critical attendance level (< 60%)'),
(993, 1, 'view_class_details', 'View Class Details'),
(994, 1, 'take_attendance', 'Take Attendance'),
(995, 1, 'view_history', 'View History'),
(996, 1, 'class_notes', 'Class Notes'),
(997, 1, 'notes_placeholder', 'Add notes about this class (upcoming tests, student concerns, reminders)...'),
(998, 1, 'save_note', 'Save Note'),
(999, 1, 'school_management_system', 'School Management System'),
(1000, 2, 'monitor_manage_classes', 'مراقبة وإدارة جميع فصولك في مكان واحد'),
(1001, 2, 'my_classes', 'فصولي'),
(1002, 2, 'no_classes_assigned', 'لا توجد فصول معينة'),
(1003, 2, 'no_classes_assigned_desc', 'لم يتم تعيينك في أي فصول بعد.'),
(1004, 2, 'request_class', 'طلب فصل'),
(1005, 2, 'attendance_percent', 'نسبة الحضور %'),
(1006, 2, 'trend', 'الاتجاه'),
(1007, 2, 'new_students_30d', 'جديد (30 يوم)'),
(1008, 2, 'good', 'جيد'),
(1009, 2, 'average', 'متوسط'),
(1010, 2, 'critical', 'حرج'),
(1011, 2, 'stable', 'مستقر'),
(1012, 2, 'improving', 'يتحسن'),
(1013, 2, 'declining', 'يتدهور'),
(1014, 2, 'todays_subjects', 'مواد اليوم'),
(1015, 2, 'attendance_taken', 'تم تسجيل الحضور'),
(1016, 2, 'missing_attendance', 'الحضور مفقود'),
(1017, 2, 'attendance_breakdown', 'تفصيل الحضور'),
(1018, 2, 'frequent_issues', 'التغيب/التأخر المتكرر'),
(1019, 2, 'issues', 'مشكلة'),
(1020, 2, 'no_frequent_issues', 'لا توجد مشكلات متكررة'),
(1021, 2, 'next_class_today', 'الفصل التالي اليوم'),
(1022, 2, 'at_time', 'الساعة'),
(1023, 2, 'attention_needed', 'تحتاج إلى اهتمام'),
(1024, 2, 'attendance_missing_today', 'الحضور مفقود لفصول اليوم'),
(1025, 2, 'critical_attendance_level', 'مستوى حضور حرج (< 60%)'),
(1026, 2, 'view_class_details', 'عرض تفاصيل الفصل'),
(1027, 2, 'take_attendance', 'تسجيل الحضور'),
(1028, 2, 'view_history', 'عرض السجل'),
(1029, 2, 'class_notes', 'ملاحظات الفصل'),
(1030, 2, 'notes_placeholder', 'أضف ملاحظات حول هذا الفصل (الاختبارات القادمة، مخاوف الطلاب، التذكيرات)...'),
(1031, 2, 'save_note', 'حفظ الملاحظة'),
(1032, 2, 'school_management_system', 'نظام إدارة المدرسة'),
(1033, 3, 'monitor_manage_classes', 'Monitor iyo maamul dhammaan fasalladaaga hal meel'),
(1034, 3, 'my_classes', 'Fasalladayda'),
(1035, 3, 'no_classes_assigned', 'Fasallo lama qoondeeyo'),
(1036, 3, 'no_classes_assigned_desc', 'Weli fasallo lama qoondeeyin.'),
(1037, 3, 'request_class', 'Codsiga Fasalka'),
(1038, 3, 'attendance_percent', 'Imtixaanka %'),
(1039, 3, 'trend', 'Jihada'),
(1040, 3, 'new_students_30d', 'Cusub (30 maalin)'),
(1041, 3, 'good', 'Fiican'),
(1042, 3, 'average', 'Dhexdhexaad'),
(1043, 3, 'critical', 'Khatar'),
(1044, 3, 'stable', 'Xasiloon'),
(1045, 3, 'improving', 'Wuu horumarayaa'),
(1046, 3, 'declining', 'Wuu hoos u dhacayaa'),
(1047, 3, 'todays_subjects', 'Mawduucyada Maanta'),
(1048, 3, 'attendance_taken', 'Imtixaanka la qoray'),
(1049, 3, 'missing_attendance', 'Imtixaanka maqan'),
(1050, 3, 'attendance_breakdown', 'Kala soocida Imtixaanka'),
(1051, 3, 'frequent_issues', 'Maqnaanshaha/Dabacsashada Dhaqso leh'),
(1052, 3, 'issues', 'dhibaato'),
(1053, 3, 'no_frequent_issues', 'Dhibaatooyin dhaqso leh ma jiraan'),
(1054, 3, 'next_class_today', 'Fasalka Xiga Maanta'),
(1055, 3, 'at_time', 'Saacadda'),
(1056, 3, 'attention_needed', 'Diirado loo baahan yahay'),
(1057, 3, 'attendance_missing_today', 'Imtixaanka maanta maqan'),
(1058, 3, 'critical_attendance_level', 'Heerka imtixaanka khatar (< 60%)'),
(1059, 3, 'view_class_details', 'Eeg faahfaahinta Fasalka'),
(1060, 3, 'take_attendance', 'Qor Imtixaanka'),
(1061, 3, 'view_history', 'Eeg Taariikhda'),
(1062, 3, 'class_notes', 'Qoraallada Fasalka'),
(1063, 3, 'notes_placeholder', 'Ku dar qoraallo fasalkaan (imtixaannada soo socda, walaacaha ardayda, xasuusiyeyaasha)...'),
(1064, 3, 'save_note', 'Save garee'),
(1065, 3, 'school_management_system', 'Nidaamka Maamulka Dugsiga');

-- --------------------------------------------------------

--
-- Table structure for table `transport_assignments`
--

CREATE TABLE `transport_assignments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `pickup_point` varchar(255) DEFAULT NULL,
  `drop_point` varchar(255) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mohamed', 'moha@gmail.com', '11334455', 'Mohamed', 'Abdi', '06109898980', NULL, 'active', '2025-11-29 12:04:00', '2026-02-02 20:22:48'),
(3, 1, 'John Doe', 'admin@gmail.com', '1122', 'mohamed', 'Abdi wahid', '121213', NULL, 'active', '2025-11-12 10:56:39', NULL),
(9, 2, 'hanad', 'hanad@gmail.com', '11221122', 'hanad', 'ahmed', '123123123123', NULL, 'active', '2026-01-16 09:58:08', NULL),
(12, 2, 'haand2', 'hanad2@gmail.com', '11111111', 'iji', 'uiu', '99999999999', NULL, 'active', '2026-01-16 10:00:27', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year_month` (`year`,`month`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_class` (`student_id`,`class_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_class` (`class_id`),
  ADD KEY `fk_alumni_section` (`section_id`);

--
-- Indexes for table `alumni_criteria`
--
ALTER TABLE `alumni_criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_section` (`class_id`,`section_id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`class_id`,`section_id`,`subject_id`,`date`),
  ADD KEY `fk_att_class` (`class_id`),
  ADD KEY `fk_subject` (`subject_id`);

--
-- Indexes for table `attendance_history`
--
ALTER TABLE `attendance_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_section` (`section_id`);

--
-- Indexes for table `class_fees`
--
ALTER TABLE `class_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fee` (`class_id`,`fee_category_id`,`month`,`year`),
  ADD KEY `fee_category_id` (`fee_category_id`);

--
-- Indexes for table `class_notes`
--
ALTER TABLE `class_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_students_class` (`class_id`),
  ADD KEY `fk_class_students_student` (`student_id`);

--
-- Indexes for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_subject` (`class_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `fk_ts_teacher` (`teacher_id`);

--
-- Indexes for table `class_subject_schedule`
--
ALTER TABLE `class_subject_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_subject_id` (`class_subject_id`);

--
-- Indexes for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ct_teacher` (`teacher_user_id`),
  ADD KEY `fk_ct_class` (`class_id`),
  ADD KEY `fk_ct_section` (`section_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_students`
--
ALTER TABLE `department_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enroll_student` (`student_id`),
  ADD KEY `fk_enroll_class` (`class_id`),
  ADD KEY `fk_enroll_section` (`section_id`),
  ADD KEY `idx_enroll_year` (`academic_year`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_exam` (`name`,`class_id`,`section_id`,`academic_year`),
  ADD KEY `fk_exam_class` (`class_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_exam_student_subject` (`exam_id`,`student_id`,`subject_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fees_category` (`fee_category_id`),
  ADD KEY `fk_fees_class` (`class_id`);

--
-- Indexes for table `fee_categories`
--
ALTER TABLE `fee_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostel_allocations`
--
ALTER TABLE `hostel_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hostel_alloc_student` (`student_id`),
  ADD KEY `fk_hostel_alloc_room` (`room_id`);

--
-- Indexes for table `hostel_blocks`
--
ALTER TABLE `hostel_blocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hostel_block` (`block_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `fk_invoice_student` (`student_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_leave_staff` (`staff_id`);

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lib_book` (`book_id`),
  ADD KEY `fk_lib_student` (`student_id`);

--
-- Indexes for table `login_slideshow`
--
ALTER TABLE `login_slideshow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_marks_exam` (`exam_id`),
  ADD KEY `fk_marks_student` (`student_id`),
  ADD KEY `fk_marks_subject` (`subject_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_invoice` (`invoice_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payroll_staff` (`staff_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_supplier` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pi_purchase` (`purchase_id`),
  ADD KEY `fk_pi_asset` (`asset_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_q_exam` (`exam_id`),
  ADD KEY `fk_q_subject` (`subject_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_permission` (`permission_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_scholar_student` (`student_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_fees`
--
ALTER TABLE `section_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_section_fee` (`section_id`,`fee_category_id`),
  ADD KEY `fee_category_id` (`fee_category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`),
  ADD KEY `fk_staff_user` (`user_id`),
  ADD KEY `fk_staff_dept` (`department_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_discounts`
--
ALTER TABLE `student_discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_student_discount` (`student_id`,`fee_category_id`),
  ADD KEY `fee_category_id` (`fee_category_id`);

--
-- Indexes for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_student_fee` (`student_id`,`class_fee_id`),
  ADD KEY `class_fee_id` (`class_fee_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable_slots`
--
ALTER TABLE `timetable_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tt_class` (`class_id`),
  ADD KEY `fk_tt_section` (`section_id`),
  ADD KEY `fk_tt_subject` (`subject_id`),
  ADD KEY `fk_tt_teacher` (`teacher_id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `language_id` (`language_id`,`translation_key`);

--
-- Indexes for table `transport_assignments`
--
ALTER TABLE `transport_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trans_student` (`student_id`),
  ADD KEY `fk_trans_vehicle` (`vehicle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`),
  ADD KEY `idx_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `alumni_criteria`
--
ALTER TABLE `alumni_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `attendance_history`
--
ALTER TABLE `attendance_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `class_fees`
--
ALTER TABLE `class_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `class_notes`
--
ALTER TABLE `class_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_students`
--
ALTER TABLE `class_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `class_subjects`
--
ALTER TABLE `class_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `class_subject_schedule`
--
ALTER TABLE `class_subject_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `class_teachers`
--
ALTER TABLE `class_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_students`
--
ALTER TABLE `department_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_categories`
--
ALTER TABLE `fee_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hostel_allocations`
--
ALTER TABLE `hostel_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostel_blocks`
--
ALTER TABLE `hostel_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_issues`
--
ALTER TABLE `library_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_slideshow`
--
ALTER TABLE `login_slideshow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `section_fees`
--
ALTER TABLE `section_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_discounts`
--
ALTER TABLE `student_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `student_fees`
--
ALTER TABLE `student_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `theme`
--
ALTER TABLE `theme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timetable_slots`
--
ALTER TABLE `timetable_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1066;

--
-- AUTO_INCREMENT for table `transport_assignments`
--
ALTER TABLE `transport_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni`
--
ALTER TABLE `alumni`
  ADD CONSTRAINT `fk_alumni_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumni_section` FOREIGN KEY (`section_id`) REFERENCES `classes` (`section_id`) ON UPDATE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_class_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_fees`
--
ALTER TABLE `class_fees`
  ADD CONSTRAINT `class_fees_ibfk_2` FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`);

--
-- Constraints for table `class_students`
--
ALTER TABLE `class_students`
  ADD CONSTRAINT `fk_class_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_students_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD CONSTRAINT `class_subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ts_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_subject_schedule`
--
ALTER TABLE `class_subject_schedule`
  ADD CONSTRAINT `class_subject_schedule_ibfk_1` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD CONSTRAINT `fk_ct_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ct_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ct_teacher` FOREIGN KEY (`teacher_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `department_students`
--
ALTER TABLE `department_students`
  ADD CONSTRAINT `department_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `department_students_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enroll_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_enroll_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_enroll_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `fk_exam_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fk_fees_category` FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fees_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hostel_allocations`
--
ALTER TABLE `hostel_allocations`
  ADD CONSTRAINT `fk_hostel_alloc_room` FOREIGN KEY (`room_id`) REFERENCES `hostel_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hostel_alloc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD CONSTRAINT `fk_hostel_block` FOREIGN KEY (`block_id`) REFERENCES `hostel_blocks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoice_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_leave_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD CONSTRAINT `fk_lib_book` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lib_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `fk_marks_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_marks_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_purchase_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `fk_pi_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pi_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_q_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD CONSTRAINT `fk_scholar_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_fees`
--
ALTER TABLE `section_fees`
  ADD CONSTRAINT `section_fees_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `section_fees_ibfk_2` FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_discounts`
--
ALTER TABLE `student_discounts`
  ADD CONSTRAINT `student_discounts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_discounts_ibfk_2` FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`);

--
-- Constraints for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD CONSTRAINT `student_fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_fees_ibfk_2` FOREIGN KEY (`class_fee_id`) REFERENCES `class_fees` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `timetable_slots`
--
ALTER TABLE `timetable_slots`
  ADD CONSTRAINT `fk_tt_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tt_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tt_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
