-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 11, 2025 at 08:25 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sales_queue`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `salesperson_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('scheduled','processing','completed','no_show') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `created_by`, `salesperson_id`, `customer_name`, `customer_phone`, `date`, `time`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 3, 'Beatrice Horne', '+1 (288) 848-4967', '1977-03-27', '20:28:00', 'completed', 'Tempor duis incidunt', '2025-06-09 12:36:15', '2025-06-09 15:10:32'),
(2, 3, 3, 'Sebastian Bean', '+1 (569) 218-2827', '1973-02-16', '12:51:00', 'completed', 'Elit natus ut persp', '2025-06-09 12:37:29', '2025-06-09 14:55:02'),
(3, 3, 6, 'Brenda Francis', '+1 (305) 147-4365', '2019-05-03', '13:00:00', 'scheduled', 'Dolorem magna repudi', '2025-06-09 12:49:16', '2025-06-09 12:49:16'),
(4, 2, 4, 'fsdj', 'kkhk', '2025-06-10', '09:08:00', 'scheduled', '23456789ytrdfghjkl;', '2025-06-09 14:43:07', '2025-06-09 14:43:07'),
(5, 2, 3, 'uyytr', 'eteyru', '2025-06-10', '12:34:00', 'completed', 'qwertyuiop', '2025-06-09 15:12:39', '2025-06-09 15:13:49'),
(6, 2, 3, 'fsdf', 'sdf', '2025-06-10', '23:04:00', 'completed', 'dfs', '2025-06-09 15:53:21', '2025-06-09 15:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:23:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:8:\"view any\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:8:\"view own\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:6:\"create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:4:\"edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:6:\"delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:6:\"manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:10:\"view users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:12:\"create users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:10:\"edit users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:12:\"delete users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"manage users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:10:\"view roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:12:\"create roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:10:\"edit roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"delete roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:12:\"assign roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:16:\"view permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:18:\"manage permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:3:{s:1:\"a\";i:19;s:1:\"b\";s:13:\"view vehicles\";s:1:\"c\";s:3:\"web\";}i:19;a:3:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"create vehicles\";s:1:\"c\";s:3:\"web\";}i:20;a:3:{s:1:\"a\";i:21;s:1:\"b\";s:13:\"edit vehicles\";s:1:\"c\";s:3:\"web\";}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:15:\"delete vehicles\";s:1:\"c\";s:3:\"web\";}i:22;a:3:{s:1:\"a\";i:23;s:1:\"b\";s:15:\"manage settings\";s:1:\"c\";s:3:\"web\";}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"Sales Manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"Sales person\";s:1:\"c\";s:3:\"web\";}}}', 1749711960);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_sales`
--

CREATE TABLE `customer_sales` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `process` json DEFAULT NULL,
  `disposition` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_sales`
--

INSERT INTO `customer_sales` (`id`, `name`, `email`, `phone`, `interest`, `notes`, `process`, `disposition`, `created_at`, `updated_at`) VALUES
(1, 'dfjlk', 'asb@gmail.com', 'kljljl', 'jljl', 'jljlj', '[\"Investigating\"]', '[\"Sold!\", \"Didn\'t Like Price\"]', '2025-06-09 15:39:13', '2025-06-09 15:39:13'),
(2, 'qwerty', 'ali@gmail.com', '332610467', 'fdsadsf', 'sdf', '[\"Investigating\"]', '[\"Challenged Credit\"]', '2025-06-09 15:44:33', '2025-06-09 15:44:33'),
(3, 'qwerty', 'ali@gmail.com', '332610467', 'mbn', 'yery', '[\"Penciling\"]', '[\"Challenged Credit\"]', '2025-06-09 15:46:36', '2025-06-09 15:46:36'),
(4, 'fsd', 'ali@gmail.com', '332610467', 'fa', 'dfs', '[\"Test Driving\"]', '[\"Insurance Expensive\"]', '2025-06-09 15:47:21', '2025-06-09 15:47:21'),
(5, 'jklfj', 'ali@gmail.com', '332610467', 'sdfsdf', 'sdf', '[\"Test Driving\"]', '[\"Insurance Expensive\"]', '2025-06-09 15:49:23', '2025-06-09 15:49:23'),
(6, 'dfhkjh', 'ali@gmail.com', '332610467', 'sdfasdf', 'sdfsdf', '[\"Penciling\"]', '[\"Wants to think about it\"]', '2025-06-09 15:50:25', '2025-06-09 15:50:25'),
(7, 'fkljal', 'ali@gmail.com', '332610467', 'fds', 'fdsaf', '[\"Credit Application\"]', '[\"Challenged Credit\"]', '2025-06-09 15:51:08', '2025-06-09 15:51:08'),
(8, 'Ifeoma Lynn', 'fesoqiki@mailinator.com', '+1 (481) 757-6503', 'Eu obcaecati impedit', 'Deserunt exercitatio', '[\"Test Driving\", \"Desking\", \"F&I\"]', '[\"Sold!\", \"Walked Away\", \"Wants to think about it\", \"Needs Co-Signer\"]', '2025-06-11 01:50:41', '2025-06-11 01:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2022_12_14_083707_create_settings_table', 1),
(5, '2024_04_02_140943_create_permission_tables', 1),
(6, '2025_04_02_140655_create_personal_access_tokens_table', 1),
(7, '2025_06_05_064958_create_queues_table', 1),
(8, '2025_06_05_071047_create_tokens_table', 1),
(9, '2025_06_05_175936_add_assigned_completed_timestamps_to_tokens_table', 1),
(10, '2025_06_05_203809_add_counter_number_to_users_table', 1),
(11, '2025_06_09_102201_create_customer_sales_table', 1),
(12, '2025_06_09_172040_create_appointments_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view any', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(2, 'view own', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(3, 'create', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(4, 'edit', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(5, 'delete', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(6, 'manage', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(7, 'view users', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(8, 'create users', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(9, 'edit users', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(10, 'delete users', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(11, 'manage users', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(12, 'view roles', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(13, 'create roles', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(14, 'edit roles', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(15, 'delete roles', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(16, 'assign roles', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(17, 'view permissions', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(18, 'manage permissions', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(19, 'view vehicles', 'web', '2025-06-09 05:45:21', '2025-06-09 05:45:21'),
(20, 'create vehicles', 'web', '2025-06-09 05:45:21', '2025-06-09 05:45:21'),
(21, 'edit vehicles', 'web', '2025-06-09 05:45:21', '2025-06-09 05:45:21'),
(22, 'delete vehicles', 'web', '2025-06-09 05:45:21', '2025-06-09 05:45:21'),
(23, 'manage settings', 'web', '2025-06-09 05:45:21', '2025-06-09 05:45:21');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_checked_in` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `checked_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `user_id`, `is_checked_in`, `checked_in_at`, `checked_out_at`, `created_at`, `updated_at`) VALUES
(1, 3, 0, '2025-06-09 05:50:18', '2025-06-11 03:17:40', '2025-06-09 05:50:18', '2025-06-11 03:17:40'),
(2, 4, 1, '2025-06-09 05:50:27', NULL, '2025-06-09 05:50:27', '2025-06-09 05:50:27'),
(3, 6, 1, '2025-06-09 05:50:44', NULL, '2025-06-09 05:50:44', '2025-06-09 05:50:44'),
(4, 5, 1, '2025-06-09 05:50:54', NULL, '2025-06-09 05:50:54', '2025-06-09 05:50:54'),
(5, 3, 1, '2025-06-11 03:17:44', NULL, '2025-06-11 03:17:44', '2025-06-11 03:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2025-06-09 05:43:35', '2025-06-09 05:43:35'),
(2, 'Sales Manager', 'web', '2025-06-09 05:43:36', '2025-06-09 05:43:36'),
(3, 'Sales person', 'web', '2025-06-09 05:43:36', '2025-06-09 05:43:36');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8IsDJnzVKdkUz0bTLAUjdmoZXU2kEP3hNdQjteWo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRkxIenV2c1R1NUppR1BPTzdsRjVTVEJZY3RrZjladUc2cjE5VWNhTyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cDovL3NhbGVzcXVldWUudGVzdC9hcHBvaW50bWVudHMvY3JlYXRlIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3QvYXBwb2ludG1lbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749627946),
('dwCAiN8HNO1G2w4pcdoBAJE9wRNzMgWAjOCT5Mc7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQUc1TDBmM2VYTDhrWTJHMkFCdjRydzZZbmxFTUl0bUFxTENSNURWOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3QvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749627655),
('EP6Bc0pPXVqv6sT2IsHhhPD9Wi13IHlN2nTAN1Cy', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjN2VEZua1VpdDkwZW4zcmhiMGFxeFhkTW9vUkdmbFBTSkxURHNwWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3QvY3JlYXRlL3NhbGVwZXJzb24iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1749628713),
('rgGRgMLznZJp3ZpDUk7S6WTnxG40NVxTBhSunoug', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWY5ZmRHYTV2M1JDT01ycnRvMVZGZGVUb2xOaHYyY0VNNUxUZHpFMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3QvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749627948),
('sT1ZJJmZBeBadNYaw1Lp1f0YK1YWwGfMqAHxv5fJ', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicU53MXZTUlZQaW9NTnRmUk05WkltYkw4SnpGMGlGdmU0UVRzOGpnSCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3Qvc2FsZXMvYWN0aXZpdHktcmVwb3J0Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1749630319),
('ZbpDL27l6VPOHu32dGgJWTVls3wBRgamkOSyb9U0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUN4Q3lDS016TnlYeTNBc09reW9WanlTbjJwb1NETDFIeDJoam9lUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9zYWxlc3F1ZXVlLnRlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749627653);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `serial_number` int NOT NULL,
  `status` enum('pending','assigned','skipped','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `serial_number`, `status`, `assigned_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'completed', '2025-06-09 05:51:04', '2025-06-09 06:12:09', '2025-06-09 05:51:04', '2025-06-09 06:12:09'),
(2, 4, 2, 'skipped', '2025-06-09 05:51:24', NULL, '2025-06-09 05:51:24', '2025-06-09 06:19:38'),
(3, 5, 3, 'completed', '2025-06-09 05:51:53', '2025-06-09 06:19:28', '2025-06-09 05:51:53', '2025-06-09 06:19:28'),
(4, 6, 4, 'completed', '2025-06-09 05:52:02', '2025-06-09 06:19:22', '2025-06-09 05:52:02', '2025-06-09 06:19:22'),
(5, 3, 5, 'completed', '2025-06-09 06:12:09', '2025-06-09 06:19:15', '2025-06-09 05:52:11', '2025-06-09 06:19:15'),
(6, 3, 6, 'assigned', '2025-06-09 06:19:50', NULL, '2025-06-09 06:19:50', '2025-06-09 06:19:50'),
(7, 4, 7, 'completed', '2025-06-09 06:24:20', '2025-06-09 06:31:17', '2025-06-09 06:24:20', '2025-06-09 06:31:17'),
(8, 5, 8, 'assigned', '2025-06-09 06:25:05', NULL, '2025-06-09 06:25:05', '2025-06-09 06:25:05'),
(9, 6, 9, 'skipped', '2025-06-09 06:29:06', NULL, '2025-06-09 06:29:06', '2025-06-09 06:29:37'),
(10, 6, 10, 'assigned', '2025-06-09 06:29:54', NULL, '2025-06-09 06:29:54', '2025-06-09 06:29:54'),
(11, 4, 11, 'assigned', '2025-06-09 06:31:17', NULL, '2025-06-09 06:29:58', '2025-06-09 06:31:17'),
(12, 3, 12, 'pending', NULL, NULL, '2025-06-11 03:17:40', '2025-06-11 03:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `counter_number`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, 'admin@admin.com', '2025-06-09 05:43:36', '$2y$12$7hjhHr.IR0v5bV9aVPyLR.CZl8EhcaT8OtQIWTmPGEl.FFyZgiK8e', NULL, '2025-06-09 05:43:36', '2025-06-09 05:43:36'),
(2, 'Sales Manager', NULL, 'salesmanager@salesmanager.com', '2025-06-09 05:43:36', '$2y$12$/Z9FPCVHI.Yq9O.TmL13tO9RM53VZqCcfE8p6LNwxlYbSjmz46UxG', 'IuuB6kQ3K8J6zxLIxvAB3obxiAw4v4aAvmie5SrDbNAqpenLMxh7pWf9uvDA', '2025-06-09 05:43:36', '2025-06-09 05:43:36'),
(3, 'Sales Person 1', '1', 'salesperson1@sales.com', '2025-06-09 05:45:20', '$2y$12$qmawPD/Xtf0ehTat39BkE.VgcTuYWD74RD/4zxSImeAm//DyfEVAa', 'K3GClET3VR542yM4YXkwTfYIyxGBrgFBAcinrMYz0qA1fEMvievV5KAKAXIz', '2025-06-09 05:45:20', '2025-06-09 05:45:20'),
(4, 'Sales Person 2', '2', 'salesperson2@sales.com', '2025-06-09 05:45:20', '$2y$12$erbRIM98jJY9QwrdD.AKp.9lGsvPd5qvZMxWIlvHRia3WGmL7jt7e', NULL, '2025-06-09 05:45:20', '2025-06-09 05:45:20'),
(5, 'Sales Person 3', '3', 'salesperson3@sales.com', '2025-06-09 05:45:21', '$2y$12$FoClvXG.fW1sVas0ZSZCMua4W6pe2pFQs5MweoUEdnvfkEsfWC8n6', 'rDjQRsqJLHIBV5p00Sy2aXU8KjD76VSZHqdfgjES18jJSsIbRPjQ1ljZ6JcG', '2025-06-09 05:45:21', '2025-06-09 05:45:21'),
(6, 'Sales Person 4', '4', 'salesperson4@sales.com', '2025-06-09 05:45:21', '$2y$12$CJKhQ.rOaHYsj27Y19IQ8OY1R7TJxIBzOUdsV5q9rx7R4WGseyWUW', '4j8E0MoPFDRNsYTp54W5Xm83jwJN4CtPW2Kc6OykUeSsAGgpAGDZZxv8OQkO', '2025-06-09 05:45:21', '2025-06-09 05:45:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_created_by_foreign` (`created_by`),
  ADD KEY `appointments_salesperson_id_foreign` (`salesperson_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customer_sales`
--
ALTER TABLE `customer_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `queues_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_group_name_unique` (`group`,`name`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tokens_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_sales`
--
ALTER TABLE `customer_sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_salesperson_id_foreign` FOREIGN KEY (`salesperson_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queues`
--
ALTER TABLE `queues`
  ADD CONSTRAINT `queues_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
