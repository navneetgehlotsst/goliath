-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2024 at 03:53 PM
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
-- Database: `goliath`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_question_to_matches`
--

CREATE TABLE `add_question_to_matches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `matchid` int(11) NOT NULL DEFAULT 0,
  `questionid` varchar(255) NOT NULL DEFAULT '0',
  `over` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `add_question_to_matches`
--

INSERT INTO `add_question_to_matches` (`id`, `matchid`, `questionid`, `over`, `created_at`, `updated_at`) VALUES
(1, 74706, '1,2,3,4,6,9,10,13', 1, NULL, NULL),
(2, 74706, '1,2,3,4,6,9,10,13', 2, NULL, NULL),
(3, 74706, '1,2,3,4,6,9,10,13', 3, NULL, NULL),
(4, 74706, '1,2,3,4,6,9,10,13', 4, NULL, NULL),
(5, 74706, '1,2,3,4,6,9,10,13', 5, NULL, NULL),
(6, 74706, '1,2,3,4,6,9,10,13', 6, NULL, NULL),
(7, 74706, '1,2,3,4,6,9,10,13', 7, NULL, NULL),
(8, 74706, '1,2,3,4,6,9,10,13', 8, NULL, NULL),
(9, 74706, '1,2,3,4,6,9,10,13', 9, NULL, NULL),
(10, 74706, '1,2,3,4,6,9,10,13', 10, NULL, NULL),
(11, 74706, '1,2,3,4,6,9,10,13', 11, NULL, NULL),
(12, 74706, '1,2,3,4,6,9,10,13', 12, NULL, NULL),
(13, 74706, '1,2,3,4,6,9,10,13', 13, NULL, NULL),
(14, 74706, '1,2,3,4,6,9,10,13', 14, NULL, NULL),
(15, 74706, '1,2,3,4,6,9,10,13', 15, NULL, NULL),
(16, 74706, '1,2,3,4,6,9,10,13', 16, NULL, NULL),
(17, 74706, '1,2,3,4,6,9,10,13', 17, NULL, NULL),
(18, 74706, '1,2,3,4,6,9,10,13', 18, NULL, NULL),
(19, 74706, '1,2,3,4,6,9,10,13', 19, NULL, NULL),
(20, 74706, '1,2,3,4,6,9,10,13', 20, NULL, NULL),
(21, 74706, '1,2,3,4,6,9,10,13', 21, NULL, NULL),
(22, 74706, '1,2,3,4,6,9,10,13', 22, NULL, NULL),
(23, 74706, '1,2,3,4,6,9,10,13', 23, NULL, NULL),
(24, 74706, '1,2,3,4,6,9,10,13', 24, NULL, NULL),
(25, 74706, '1,2,3,4,6,9,10,13', 25, NULL, NULL),
(26, 74706, '1,2,3,4,6,9,10,13', 26, NULL, NULL),
(27, 74706, '1,2,3,4,6,9,10,13', 27, NULL, NULL),
(28, 74706, '1,2,3,4,6,9,10,13', 28, NULL, NULL),
(29, 74706, '1,2,3,4,6,9,10,13', 29, NULL, NULL),
(30, 74706, '1,2,3,4,6,9,10,13', 30, NULL, NULL),
(31, 74706, '1,2,3,4,6,9,10,13', 31, NULL, NULL),
(32, 74706, '1,2,3,4,6,9,10,13', 32, NULL, NULL),
(33, 74706, '1,2,3,4,6,9,10,13', 33, NULL, NULL),
(34, 74706, '1,2,3,4,6,9,10,13', 34, NULL, NULL),
(35, 74706, '1,2,3,4,6,9,10,13', 35, NULL, NULL),
(36, 74706, '1,2,3,4,6,9,10,13', 36, NULL, NULL),
(37, 74706, '1,2,3,4,6,9,10,13', 37, NULL, NULL),
(38, 74706, '1,2,3,4,6,9,10,13', 38, NULL, NULL),
(39, 74706, '1,2,3,4,6,9,10,13', 39, NULL, NULL),
(40, 74706, '1,2,3,4,6,9,10,13', 40, NULL, NULL),
(41, 74706, '1,2,3,4,6,9,10,13', 41, NULL, NULL),
(42, 74706, '1,2,3,4,6,9,10,13', 42, NULL, NULL),
(43, 74706, '1,2,3,4,6,9,10,13', 43, NULL, NULL),
(44, 74706, '1,2,3,4,6,9,10,13', 44, NULL, NULL),
(45, 74706, '1,2,3,4,6,9,10,13', 45, NULL, NULL),
(46, 74706, '1,2,3,4,6,9,10,13', 46, NULL, NULL),
(47, 74706, '1,2,3,4,6,9,10,13', 47, NULL, NULL),
(48, 74706, '1,2,3,4,6,9,10,13', 48, NULL, NULL),
(49, 74706, '1,2,3,4,6,9,10,13', 49, NULL, NULL),
(50, 74706, '1,2,3,4,6,9,10,13', 50, NULL, NULL),
(51, 74709, '1,2,3,4,6,9,10,13', 1, NULL, NULL),
(52, 74709, '1,2,3,4,6,9,10,13', 2, NULL, NULL),
(53, 74709, '1,2,3,4,6,9,10,13', 3, NULL, NULL),
(54, 74709, '1,2,3,4,6,9,10,13', 4, NULL, NULL),
(55, 74709, '1,2,3,4,6,9,10,13', 5, NULL, NULL),
(56, 74709, '1,2,3,4,6,9,10,13', 6, NULL, NULL),
(57, 74709, '1,2,3,4,6,9,10,13', 7, NULL, NULL),
(58, 74709, '1,2,3,4,6,9,10,13', 8, NULL, NULL),
(59, 74709, '1,2,3,4,6,9,10,13', 9, NULL, NULL),
(60, 74709, '1,2,3,4,6,9,10,13', 10, NULL, NULL),
(61, 74709, '1,2,3,4,6,9,10,13', 11, NULL, NULL),
(62, 74709, '1,2,3,4,6,9,10,13', 12, NULL, NULL),
(63, 74709, '1,2,3,4,6,9,10,13', 13, NULL, NULL),
(64, 74709, '1,2,3,4,6,9,10,13', 14, NULL, NULL),
(65, 74709, '1,2,3,4,6,9,10,13', 15, NULL, NULL),
(66, 74709, '1,2,3,4,6,9,10,13', 16, NULL, NULL),
(67, 74709, '1,2,3,4,6,9,10,13', 17, NULL, NULL),
(68, 74709, '1,2,3,4,6,9,10,13', 18, NULL, NULL),
(69, 74709, '1,2,3,4,6,9,10,13', 19, NULL, NULL),
(70, 74709, '1,2,3,4,6,9,10,13', 20, NULL, NULL),
(71, 73801, '1,2,3,4,6,9,10,13', 1, NULL, NULL),
(72, 73801, '1,2,3,4,6,9,10,13', 2, NULL, NULL),
(73, 73801, '1,2,3,4,6,9,10,13', 3, NULL, NULL),
(74, 73801, '1,2,3,4,6,9,10,13', 4, NULL, NULL),
(75, 73801, '1,2,3,4,6,9,10,13', 5, NULL, NULL),
(76, 73801, '1,2,3,4,6,9,10,13', 6, NULL, NULL),
(77, 73801, '1,2,3,4,6,9,10,13', 7, NULL, NULL),
(78, 73801, '1,2,3,4,6,9,10,13', 8, NULL, NULL),
(79, 73801, '1,2,3,4,6,9,10,13', 9, NULL, NULL),
(80, 73801, '1,2,3,4,6,9,10,13', 10, NULL, NULL),
(81, 73801, '1,2,3,4,6,9,10,13', 11, NULL, NULL),
(82, 73801, '1,2,3,4,6,9,10,13', 12, NULL, NULL),
(83, 73801, '1,2,3,4,6,9,10,13', 13, NULL, NULL),
(84, 73801, '1,2,3,4,6,9,10,13', 14, NULL, NULL),
(85, 73801, '1,2,3,4,6,9,10,13', 15, NULL, NULL),
(86, 73801, '1,2,3,4,6,9,10,13', 16, NULL, NULL),
(87, 73801, '1,2,3,4,6,9,10,13', 17, NULL, NULL),
(88, 73801, '1,2,3,4,6,9,10,13', 18, NULL, NULL),
(89, 73801, '1,2,3,4,6,9,10,13', 19, NULL, NULL),
(90, 73801, '1,2,3,4,6,9,10,13', 20, NULL, NULL),
(91, 74733, '1,2,3,4,6,9,10,13', 1, NULL, NULL),
(92, 74733, '1,2,3,4,6,9,10,13', 2, NULL, NULL),
(93, 74733, '1,2,3,4,6,9,10,13', 3, NULL, NULL),
(94, 74733, '1,2,3,4,6,9,10,13', 4, NULL, NULL),
(95, 74733, '1,2,3,4,6,9,10,13', 5, NULL, NULL),
(96, 74733, '1,2,3,4,6,9,10,13', 6, NULL, NULL),
(97, 74733, '1,2,3,4,6,9,10,13', 7, NULL, NULL),
(98, 74733, '1,2,3,4,6,9,10,13', 8, NULL, NULL),
(99, 74733, '1,2,3,4,6,9,10,13', 9, NULL, NULL),
(100, 74733, '1,2,3,4,6,9,10,13', 10, NULL, NULL),
(101, 74733, '1,2,3,4,6,9,10,13', 11, NULL, NULL),
(102, 74733, '1,2,3,4,6,9,10,13', 12, NULL, NULL),
(103, 74733, '1,2,3,4,6,9,10,13', 13, NULL, NULL),
(104, 74733, '1,2,3,4,6,9,10,13', 14, NULL, NULL),
(105, 74733, '1,2,3,4,6,9,10,13', 15, NULL, NULL),
(106, 74733, '1,2,3,4,6,9,10,13', 16, NULL, NULL),
(107, 74733, '1,2,3,4,6,9,10,13', 17, NULL, NULL),
(108, 74733, '1,2,3,4,6,9,10,13', 18, NULL, NULL),
(109, 74733, '1,2,3,4,6,9,10,13', 19, NULL, NULL),
(110, 74733, '1,2,3,4,6,9,10,13', 20, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country_code` int(11) NOT NULL DEFAULT 61,
  `password` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `type` enum('email','mobile') NOT NULL DEFAULT 'mobile',
  `otp` varchar(255) DEFAULT NULL,
  `otp_expired` varchar(255) DEFAULT NULL,
  `avatar` text NOT NULL DEFAULT '',
  `device_token` varchar(255) NOT NULL DEFAULT '',
  `device_type` enum('android','ios') NOT NULL DEFAULT 'ios',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`id`, `first_name`, `last_name`, `full_name`, `slug`, `email`, `phone`, `country_code`, `password`, `email_verified_at`, `phone_verified_at`, `role`, `type`, `otp`, `otp_expired`, `avatar`, `device_token`, `device_type`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(67, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-5', 'navneetgehlot.sst2@gmail.com', NULL, 91, NULL, NULL, NULL, 'user', 'mobile', '1234', '2024-04-25 08:13:29', '', 'test', 'ios', 'active', NULL, '2024-04-25 00:43:29', '2024-04-25 00:43:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `check_otps`
--

CREATE TABLE `check_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_code` int(11) DEFAULT NULL,
  `data` varchar(255) NOT NULL,
  `otp` int(11) NOT NULL,
  `otp_expire_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `check_otps`
--

INSERT INTO `check_otps` (`id`, `country_code`, `data`, `otp`, `otp_expire_time`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, NULL, 'navneetgehlot.sst12@gmail.com', 1234, '2024-04-18 14:40:36', '2024-04-18 07:10:36', '2024-04-18 07:10:39', '2024-04-18 07:10:39'),
(5, NULL, 'navneetgehlot.sst12@gmail.com', 1234, '2024-04-18 14:44:29', '2024-04-18 07:14:29', '2024-04-18 07:14:29', NULL),
(6, NULL, 'navneetgehlot.sst12@gmail.com', 1234, '2024-04-18 14:44:56', '2024-04-18 07:14:56', '2024-04-18 07:14:56', NULL),
(7, NULL, '7821810600', 1234, '2024-04-18 14:45:35', '2024-04-18 07:15:35', '2024-04-18 07:16:57', '2024-04-18 07:16:57'),
(8, NULL, '7821810600', 1234, '2024-04-18 14:46:41', '2024-04-18 07:16:41', '2024-04-18 07:16:41', NULL),
(9, NULL, '7821810600', 1234, '2024-04-18 14:47:09', '2024-04-18 07:17:09', '2024-04-18 07:17:09', NULL),
(10, NULL, '7821810600', 1234, '2024-04-18 14:49:18', '2024-04-18 07:19:18', '2024-04-18 07:19:18', NULL),
(11, NULL, '7821810612', 1234, '2024-04-18 14:50:04', '2024-04-18 07:20:04', '2024-04-18 07:20:30', '2024-04-18 07:20:30'),
(12, NULL, '7821810612', 1234, '2024-04-18 14:50:50', '2024-04-18 07:20:50', '2024-04-18 07:20:50', NULL),
(13, NULL, '7821810613', 1234, '2024-04-18 14:51:38', '2024-04-18 07:21:38', '2024-04-18 07:21:38', NULL),
(16, 91, '7821810614', 1234, '2024-04-22 07:29:15', '2024-04-21 23:59:15', '2024-04-21 23:59:25', '2024-04-21 23:59:25'),
(23, 91, '7821810615', 1234, '2024-04-25 08:13:56', '2024-04-25 00:43:56', '2024-04-25 00:43:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Navneet Gehlot', 'navneet@gmail.com', 'test', '2024-04-18 01:51:57', '2024-04-18 01:51:57', NULL),
(2, 'Navneet Gehlot', 'navneet@gmail.com', 'test', '2024-04-18 01:53:53', '2024-04-18 01:53:53', NULL),
(3, 'Navneet Gehlot', 'navneet@gmail.com', 'test', '2024-04-18 01:58:17', '2024-04-18 01:58:17', NULL),
(4, 'Navneet Gehlot', 'navneet@gmail.com', 'test', '2024-04-18 02:03:50', '2024-04-18 02:03:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `how_to_plays`
--

CREATE TABLE `how_to_plays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `how_to_plays`
--

INSERT INTO `how_to_plays` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, 'Signup www.goliath101.com', NULL, NULL),
(2, 'Make your match Selection', NULL, NULL),
(3, 'Select The OVER(S) to play', NULL, NULL),
(4, 'Select one or multiple OVERS', NULL, NULL),
(5, 'Make your predictions', NULL, NULL),
(6, 'Load your Wallet', NULL, NULL),
(7, 'Entry fee ₹501 per OVER', NULL, NULL),
(8, 'Check the APP', NULL, NULL),
(9, 'See instant results WIN/LOSE', NULL, NULL),
(10, 'Check your winning status.', NULL, NULL),
(11, 'Check your wallet', NULL, NULL),
(12, 'Predictions for next OVER', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `innings_overs`
--

CREATE TABLE `innings_overs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `match_innings_id` int(11) NOT NULL DEFAULT 0,
  `overs` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_innings`
--

CREATE TABLE `match_innings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `match_id` int(11) NOT NULL DEFAULT 0,
  `innings` enum('1','2','3','4') NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2023_11_29_105313_create_app_users_table', 1),
(7, '2023_11_29_105352_create_contacts_table', 1),
(8, '2023_11_29_105450_create_notifications_table', 1),
(9, '2023_11_29_105510_create_notification_users_table', 1),
(10, '2023_11_29_105528_create_pages_table', 1),
(11, '2024_03_01_122556_create_splash_screens_table', 1),
(12, '2024_04_03_062503_create_questions_table', 1),
(13, '2024_04_05_095948_create_transactions_table', 1),
(16, '2024_04_15_114910_create_how_to_plays_table', 2),
(17, '2024_04_16_112346_add_type_to_questions', 3),
(18, '2023_11_29_105417_create_email_otps_table', 4),
(20, '2024_04_24_092344_create_add_question_to_match_table', 5),
(21, '2024_04_24_123404_create_match_innings_table', 5),
(22, '2024_04_24_123425_create_innings_overs_table', 5),
(23, '2024_04_24_123448_create_over_questions_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `action_type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL,
  `data` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_users`
--

CREATE TABLE `notification_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `over_questions`
--

CREATE TABLE `over_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `innings_over_id` int(11) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `key`, `name`, `value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'faqs', 'Faqs', '', '2024-04-12 05:04:15', '2024-04-12 05:04:15', NULL),
(2, 'about-app', 'About App', '', '2024-04-12 05:04:15', '2024-04-12 05:04:15', NULL),
(3, 'term-condition', 'Term & Condition', '', '2024-04-12 05:04:15', '2024-04-12 05:04:15', NULL),
(4, 'privacy-policy', 'Privacy Policy', '', '2024-04-12 05:04:15', '2024-04-12 05:04:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` varchar(255) NOT NULL,
  `question_type` enum('dot_ball','boundary','wicket','run','no_ball','wide') NOT NULL DEFAULT 'dot_ball',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `conditions` enum('greater_than','less_than','equal','greater_than_equal','less_than_equal','not_equal','even','odd') NOT NULL DEFAULT 'equal',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `type` enum('initial','supplementry') NOT NULL DEFAULT 'initial'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `question_type`, `status`, `conditions`, `quantity`, `created_at`, `updated_at`, `deleted_at`, `type`) VALUES
(1, 'Even runs in over', 'run', 'active', 'even', 0, NULL, NULL, NULL, 'initial'),
(2, 'First ball scoring', 'run', 'active', 'not_equal', 0, NULL, NULL, NULL, 'initial'),
(3, 'Boundary in over', 'boundary', 'active', 'greater_than_equal', 1, NULL, NULL, NULL, 'initial'),
(4, '“6” hit in over', 'boundary', 'active', 'greater_than_equal', 1, NULL, NULL, NULL, 'initial'),
(6, 'More than 3 dot balls', 'dot_ball', 'active', 'greater_than', 3, NULL, NULL, NULL, 'initial'),
(9, 'More than 3 single runs', 'run', 'active', 'greater_than', 3, NULL, NULL, NULL, 'initial'),
(10, 'More than 2 double runs', 'run', 'active', 'greater_than', 2, NULL, NULL, NULL, 'initial'),
(11, 'More than 2 boundaries', 'boundary', 'active', 'greater_than', 2, NULL, NULL, NULL, 'supplementry'),
(12, 'More than 1 6', 'boundary', 'active', 'greater_than', 1, NULL, NULL, NULL, 'supplementry'),
(13, 'Total in over more than 7 runs', 'run', 'active', 'greater_than', 7, NULL, NULL, NULL, 'initial'),
(14, 'No Ball in Over', 'no_ball', 'active', 'greater_than_equal', 1, NULL, NULL, NULL, 'supplementry'),
(15, 'Wide in Over', 'wide', 'active', 'greater_than_equal', 1, NULL, NULL, NULL, 'supplementry'),
(16, 'LBW In an over', 'wicket', 'active', 'greater_than_equal', 1, NULL, NULL, NULL, 'supplementry'),
(17, 'Maiden Over', 'run', 'active', 'equal', 0, NULL, NULL, NULL, 'supplementry'),
(18, 'Out for Duck', 'wicket', 'active', 'equal', 0, NULL, NULL, NULL, 'supplementry');

-- --------------------------------------------------------

--
-- Table structure for table `splash_screens`
--

CREATE TABLE `splash_screens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('onboarding','welcome') NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `splash_screens`
--

INSERT INTO `splash_screens` (`id`, `type`, `heading`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 'onboarding', 'Find people who match with you', '', 'assets\\app\\splash\\onboarding_one.png', '2024-04-12 05:04:15', '2024-04-12 05:04:15'),
(2, 'onboarding', 'Easily message & call the people you like', '', 'assets\\app\\splash\\onboarding_two.png', '2024-04-12 05:04:15', '2024-04-12 05:04:15'),
(3, 'onboarding', 'Don`t wait anymore, find out your soul mate now', '', 'assets\\app\\splash\\onboarding_three.png', '2024-04-12 05:04:15', '2024-04-12 05:04:15'),
(4, 'welcome', 'Let\'s you in', '', 'assets\\app\\splash\\welcome.png', '2024-04-12 05:04:15', '2024-04-12 05:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `amount` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `transaction_type` enum('admin-payout','wallet-transiction','winning-amount') NOT NULL DEFAULT 'winning-amount',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `amount`, `transaction_id`, `transaction_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 95, '598', 'mCQF63epGk', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(2, 110, '616', 'AcqPOy2MkP', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(3, 37, '847', 'Ui2Y3uxD7q', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(4, 37, '148', 'sp17ziKwF3', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(5, 112, '281', 'XkmJWfo85e', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(6, 94, '818', 'xiKuqRGivf', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(7, 89, '133', 'wBQqV3gKn7', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(8, 37, '934', 'qqsjfhi0o7', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(9, 101, '541', 'BzKnJ45B5o', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL),
(10, 99, '950', 'VaRY459I7H', 'winning-amount', '2024-04-16 07:15:58', '2024-04-16 07:15:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country_code` int(11) NOT NULL DEFAULT 61,
  `password` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `type` enum('email','mobile') NOT NULL DEFAULT 'mobile',
  `otp` varchar(255) DEFAULT NULL,
  `otp_expired` varchar(255) DEFAULT NULL,
  `avatar` text NOT NULL DEFAULT '',
  `device_token` varchar(255) NOT NULL DEFAULT '',
  `device_type` enum('android','ios') NOT NULL DEFAULT 'ios',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `full_name`, `slug`, `email`, `phone`, `country_code`, `password`, `email_verified_at`, `phone_verified_at`, `role`, `type`, `otp`, `otp_expired`, `avatar`, `device_token`, `device_type`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Project', 'Admin', 'Project Admin', 'project-admin', 'projectadmin@mailinator.com', '8000000000', 61, '$2y$12$K9Lf5alugnFyZ47egrjd1ee5vEPHxiDP/FgEitrBMIKcEYymoabU6', NULL, NULL, 'admin', 'mobile', NULL, NULL, 'uploads/user/1713153033Goliath-101-MQ.png', '', 'ios', 'active', NULL, '2024-04-12 05:04:15', '2024-04-14 22:20:33', NULL),
(121, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot', 'navneetgehlot.sst12@gmail.com', NULL, 91, NULL, NULL, NULL, 'user', 'mobile', '1234', '2024-04-18 14:44:56', '', 'test', 'ios', 'active', NULL, '2024-04-18 07:10:39', '2024-04-18 07:14:56', NULL),
(122, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-1', NULL, '7821810600', 91, NULL, NULL, NULL, 'user', 'mobile', '1234', '2024-04-18 14:49:18', '', 'test', 'ios', 'active', NULL, '2024-04-18 07:16:57', '2024-04-18 07:19:18', NULL),
(123, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-2', NULL, '7821810612', 91, NULL, NULL, NULL, 'user', 'mobile', '1234', '2024-04-18 14:50:50', '', 'test', 'ios', 'active', NULL, '2024-04-18 07:20:30', '2024-04-18 07:20:50', NULL),
(124, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-3', NULL, '7821810614', 91, NULL, NULL, NULL, 'user', 'mobile', NULL, NULL, '', 'test', 'ios', 'active', NULL, '2024-04-21 23:59:25', '2024-04-21 23:59:25', NULL),
(125, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-4', NULL, '7821810615', 91, NULL, NULL, NULL, 'user', 'mobile', '1234', '2024-04-25 08:13:56', '', 'test', 'ios', 'active', NULL, '2024-04-22 00:01:02', '2024-04-25 00:43:56', NULL),
(126, 'Navneet', 'Gehlot', 'Navneet Gehlot', 'navneet-gehlot-5', 'navneetgehlot.sst13@gmail.com', NULL, 91, NULL, NULL, NULL, 'user', 'mobile', NULL, NULL, '', 'test', 'ios', 'active', NULL, '2024-04-25 00:52:27', '2024-04-25 00:52:27', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_question_to_matches`
--
ALTER TABLE `add_question_to_matches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_users_email_unique` (`email`),
  ADD UNIQUE KEY `app_users_phone_unique` (`phone`);

--
-- Indexes for table `check_otps`
--
ALTER TABLE `check_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `how_to_plays`
--
ALTER TABLE `how_to_plays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `innings_overs`
--
ALTER TABLE `innings_overs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `match_innings`
--
ALTER TABLE `match_innings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_users`
--
ALTER TABLE `notification_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `over_questions`
--
ALTER TABLE `over_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `splash_screens`
--
ALTER TABLE `splash_screens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_question_to_matches`
--
ALTER TABLE `add_question_to_matches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `check_otps`
--
ALTER TABLE `check_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `how_to_plays`
--
ALTER TABLE `how_to_plays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `innings_overs`
--
ALTER TABLE `innings_overs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `match_innings`
--
ALTER TABLE `match_innings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notification_users`
--
ALTER TABLE `notification_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `over_questions`
--
ALTER TABLE `over_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `splash_screens`
--
ALTER TABLE `splash_screens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
