-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2023 at 04:38 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simple_attendance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_tbl`
--

CREATE TABLE `attendance_tbl` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_date` date NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 = Present, 2 = Late, 3 = Absent, 4 = Holiday',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_tbl`
--

INSERT INTO `attendance_tbl` (`id`, `student_id`, `class_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, '2023-11-30', 2, '2023-11-30 08:52:11', '2023-11-30 09:07:40'),
(2, 3, '2023-11-30', 1, '2023-11-30 08:52:11', '2023-11-30 09:06:53'),
(3, 1, '2023-11-30', 1, '2023-11-30 08:52:11', NULL),
(4, 5, '2023-11-30', 3, '2023-11-30 08:52:11', '2023-11-30 09:07:40'),
(5, 6, '2023-11-30', 1, '2023-11-30 08:52:11', '2023-11-30 09:06:53'),
(6, 4, '2023-11-27', 4, '2023-11-30 09:08:16', NULL),
(7, 3, '2023-11-27', 4, '2023-11-30 09:08:16', NULL),
(8, 1, '2023-11-27', 4, '2023-11-30 09:08:16', NULL),
(9, 5, '2023-11-27', 4, '2023-11-30 09:08:16', NULL),
(10, 6, '2023-11-27', 4, '2023-11-30 09:08:16', NULL),
(11, 4, '2023-11-28', 1, '2023-11-30 09:08:27', NULL),
(12, 3, '2023-11-28', 1, '2023-11-30 09:08:27', NULL),
(13, 1, '2023-11-28', 2, '2023-11-30 09:08:27', NULL),
(14, 5, '2023-11-28', 1, '2023-11-30 09:08:27', NULL),
(15, 6, '2023-11-28', 1, '2023-11-30 09:08:27', NULL),
(16, 4, '2023-11-24', 1, '2023-11-30 09:09:19', NULL),
(17, 3, '2023-11-24', 3, '2023-11-30 09:09:19', NULL),
(18, 1, '2023-11-24', 1, '2023-11-30 09:09:19', NULL),
(19, 5, '2023-11-24', 2, '2023-11-30 09:09:19', NULL),
(20, 6, '2023-11-24', 2, '2023-11-30 09:09:19', NULL),
(21, 4, '2023-11-29', 1, '2023-11-30 10:23:29', NULL),
(22, 3, '2023-11-29', 1, '2023-11-30 10:23:29', NULL),
(23, 1, '2023-11-29', 1, '2023-11-30 10:23:29', NULL),
(24, 5, '2023-11-29', 1, '2023-11-30 10:23:29', NULL),
(25, 6, '2023-11-29', 1, '2023-11-30 10:23:29', NULL),
(26, 4, '2023-11-01', 1, '2023-11-30 10:54:41', NULL),
(27, 3, '2023-11-01', 1, '2023-11-30 10:54:41', NULL),
(28, 1, '2023-11-01', 1, '2023-11-30 10:54:41', NULL),
(29, 5, '2023-11-01', 1, '2023-11-30 10:54:41', NULL),
(30, 6, '2023-11-01', 1, '2023-11-30 10:54:41', NULL),
(31, 4, '2023-11-02', 1, '2023-11-30 10:54:57', NULL),
(32, 3, '2023-11-02', 1, '2023-11-30 10:54:57', NULL),
(33, 1, '2023-11-02', 1, '2023-11-30 10:54:57', NULL),
(34, 5, '2023-11-02', 1, '2023-11-30 10:54:57', NULL),
(35, 6, '2023-11-02', 1, '2023-11-30 10:54:57', NULL),
(36, 4, '2023-11-03', 2, '2023-11-30 10:55:03', NULL),
(37, 3, '2023-11-03', 1, '2023-11-30 10:55:03', NULL),
(38, 1, '2023-11-03', 2, '2023-11-30 10:55:03', NULL),
(39, 5, '2023-11-03', 1, '2023-11-30 10:55:03', NULL),
(40, 6, '2023-11-03', 1, '2023-11-30 10:55:03', NULL),
(41, 4, '2023-11-06', 1, '2023-11-30 10:55:12', NULL),
(42, 3, '2023-11-06', 1, '2023-11-30 10:55:12', NULL),
(43, 1, '2023-11-06', 1, '2023-11-30 10:55:12', NULL),
(44, 5, '2023-11-06', 1, '2023-11-30 10:55:12', NULL),
(45, 6, '2023-11-06', 1, '2023-11-30 10:55:12', NULL),
(46, 4, '2023-11-07', 1, '2023-11-30 10:55:19', NULL),
(47, 3, '2023-11-07', 3, '2023-11-30 10:55:19', NULL),
(48, 1, '2023-11-07', 1, '2023-11-30 10:55:19', NULL),
(49, 5, '2023-11-07', 1, '2023-11-30 10:55:19', NULL),
(50, 6, '2023-11-07', 1, '2023-11-30 10:55:19', NULL),
(51, 4, '2023-11-08', 3, '2023-11-30 10:55:26', NULL),
(52, 3, '2023-11-08', 1, '2023-11-30 10:55:26', NULL),
(53, 1, '2023-11-08', 1, '2023-11-30 10:55:26', NULL),
(54, 5, '2023-11-08', 2, '2023-11-30 10:55:26', NULL),
(55, 6, '2023-11-08', 1, '2023-11-30 10:55:26', NULL),
(56, 4, '2023-11-09', 1, '2023-11-30 10:55:31', NULL),
(57, 3, '2023-11-09', 1, '2023-11-30 10:55:31', NULL),
(58, 1, '2023-11-09', 1, '2023-11-30 10:55:31', NULL),
(59, 5, '2023-11-09', 1, '2023-11-30 10:55:31', NULL),
(60, 6, '2023-11-09', 1, '2023-11-30 10:55:31', NULL),
(61, 4, '2023-11-10', 1, '2023-11-30 10:55:37', NULL),
(62, 3, '2023-11-10', 1, '2023-11-30 10:55:37', NULL),
(63, 1, '2023-11-10', 1, '2023-11-30 10:55:37', NULL),
(64, 5, '2023-11-10', 1, '2023-11-30 10:55:37', NULL),
(65, 6, '2023-11-10', 1, '2023-11-30 10:55:37', NULL),
(66, 4, '2023-11-13', 1, '2023-11-30 10:55:48', NULL),
(67, 3, '2023-11-13', 1, '2023-11-30 10:55:48', NULL),
(68, 1, '2023-11-13', 1, '2023-11-30 10:55:48', NULL),
(69, 5, '2023-11-13', 1, '2023-11-30 10:55:48', NULL),
(70, 6, '2023-11-13', 1, '2023-11-30 10:55:48', NULL),
(71, 4, '2023-11-14', 1, '2023-11-30 10:55:52', NULL),
(72, 3, '2023-11-14', 1, '2023-11-30 10:55:52', NULL),
(73, 1, '2023-11-14', 1, '2023-11-30 10:55:52', NULL),
(74, 5, '2023-11-14', 1, '2023-11-30 10:55:52', NULL),
(75, 6, '2023-11-14', 1, '2023-11-30 10:55:52', NULL),
(76, 4, '2023-11-15', 1, '2023-11-30 10:55:57', NULL),
(77, 3, '2023-11-15', 1, '2023-11-30 10:55:57', NULL),
(78, 1, '2023-11-15', 1, '2023-11-30 10:55:57', NULL),
(79, 5, '2023-11-15', 1, '2023-11-30 10:55:57', NULL),
(80, 6, '2023-11-15', 1, '2023-11-30 10:55:57', NULL),
(81, 4, '2023-11-16', 1, '2023-11-30 10:56:02', NULL),
(82, 3, '2023-11-16', 1, '2023-11-30 10:56:02', NULL),
(83, 1, '2023-11-16', 1, '2023-11-30 10:56:02', NULL),
(84, 5, '2023-11-16', 2, '2023-11-30 10:56:02', NULL),
(85, 6, '2023-11-16', 1, '2023-11-30 10:56:02', NULL),
(86, 4, '2023-11-17', 1, '2023-11-30 10:56:07', NULL),
(87, 3, '2023-11-17', 1, '2023-11-30 10:56:07', NULL),
(88, 1, '2023-11-17', 1, '2023-11-30 10:56:07', NULL),
(89, 5, '2023-11-17', 1, '2023-11-30 10:56:07', NULL),
(90, 6, '2023-11-17', 1, '2023-11-30 10:56:07', NULL),
(91, 4, '2023-11-20', 1, '2023-11-30 10:56:14', NULL),
(92, 3, '2023-11-20', 1, '2023-11-30 10:56:14', NULL),
(93, 1, '2023-11-20', 1, '2023-11-30 10:56:14', NULL),
(94, 5, '2023-11-20', 1, '2023-11-30 10:56:14', NULL),
(95, 6, '2023-11-20', 1, '2023-11-30 10:56:14', NULL),
(96, 4, '2023-11-21', 1, '2023-11-30 10:56:21', NULL),
(97, 3, '2023-11-21', 1, '2023-11-30 10:56:21', NULL),
(98, 1, '2023-11-21', 1, '2023-11-30 10:56:21', NULL),
(99, 5, '2023-11-21', 1, '2023-11-30 10:56:21', NULL),
(100, 6, '2023-11-21', 3, '2023-11-30 10:56:21', NULL),
(101, 4, '2023-11-22', 1, '2023-11-30 10:56:32', NULL),
(102, 3, '2023-11-22', 2, '2023-11-30 10:56:32', NULL),
(103, 1, '2023-11-22', 1, '2023-11-30 10:56:32', NULL),
(104, 5, '2023-11-22', 2, '2023-11-30 10:56:32', NULL),
(105, 6, '2023-11-22', 1, '2023-11-30 10:56:32', NULL),
(106, 4, '2023-11-23', 1, '2023-11-30 10:57:14', NULL),
(107, 3, '2023-11-23', 1, '2023-11-30 10:57:14', NULL),
(108, 1, '2023-11-23', 1, '2023-11-30 10:57:14', NULL),
(109, 5, '2023-11-23', 1, '2023-11-30 10:57:14', NULL),
(110, 6, '2023-11-23', 1, '2023-11-30 10:57:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_tbl`
--

CREATE TABLE `class_tbl` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_tbl`
--

INSERT INTO `class_tbl` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Grade 8-1 - English', '2023-11-16 11:37:26', '2023-11-16 11:52:34'),
(2, 'Grade 8-2 - English', '2023-11-16 11:52:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students_tbl`
--

CREATE TABLE `students_tbl` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_tbl`
--

INSERT INTO `students_tbl` (`id`, `class_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Smith', '2023-11-16 13:18:15', '2023-11-16 13:18:27'),
(3, 1, 'John Doe', '2023-11-16 13:18:49', NULL),
(4, 1, 'Claire Blake', '2023-11-16 13:18:56', NULL),
(5, 1, 'Mark Cooper', '2023-11-16 13:19:18', NULL),
(6, 1, 'Samantha Lou', '2023-11-16 13:19:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id_fk` (`student_id`);

--
-- Indexes for table `class_tbl`
--
ALTER TABLE `class_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_tbl`
--
ALTER TABLE `students_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id_fk` (`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `class_tbl`
--
ALTER TABLE `class_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students_tbl`
--
ALTER TABLE `students_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  ADD CONSTRAINT `student_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `students_tbl`
--
ALTER TABLE `students_tbl`
  ADD CONSTRAINT `class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `class_tbl` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
