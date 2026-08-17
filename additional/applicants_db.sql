-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2026 at 06:45 AM
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

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `name`, `designation_score`, `applied_date`) VALUES
(1, 'John Doe', 50, '2026-06-01'),
(2, 'Kamal Perera', 80, '2026-06-02'),
(3, 'Nimal Silva', 95, '2026-06-02'),
(4, 'Sunil Jayasuriya', 70, '2026-06-03');

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

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `nic`, `computer_no`, `quarter_type`, `application_date`, `boss_status`, `boss_reason`, `file_status`, `file_reason`, `clerk_status`, `clerk_reason`, `final_status`, `final_reason`, `marks`, `waiting_list_no`, `created_at`) VALUES
(1, '199012345678', 'EMP001', 'Type A', '2026-03-01', 'approved', 'Checked and verified', 'approved', 'All documents complete', 'rejected', 'Missing income tax report', 'pending', 'Waiting for final review', 85.50, 3, '2026-08-04 03:52:23'),
(2, '198812345678', 'EMP002', 'Type B', '2026-03-05', 'approved', 'Approved', 'approved', 'Verified', 'approved', 'Checked', 'approved', 'Finalized', 85.00, 1, '2026-08-06 05:02:56'),
(3, '199512345678', 'EMP003', 'Type A', '2026-03-10', 'rejected', 'Invalid employee details', 'pending', '', 'pending', '', 'pending', '', 60.00, NULL, '2026-08-06 05:02:56'),
(4, '198512345678', 'EMP004', 'Type C', '2026-03-12', 'approved', 'Approved by boss', 'rejected', 'Service period insufficient', 'pending', '', 'pending', '', 70.00, NULL, '2026-08-06 05:02:56');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `nic`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, '199012345678', 'Application Received', 'Your quarter application has been successfully submitted to the system.', 1, '2026-08-01 09:00:00'),
(2, '199012345678', 'Document Verification', 'Your boss status has been approved by the department head.', 1, '2026-08-03 10:30:00'),
(3, '199012345678', 'Interview Schedule', 'Your technical interview is scheduled for next Monday at 10.00 AM.', 1, '2026-08-04 06:19:28'),
(4, '199012345678', 'Action Required', 'Please update your missing income tax report as requested by the clerk.', 0, '2026-08-05 14:20:00'),
(5, '198812345678', 'Application Received', 'Your application for Type B quarter has been recorded.', 1, '2026-08-01 09:15:00'),
(6, '198812345678', 'Boss Approval', 'Your application has been verified and approved by your supervisor.', 1, '2026-08-03 11:00:00'),
(7, '198812345678', 'Quarter Application Approved', 'Your application for quarter allocation has been successfully approved.', 1, '2026-08-04 07:00:00'),
(8, '198812345678', 'Allocation Confirmed', 'You have been placed at position 1 in the waiting list.', 0, '2026-08-06 08:30:00'),
(9, '199512345678', 'Application Submitted', 'Your Type A application is currently pending initial review.', 1, '2026-08-02 08:00:00'),
(10, '199512345678', 'Review Update', 'Your boss status was rejected due to invalid employee details.', 1, '2026-08-04 12:00:00'),
(11, '199512345678', 'Document Verification', 'Please submit your missing salary confirmation letter before Friday.', 0, '2026-08-05 03:45:00'),
(12, '199512345678', 'Profile Notice', 'Please correct your employee records to proceed with the allocation.', 0, '2026-08-06 09:00:00'),
(13, '198512345678', 'Application Registered', 'Your Type C quarter application has been received.', 1, '2026-08-02 10:00:00'),
(14, '198512345678', 'Supervisor Review', 'Your application was approved by your direct boss.', 1, '2026-08-04 14:00:00'),
(15, '198512345678', 'File Status Alert', 'Your file status was rejected due to insufficient service period.', 1, '2026-08-05 08:00:00'),
(16, '198512345678', 'Allocation Cancelled', 'Your previous quarter request has been cancelled due to incomplete details.', 1, '2026-08-05 08:50:00');

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

--
-- Dumping data for table `respond_to_offer`
--

INSERT INTO `respond_to_offer` (`id`, `nic`, `status`, `created_at`) VALUES
(1, '199012345678', 'accepted', '2026-08-14 09:44:41'),
(2, '198812345678', 'approved', '2026-08-06 07:37:00'),
(3, '199512345678', 'pending', '2026-08-06 07:38:48'),
(4, '198512345678', 'approved', '2026-08-10 03:33:45');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`nic`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nic`, `name`, `email`, `password`, `created_at`) VALUES
('198512345678', 'Sunil Jayasuriya', 'sunil@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2026-07-30 05:32:21'),
('198812345678', 'Kamal Perera', 'kamal@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2026-07-30 05:32:21'),
('199012345678', 'John Do', 'john@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2026-07-30 05:32:21'),
('199512345678', 'Nimal Silva', 'nimal@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2026-07-30 05:32:21');

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
-- Dumping data for table `waiting_list`
--

INSERT INTO `waiting_list` (`id`, `nic`, `position`, `quarter_type`, `created_at`, `applied_date`, `designation_score`, `employee_marks`) VALUES
(1, '199012345678', 3, 'Type A', '2026-07-30 05:32:21', '2026-01-10', 10, 85),
(2, '198812345678', 1, 'Type B', '2026-07-30 05:32:21', '2026-01-05', 15, 92),
(3, '199512345678', 4, 'Type A', '2026-07-30 05:49:28', '2026-01-15', 5, 60),
(4, '198512345678', 2, 'Type C', '2026-07-30 05:49:31', '2026-01-08', 12, 75);

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


--
-- Metadata
--
USE `phpmyadmin`;

--
-- Metadata for table applicants
--

--
-- Metadata for table applications
--

--
-- Metadata for table notifications
--

--
-- Metadata for table respond_to_offer
--

--
-- Metadata for table users
--

--
-- Metadata for table waiting_list
--

--
-- Metadata for database applicants_db
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
