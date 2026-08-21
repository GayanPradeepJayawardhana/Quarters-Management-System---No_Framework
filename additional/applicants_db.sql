-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2026 at 06:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `applicants_db`
--
CREATE DATABASE IF NOT EXISTS `applicants_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `applicants_db`;

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

DROP TABLE IF EXISTS `applicants`;
CREATE TABLE IF NOT EXISTS `applicants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `designation_score` int(11) NOT NULL DEFAULT 0,
  `applied_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nic` varchar(20) NOT NULL,
  `computer_no` varchar(50) NOT NULL,
  `quarter_type` varchar(100) NOT NULL,
  `application_date` date NOT NULL,
  `boss_status` varchar(20) DEFAULT 'pending',
  `boss_reason` text DEFAULT NULL,
  `file_status` varchar(20) DEFAULT 'pending',
  `file_reason` text DEFAULT NULL,
  `clerk_status` varchar(20) DEFAULT 'pending',
  `clerk_reason` text DEFAULT NULL,
  `final_status` varchar(20) DEFAULT 'pending',
  `final_reason` text DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `waiting_list_no` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nic` (`nic`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nic` varchar(20) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nic_time` (`nic`,`attempt_time`),
  KEY `idx_ip_time` (`ip_address`,`attempt_time`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `nic`, `ip_address`, `success`, `attempt_time`) VALUES
(1, '200312345678', '::1', 1, '2026-08-18 05:31:10'),
(2, '200312345678', '::1', 1, '2026-08-18 06:04:40'),
(3, '200312345678', '::1', 1, '2026-08-18 06:16:18'),
(4, '199012345678', '::1', 0, '2026-08-21 03:42:21'),
(5, '199012345678', '::1', 0, '2026-08-21 03:42:27'),
(6, '199012345678', '::1', 0, '2026-08-21 03:42:30'),
(7, '20031234567', '::1', 0, '2026-08-21 03:43:07'),
(8, '20031234567', '::1', 0, '2026-08-21 03:43:15'),
(9, '200312345678', '::1', 0, '2026-08-21 03:43:31'),
(10, '200312345678', '::1', 1, '2026-08-21 03:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nic` varchar(20) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nic` (`nic`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respond_to_offer`
--

DROP TABLE IF EXISTS `respond_to_offer`;
CREATE TABLE IF NOT EXISTS `respond_to_offer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nic` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nic` (`nic`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `nic` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `computer_number` varchar(50) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`nic`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nic`, `name`, `email`, `password`, `computer_number`, `mobile`, `is_active`, `created_at`, `updated_at`) VALUES
('198512345678', 'Sunil Jayasuriya', 'sunil@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'EMP004', '0712345681', 1, '2026-08-18 04:06:53', '2026-08-18 04:06:53'),
('198812345678', 'Kamal Perera', 'kamal@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'EMP002', '0712345679', 1, '2026-08-18 04:06:53', '2026-08-18 04:06:53'),
('199012345678', 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'EMP001', '0712345678', 1, '2026-08-18 04:06:53', '2026-08-18 04:06:53'),
('199512345678', 'Nimal Silva', 'nimal@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'EMP003', '0712345680', 1, '2026-08-18 04:06:53', '2026-08-18 04:06:53'),
('200312345678', 'Gayan Pradeep Jayawardhana', 'gpj@gmail.com', '$2y$12$Grhjkf.c4D/4Y899vMEED.FUAbufV0cOZQn5sp.tbhLnnRR.a2UwS', '200312345678', '0771234567', 1, '2026-08-18 05:30:49', '2026-08-18 05:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `waiting_list`
--

DROP TABLE IF EXISTS `waiting_list`;
CREATE TABLE IF NOT EXISTS `waiting_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nic` varchar(20) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `quarter_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `applied_date` date DEFAULT NULL,
  `designation_score` int(11) DEFAULT 0,
  `employee_marks` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `nic` (`nic`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`nic`) REFERENCES `users` (`nic`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`nic`) REFERENCES `users` (`nic`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `respond_to_offer`
--
ALTER TABLE `respond_to_offer`
  ADD CONSTRAINT `respond_to_offer_ibfk_1` FOREIGN KEY (`nic`) REFERENCES `users` (`nic`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `waiting_list`
--
ALTER TABLE `waiting_list`
  ADD CONSTRAINT `waiting_list_ibfk_1` FOREIGN KEY (`nic`) REFERENCES `users` (`nic`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
