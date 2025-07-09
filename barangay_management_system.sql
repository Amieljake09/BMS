-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jul 09, 2025 at 01:22 PM
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
-- Database: `barangay_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_held` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `attendees_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `position`, `phone`, `created_at`) VALUES
(2, 'Administrator', 'admin@example.com', '$2y$10$Pf9XPIwDEHT65BLkKRAUGeZ/qF/HsL2cTc7KhLYVfofWLmSdD4QGe', 'Administrator', '09978723222', '2025-07-01 07:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `description`, `created_at`) VALUES
(24, 'Announcement', 'Our barangay, Gaid Dimasalang Masbate, is generally peaceful because, aside from our reliable tanods, we homeowners strictly implement measures in each respective street. Extended families are a no-no and transpo is on limited time. Not only is ours peaceful, we also host a lot of schoolchildren from nearby barangays because we have a high school and wide conducive grounds. Our Toda is polite.', '2025-07-01 07:01:21');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_reports`
--

CREATE TABLE `blotter_reports` (
  `id` int(11) NOT NULL,
  `residents_id` int(11) DEFAULT NULL,
  `complainant_name` varchar(255) NOT NULL,
  `incident_details` text NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `date_reported` datetime DEFAULT current_timestamp(),
  `complainant_address` varchar(255) DEFAULT NULL,
  `complainant_contact` varchar(20) DEFAULT NULL,
  `accused_name` varchar(255) DEFAULT NULL,
  `accused_address` varchar(255) DEFAULT NULL,
  `accused_contact` varchar(255) DEFAULT NULL,
  `complaint_type` varchar(100) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `incident_time` time DEFAULT NULL,
  `incident_location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_reports`
--

INSERT INTO `blotter_reports` (`id`, `residents_id`, `complainant_name`, `incident_details`, `status`, `date_reported`, `complainant_address`, `complainant_contact`, `accused_name`, `accused_address`, `accused_contact`, `complaint_type`, `incident_date`, `incident_time`, `incident_location`) VALUES
(4, 32, 'Amiel Jake Baril', 'On June 30, 2025, at approximately 10:30 PM, a report was filed by John Doe regarding a theft incident at 123 Main Street. Doe stated that his black wallet containing $200 in cash, credit cards, and his driver\\\'s license was stolen from his unlocked vehicle while parked in front of his residence. The vehicle was a blue sedan, make and model not specified. No witnesses were reported at the time of the incident. The responding officer, Patrolman Smith, documented the details and filed a blotter report for further investigation. ', 'Pending', '2025-07-02 14:55:22', '', '', 'Marc Arizobal', 'Gaid', 'Marc Arizobal', 'Nanapak ng walang dahilan', '2025-06-30', '21:30:00', 'Gaid Plaza'),
(7, 35, 'Maricel Baril', 'On June 30, 2025, at approximately 10:30 PM, a report was filed by John Doe regarding a theft incident at 123 Main Street. Doe stated that his black wallet containing $200 in cash, credit cards, and his driver\\\'s license was stolen from his unlocked vehicle while parked in front of his residence. The vehicle was a blue sedan, make and model not specified. No witnesses were reported at the time of the incident. The responding officer, Patrolman Smith, documented the details and filed a blotter report for further investigation. ', 'Ongoing', '2025-07-02 15:05:21', '', '', 'Ruffa Arizobal', 'Gaid', 'Affur Damiar Arizobal', 'Paninirang puri', '2025-06-30', '09:30:00', 'Gaid Plaza'),
(8, 50, 'Rechel Gentiliso', 'sahsjahdkjhakshdksa', 'Ongoing', '2025-07-03 10:22:57', '', '', 'Amiel Jake', 'Sauyo', 'Amiel Jake', 'Nanapak ng walang dahilan', '2025-06-03', '16:30:00', 'Sauyo'),
(9, 30, 'Jesse Barrera', 'dhfshjafheuhfuhaficbiebfweicebeyfugewgsasafefaeebebdeuhdehufefeubfuebfueufhaehhahfeufhaeuhauifaiehfa', 'Pending', '2025-07-03 15:57:05', '', '', 'Emmanuel Ballesteros', 'Munoz', 'Emmanuel Ballesteros', 'Nagwawala', '2004-05-03', '21:30:00', 'Novaliches');

-- --------------------------------------------------------

--
-- Table structure for table `budget_allocations`
--

CREATE TABLE `budget_allocations` (
  `id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `allocated_amount` decimal(10,2) DEFAULT NULL,
  `used_amount` decimal(10,2) DEFAULT NULL,
  `year` int(11) DEFAULT year(curdate())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_reports`
--

CREATE TABLE `community_reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `priority_level` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `evidence` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `action_taken` text DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `people_involved` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submitter_name` varchar(255) DEFAULT NULL,
  `submitter_email` varchar(255) DEFAULT NULL,
  `submitter_contact` varchar(15) DEFAULT NULL,
  `submitter_type` varchar(50) DEFAULT 'Resident'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_reports`
--

INSERT INTO `community_reports` (`report_id`, `user_id`, `report_type`, `category`, `priority_level`, `location`, `title`, `description`, `evidence`, `status`, `action_taken`, `action_date`, `people_involved`, `recommendations`, `created_at`, `updated_at`, `submitter_name`, `submitter_email`, `submitter_contact`, `submitter_type`) VALUES
(8, 32, 'Kalat', 'Environmental', 'Medium', 'Area 6', 'Kalat', 'Napakahalaga na tugunan ang isyu ng **kalat ng basura** sa ating komunidad, dahil direktang nakakaapekto ito sa kalusugan at kapakanan ng bawat residente. Hindi lamang ito nagdudulot ng masamang amoy at nakakabawas sa ganda ng ating kapaligiran, kundi nagiging sanhi rin ito ng pagdami ng mga peste tulad ng daga at insekto na maaaring magdala ng sakit. Bukod pa rito, ang hindi tamang pagtatapon ng basura ay nagbabara sa mga kanal at daluyan ng tubig, na nagiging sanhi ng pagbaha lalo na tuwing tag-ulan, na nagpapahirap sa pang-araw-araw na buhay at nagdudulot ng malaking perwisyo sa mga ari-arian.', NULL, 'Pending', NULL, NULL, NULL, NULL, '2025-07-03 05:47:13', '2025-07-03 05:47:13', 'Amiel Jake Baril', 'amieljake@gmail.com', '09978723222', 'Resident');

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `resident_name` varchar(255) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `business_address` varchar(255) DEFAULT NULL,
  `tin_number` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `educ_attainment` varchar(100) DEFAULT NULL,
  `course_graduated` varchar(100) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `purpose` text NOT NULL,
  `notes` text DEFAULT NULL,
  `request_date` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `hidden_for_official` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`id`, `resident_id`, `resident_name`, `document_type`, `business_name`, `business_address`, `tin_number`, `dob`, `pob`, `citizenship`, `profile_photo`, `educ_attainment`, `course_graduated`, `profession`, `purpose`, `notes`, `request_date`, `status`, `hidden_for_official`, `approved_by`, `fee`) VALUES
(46, 35, 'Maricel Baril', 'Clearance', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '', 'work purposes', 'work purposes', '2025-07-02 15:27:18', 'Approved', 0, 4, 0.00),
(47, 50, 'Rechel Gentiliso', 'Clearance', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '', 'hjh', 'jkjk', '2025-07-03 10:20:14', 'Approved', 0, 4, 0.00),
(48, 32, 'Amiel Jake Baril', 'Barangay ID', '', '', '', '2004-04-09', 'Valenzuela General Hospital', 'Filipino', NULL, '', '', '', 'work purposes', 'work purposes', '2025-07-05 05:41:32', 'Approved', 0, 4, 0.00),
(49, 32, 'Amiel Jake Baril', 'First Time Job Seeker', '', '', '', '0000-00-00', '', '', NULL, '4th year college', 'Information Technology (IT)', '', 'work purposes', 'work purposes', '2025-07-05 05:44:28', 'Approved', 0, 4, 0.00),
(50, 32, 'Amiel Jake Baril', 'Clearance', '', '', '', '0000-00-00', '', '', NULL, '', '', '', 'work purposes', 'work purposes', '2025-07-05 05:46:51', 'Approved', 0, 4, 0.00),
(51, 32, 'Amiel Jake Baril', 'Residency', '', '', '', '0000-00-00', '', '', NULL, '', '', '', 'future purposes', 'future purposes', '2025-07-05 05:47:09', 'Rejected', 0, NULL, 0.00),
(55, 32, 'Amiel Jake Baril', 'Barangay ID', '', '', '', '2004-04-09', 'Valenzuela General Hospital', 'Filipino', NULL, '', '', '', 'work', 'work', '2025-07-05 07:03:57', 'Pending', 0, NULL, 0.00),
(56, 30, 'Jesse Barrera', 'First Time Job Seeker', '', '', '', '0000-00-00', '', '', NULL, 'College Graduated', 'Information Technology (IT)', '', 'work purposes', 'work purposes', '2025-07-05 07:06:16', 'Approved', 0, 4, 0.00),
(57, 30, 'Jesse Barrera', 'Barangay ID', '', '', '', '2000-11-11', 'Valenzuela General Hospital', 'Filipino', NULL, '', '', '', 'work', 'work', '2025-07-05 07:06:53', 'Approved', 0, 33, 0.00),
(58, 30, 'Jesse Barrera', 'Clearance', '', '', '', '0000-00-00', '', '', NULL, '', '', '', 'work ', 'work', '2025-07-05 07:09:31', 'Pending', 0, NULL, 0.00),
(59, 30, 'Jesse Barrera', 'Cedula', '', '', '111-111-111', '0000-00-00', '', '', NULL, '', '', 'Secuirty Guard', 'work', 'work', '2025-07-05 07:10:22', 'Approved', 0, 33, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `id` int(11) NOT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `head_of_family` varchar(100) DEFAULT NULL,
  `members` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `house_number`, `head_of_family`, `members`, `created_at`) VALUES
(1, '12345', 'Amiel Jake Baril', '[\"Maricel Baril\",\"Ariel Baril\",\"Mariel Jean Baril\"]', '2025-07-02 11:37:45'),
(2, '13456', 'Christine Hieto', '[\"Charlene Hieto\",\"Che hieto\"]', '2025-07-02 11:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widow/Widower','Separated') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `term_start` date DEFAULT NULL,
  `term_end` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_off` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`id`, `user_id`, `dob`, `pob`, `age`, `gender`, `civil_status`, `nationality`, `religion`, `position`, `term_start`, `term_end`, `address`, `phone`, `email_off`) VALUES
(2, 4, '2003-11-09', 'Valenzuela', 22, 'Female', 'Single', 'Philippines', 'INC', 'Secretary', '2024-09-04', '2026-09-04', 'San Pedro 6 Subdi JVC compound Emerald street tandang sora QC', '09858145011', 'chrstn1000011@gmail.com'),
(6, 33, '2004-04-09', 'QC', 21, 'Male', 'Single', 'Filipino', 'catholic', 'Kagawad', '2024-11-11', '2027-11-11', 'QC', '09123238585', 'lukechiang@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'chrstn1000011@gmail.com', '2b936d7a748f42725c0222244bdf3b4ba6fb13e751e75a66caed5be233ea3791', '2025-05-24 12:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `total_budget` decimal(10,2) DEFAULT NULL,
  `funds_received` decimal(10,2) DEFAULT NULL,
  `status` enum('Ongoing','Completed','Pending') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widow/Widower','Separated') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `res_email` varchar(255) DEFAULT NULL,
  `resident_type` enum('Permanent','Temporary','Voter','Non-Voter') DEFAULT NULL,
  `stay_length` varchar(50) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp(),
  `household_id` int(11) DEFAULT NULL,
  `employment_status` enum('Employed','Unemployed','Self-Employed','Student','Retired','Homemaker','Others') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `user_id`, `dob`, `pob`, `age`, `gender`, `civil_status`, `nationality`, `religion`, `address`, `phone`, `res_email`, `resident_type`, `stay_length`, `proof`, `date_registered`, `household_id`, `employment_status`) VALUES
(16, 30, '2004-04-09', 'QC', 21, 'Male', 'Single', 'ph', 'catholic', 'QC', '09111111111', 'jessebarrera@gmail.com', 'Permanent', '2', '2', '2025-07-01 15:39:27', NULL, 'Self-Employed'),
(17, 32, '2004-04-09', 'Valenzuela General Hospital', 21, 'Male', 'Single', 'Filipino', 'catholic', 'QC', '09978723222', 'amieljake929@gmail.com', 'Permanent', '2', '2', '2025-07-01 19:15:41', NULL, 'Student'),
(19, 35, '1979-09-01', 'Masbate', 46, 'Female', 'Married', 'Filipino', 'catholic', 'QC', '09111111111', 'maricelbaril@gmail.com', 'Permanent', '2', '2', '2025-07-01 20:54:52', NULL, 'Employed'),
(22, 43, '2000-09-11', 'Valenzuela General Hospital', 24, 'Female', 'Single', 'Filipino', 'INC', 'San Pedro 6 Subdi JVC compound Emerald street tandang sora QC', '09123238585', 'charlene@gmail.com', 'Temporary', '2', '2', '2025-07-02 15:48:48', NULL, 'Unemployed'),
(24, 47, '2000-11-11', 'Valenzuela General Hospital', 24, 'Male', 'Single', 'Filipino', 'catholic', 'San Pedro 6 Subdi JVC compound Emerald street tandang sora QC', '09111111111', 'gerald@gmail.com', 'Permanent', '2', 'uploads/proof_6866237d8fe77_Clearance_20250701072627.pdf', '2025-07-03 14:30:21', NULL, 'Employed'),
(25, 48, '2000-11-11', 'Masbate', 24, 'Male', 'Married', 'Filipino', 'catholic', 'QC', '09111111111', 'haha@gmail.com', 'Permanent', '2', 'uploads/proof_68662dccdf85b_Clearance_20250701072627.pdf', '2025-07-03 15:14:20', NULL, 'Unemployed'),
(26, 49, '2003-11-11', 'Masbate', 21, 'Male', 'Single', 'Filipino', 'catholic', 'San Pedro 6 Subdi JVC compound Emerald street tandang sora QC', '09111111111', 'christian@gmail.com', 'Permanent', '2', 'uploads/proof_68663a911ca8a_BARIL, AMIEL JAKE A..pdf', '2025-07-03 16:08:49', NULL, 'Unemployed'),
(27, 50, '2004-09-20', 'QC', 21, 'Female', 'Single', 'Filipino', 'catholic', 'QC', '09111111111', 'rechel@gmail.com', 'Permanent', '3', 'uploads/proof_68663ca8efa5e_BARIL, AMIEL JAKE A..pdf', '2025-07-03 16:17:44', NULL, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `date_started` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `user_id`, `address`, `phone`, `email`, `position`, `date_started`) VALUES
(1, 23, 'San Pedro 6 Subdi JVC compound Emerald street tandang sora QC', '09978723222', 'danielaberba@gmail.com', '', '2025-07-01'),
(2, 37, 'QC tandang sora pearl st', '09978723222', 'mariel@gmail.com', 'Barangay Treasurer', '2007-09-20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(4, 'Christine Hieto', 'chrstn1000011@gmail.com', '$2y$10$o1c9klg4Fj9wG6memBiWw.lqqu2urxfN88EX.r9X5re7JPJ39TxfC', 'Official', '2025-05-24 03:13:07', 'approved'),
(23, 'Daniela Berba', 'danielaberba@gmail.com', '$2y$10$4U.Kp0DzYa7gQ/Je.z/qpe4uA4JKKvi3.zdOoUTE0FfygHzDsMNvK', 'Staff', '2025-07-01 04:43:59', 'approved'),
(30, 'Jesse Barrera', 'jessebarrera@gmail.com', '$2y$10$1L91F14sd3WSdEGDfj9kfuLbssHUa0KpcQ7xmEzUDKTiihQxa2MwG', 'Resident', '2025-07-01 07:39:27', 'approved'),
(31, 'Administrator', 'admin@example.com', '$2y$10$Pf9XPIwDEHT65BLkKRAUGeZ/qF/HsL2cTc7KhLYVfofWLmSdD4QGe', 'Admin', '2025-07-01 07:46:36', 'approved'),
(32, 'Amiel Jake Baril', 'amieljake929@gmail.com', '$2y$10$NOYNYhI8MKEXGw4dAWdRP.CgryYsqVhYBuH1NiLsp4AotRuDK1jA.', 'Resident', '2025-07-01 11:15:41', 'approved'),
(33, 'Luke Chiang', 'lukechiang@gmail.com', '$2y$10$EFpw3TB3MInM7HV5EnzAreYGbaz4QVJIYx0iF8G5s3jYw/56xS.ke', 'Official', '2025-07-01 11:40:34', 'approved'),
(35, 'Maricel Baril', 'maricelbaril@gmail.com', '$2y$10$0sPqOmVgzFIrZdu7Jb2nu.13mYLhrrJsG/c1n2QZ0tOFS97D2GWA6', 'Resident', '2025-07-01 12:54:52', 'approved'),
(37, 'Mariel Jean Baril', 'mariel@gmail.com', '$2y$10$5lN90.HK9VdzwenjaCEvJuXkrtvDKLH6zSjBcvAbRV9IIA0E6VpcW', 'Staff', '2025-07-02 07:20:21', 'approved'),
(43, 'Charlene Hieto', 'charlene@gmail.com', '$2y$10$u.InrJ2PKFVMiHNa9/JtUed94Ztclah9URvHCtpLj5ZhqSbAxH4OS', 'Resident', '2025-07-02 07:48:48', 'approved'),
(47, 'Gerald Anderson', 'gerald@gmail.com', '$2y$10$QJlXn5j0Cs5vCDbtIwr0.O86E1myJCx9FCHLxVWFdgQxOeaYhHxdG', 'Resident', '2025-07-03 06:30:21', 'pending'),
(48, 'haha', 'haha@gmail.com', '$2y$10$/lUJorK9ilrJQHedLqvFwO.ERTUn4huAUoYUM21LVpCpdnmUNviA2', 'Resident', '2025-07-03 07:14:20', 'pending'),
(49, 'Christian Clores', 'christian@gmail.com', '$2y$10$e1.92X5kHb5XR.NvhqoYpu1mHll3V0y4tmiDkBZfxrlwwnVKRZ5lG', 'Resident', '2025-07-03 08:08:49', 'pending'),
(50, 'Rechel Gentiliso', 'rechel@gmail.com', '$2y$10$9PCmwwKuAw/hkbBrJhR54eY3Nzgzwn8zHWyuaH/WrPTUAO92547Ta', 'Resident', '2025-07-03 08:17:44', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`residents_id`);

--
-- Indexes for table `budget_allocations`
--
ALTER TABLE `budget_allocations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `community_reports`
--
ALTER TABLE `community_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `budget_allocations`
--
ALTER TABLE `budget_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_reports`
--
ALTER TABLE `community_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`residents_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_reports`
--
ALTER TABLE `community_reports`
  ADD CONSTRAINT `community_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `residents` (`user_id`);

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `officials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `residents`
--
ALTER TABLE `residents`
  ADD CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
