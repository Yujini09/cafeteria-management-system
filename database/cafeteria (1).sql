-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 02:32 PM
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
-- Database: `cafeteria`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_trails`
--

CREATE TABLE `audit_trails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_trails`
--

INSERT INTO `audit_trails` (`id`, `user_id`, `action`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Logged in', 'auth', 'User logged in successfully', '2025-10-01 10:32:45', '2025-10-01 10:32:45'),
(2, 1, 'Created Admin', 'users', 'Created admin admin@example.com', '2025-10-01 10:33:18', '2025-10-01 10:33:18'),
(3, 1, 'Logged out', 'auth', 'User logged out successfully', '2025-10-01 10:33:24', '2025-10-01 10:33:24'),
(4, 8, 'Logged out', 'auth', 'User logged out successfully', '2025-10-01 10:45:30', '2025-10-01 10:45:30'),
(5, 8, 'Logged in', 'auth', 'User logged in successfully', '2025-10-01 10:54:45', '2025-10-01 10:54:45'),
(6, 8, 'Logged out', 'auth', 'User logged out successfully', '2025-10-01 11:32:23', '2025-10-01 11:32:23'),
(7, 7, 'Logged in', 'auth', 'User logged in successfully', '2025-10-01 11:32:42', '2025-10-01 11:32:42'),
(8, 7, 'Updated password', 'users', 'User admin@example.com updated their password.', '2025-10-01 11:51:56', '2025-10-01 11:51:56'),
(9, 7, 'Logged out', 'auth', 'User logged out successfully', '2025-10-01 11:52:06', '2025-10-01 11:52:06'),
(10, 7, 'Logged in', 'auth', 'User logged in successfully', '2025-10-01 11:55:24', '2025-10-01 11:55:24');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-admin@example.com|127.0.0.1', 'i:2;', 1759314812),
('laravel-cache-admin@example.com|127.0.0.1:timer', 'i:1759314812;', 1759314812),
('laravel-cache-admin1@example.com|::1', 'i:1;', 1759319772),
('laravel-cache-admin1@example.com|::1:timer', 'i:1759319772;', 1759319772);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `category` enum('Perishable','Condiments','Frozen','Beverages','Desserts','Others') NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `name`, `qty`, `unit`, `category`, `expiry_date`, `created_at`, `updated_at`) VALUES
(2, 'dwdwd', 4, 'Kgs', 'Perishable', NULL, '2025-10-01 11:44:35', '2025-10-01 11:44:35'),
(3, 'hgfhgc', 6, 'Pieces', 'Condiments', NULL, '2025-10-01 11:57:04', '2025-10-01 11:57:04');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `meal_time` enum('breakfast','am_snacks','lunch','pm_snacks','dinner') NOT NULL,
  `type` enum('standard','special') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `meal_time`, `type`, `price`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Breakfast • Standard • Menu 1', 'breakfast', 'standard', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(2, 'Breakfast • Standard • Menu 2', 'breakfast', 'standard', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(3, 'Breakfast • Standard • Menu 3', 'breakfast', 'standard', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(4, 'Breakfast • Standard • Menu 4', 'breakfast', 'standard', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(5, 'Breakfast • Special • Menu 1', 'breakfast', 'special', 0.00, 'Minimum of 10 pax • ₱170/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(6, 'Breakfast • Special • Menu 2', 'breakfast', 'special', 0.00, 'Minimum of 10 pax • ₱170/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(7, 'Breakfast • Special • Menu 3', 'breakfast', 'special', 0.00, 'Minimum of 10 pax • ₱170/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(8, 'Breakfast • Special • Menu 4', 'breakfast', 'special', 0.00, 'Minimum of 10 pax • ₱170/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(9, 'A.M. Snacks • Standard • Day 1', 'am_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(10, 'A.M. Snacks • Standard • Day 2', 'am_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(11, 'A.M. Snacks • Standard • Day 3', 'am_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(12, 'A.M. Snacks • Standard • Day 4', 'am_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(13, 'A.M. Snacks • Special • Day 1', 'am_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(14, 'A.M. Snacks • Special • Day 2', 'am_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(15, 'A.M. Snacks • Special • Day 3', 'am_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(16, 'A.M. Snacks • Special • Day 4', 'am_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(17, 'Lunch • Standard • Day 1', 'lunch', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(18, 'Lunch • Standard • Day 2', 'lunch', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(19, 'Lunch • Standard • Day 3', 'lunch', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(20, 'Lunch • Standard • Day 4', 'lunch', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(21, 'Lunch • Special • Day 1', 'lunch', 'special', 0.00, 'Minimum of 10 pax • ₱350/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(22, 'Lunch • Special • Day 2', 'lunch', 'special', 0.00, 'Minimum of 10 pax • ₱350/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(23, 'Lunch • Special • Day 3', 'lunch', 'special', 0.00, 'Minimum of 10 pax • ₱350/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(24, 'Lunch • Special • Day 4', 'lunch', 'special', 0.00, 'Minimum of 10 pax • ₱350/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(25, 'P.M. Snacks • Standard • Day 1', 'pm_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(26, 'P.M. Snacks • Standard • Day 2', 'pm_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(27, 'P.M. Snacks • Standard • Day 3', 'pm_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(28, 'P.M. Snacks • Standard • Day 4', 'pm_snacks', 'standard', 0.00, 'Minimum of 10 pax • ₱100/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(29, 'P.M. Snacks • Special • Day 1', 'pm_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(30, 'P.M. Snacks • Special • Day 2', 'pm_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(31, 'P.M. Snacks • Special • Day 3', 'pm_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(32, 'P.M. Snacks • Special • Day 4', 'pm_snacks', 'special', 0.00, 'Minimum of 10 pax • ₱150/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(33, 'Dinner • Standard • Day 1', 'dinner', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(34, 'Dinner • Standard • Day 2', 'dinner', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(35, 'Dinner • Standard • Day 3', 'dinner', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(36, 'Dinner • Standard • Day 4', 'dinner', 'standard', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(37, 'Dinner • Special • Day 1', 'dinner', 'special', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(38, 'Dinner • Special • Day 2', 'dinner', 'special', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(39, 'Dinner • Special • Day 3', 'dinner', 'special', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(40, 'Dinner • Special • Day 4', 'dinner', 'special', 0.00, 'Minimum of 10 pax • ₱300/head', '2025-10-01 10:24:37', '2025-10-01 10:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('meal','drink','dessert','other') NOT NULL DEFAULT 'other',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `menu_id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 'Longanisa w/ Slice Tomato', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(2, 1, 'Fried Egg Sunny Side Up', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(3, 1, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(4, 1, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(5, 1, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(6, 2, 'Pork Embutido', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(7, 2, 'Onion Omelet', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(8, 2, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(9, 2, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(10, 2, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(11, 3, 'Luncheon Meat', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(12, 3, 'Dilis w/ Chopped Tomato', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(13, 3, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(14, 3, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(15, 3, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(16, 4, 'Pork Tapa w/ Tomato', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(17, 4, 'Salted Egg', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(18, 4, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(19, 4, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(20, 4, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(21, 5, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(22, 5, 'Longanisa w/ Slice Tomato', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(23, 5, 'Boneless Daing na Bangus', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(24, 5, 'Mushroom Omelet', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(25, 5, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(26, 5, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(27, 5, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(28, 6, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(29, 6, 'Pork Omelet w/ Catsup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(30, 6, 'Fried Eggplant', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(31, 6, 'Toasted Bread w/ Jam&Butter', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(32, 6, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(33, 6, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(34, 6, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(35, 7, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(36, 7, 'Chicken Embutido', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(37, 7, 'Fried Sausage', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(38, 7, 'Fried Egg Sunny Side Up', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(39, 7, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(40, 7, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(41, 7, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(42, 8, 'Nilagang Saba', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(43, 8, 'Pork Tapa w/ Tomato', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(44, 8, 'Salted Egg', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(45, 8, 'Daing Dilis', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(46, 8, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(47, 8, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(48, 8, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(49, 9, 'Ham & Cheese Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(50, 9, 'Buko w/ Gulaman', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(51, 9, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(52, 9, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(53, 10, 'Pimiento Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(54, 10, 'Buko w/ Gulaman', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(55, 10, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(56, 10, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(57, 11, 'Chicken Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(58, 11, 'P/A Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(59, 11, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(60, 11, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(61, 12, 'Cheese Burger', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(62, 12, 'Iced Tea w/ Tanglad', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(63, 12, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(64, 12, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(65, 13, 'Lomi', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(66, 13, 'Puto Cheese', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(67, 13, 'Orange Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(68, 13, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(69, 13, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(70, 14, 'Bihon Guisado', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(71, 14, 'Kutsinta w/ Latik', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(72, 14, 'Buko Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(73, 14, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(74, 14, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(75, 15, 'Spaghetti w/ Meat Balls', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(76, 15, 'P/A Orange Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(77, 15, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(78, 15, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(79, 16, 'Carbonara w/ Chicken Fillet', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(80, 16, '4 Season Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(81, 16, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(82, 16, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(83, 17, 'Chickenn Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(84, 17, 'Pork Karekare w/ Binagoongan', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(85, 17, 'Lumpia Frito', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(86, 17, 'Bolabola w/ P/A Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(87, 17, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(88, 17, 'Molded Gulaman', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(89, 17, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(90, 18, 'Crab & Corn Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(91, 18, 'Pork w/ Mushroom', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(92, 18, 'Chinese Veg. w/ Quail Egg', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(93, 18, 'Fish Fillet w/ Sweet Chilli Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(94, 18, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(95, 18, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(96, 18, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(97, 19, 'Onion Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(98, 19, 'Cordon Bleu w/Creamy Mushroom-Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(99, 19, 'Pork Bistick', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(100, 19, 'Toge Guisado', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(101, 19, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(102, 19, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(103, 19, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(104, 20, 'Corn Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(105, 20, 'Pork Sarciado', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(106, 20, 'Gising-gising', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(107, 20, 'Fish Bolabola', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(108, 20, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(109, 20, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(110, 20, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(111, 21, 'Egg Drop Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(112, 21, 'Pork Caldereta', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(113, 21, 'Chinese Vegetables', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(114, 21, 'Sweet and Sour Fish', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(115, 21, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(116, 21, 'Leche Flan', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(117, 21, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(118, 22, 'Crab & Corn Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(119, 22, 'Steamed Veg. w/Butter Garlic Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(120, 22, 'Chicken Pork Adobo w/Coco Cream', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(121, 22, 'Fish Escabeche', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(122, 22, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(123, 22, 'Fruit Salad', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(124, 22, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(125, 23, 'Sinigang na Hipon', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(126, 23, 'Fried Chicken', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(127, 23, 'Gising-gising', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(128, 23, 'Slice Fruits', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(129, 23, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(130, 23, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(131, 24, 'Bolabola Fish w/ Misua', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(132, 24, 'Pork Karekare', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(133, 24, 'Lumpia Shanghai', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(134, 24, 'Breaded Chicken w/ P/A Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(135, 24, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(136, 24, 'Fruit Cocktail', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(137, 24, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(138, 25, 'Cheese Burger Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(139, 25, 'Sago\'t Gulaman', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(140, 25, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(141, 25, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(142, 26, 'Chicken Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(143, 26, 'P/A Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(144, 26, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(145, 26, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(146, 27, 'Tuna Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(147, 27, 'Iced Tea', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(148, 27, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(149, 27, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(150, 28, 'Cheese Pimiento Sandwich', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(151, 28, 'Black Gulaman', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(152, 28, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(153, 28, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(154, 29, 'Carbonara', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(155, 29, 'Toasted Bread', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(156, 29, '4 Season Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(157, 29, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(158, 29, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(159, 30, 'Sotanghon Guisado', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(160, 30, 'Maja', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(161, 30, 'Iced Tea w/ Lemon', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(162, 30, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(163, 30, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(164, 31, 'Bihon Guisado', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(165, 31, 'Kutsinta w/ Latik', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(166, 31, 'Sago\'t Gulaman', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(167, 31, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(168, 31, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(169, 32, 'Spaghetti', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(170, 32, 'Garlic Bread', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(171, 32, 'P/A Juice', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(172, 32, 'Tea/Coffee', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(173, 32, 'Distilled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(174, 33, 'Egg Drop Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(175, 33, 'Mushroom Cabbage w/ Pork Balls', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(176, 33, 'Chicken Caldereta', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(177, 33, 'Fried Tilapia', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(178, 33, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(179, 33, 'Banana', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(180, 33, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(181, 34, 'Bolabola w/ Misua', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(182, 34, 'Breaded Pork', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(183, 34, 'Bean w/ Ham Strips', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(184, 34, 'Fried Bangus', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(185, 34, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(186, 34, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(187, 34, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(188, 35, 'Batchoy w/ Meat', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(189, 35, 'Pinakbet', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(190, 35, 'Fried Hito', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(191, 35, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(192, 35, 'Leche Flan', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(193, 35, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(194, 36, 'Chicken Tinola', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(195, 36, 'Inihaw na Tilapia', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(196, 36, 'Lagalaga Veg. w/ Buro', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(197, 36, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(198, 36, 'Fruit Salad', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(199, 36, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(200, 37, 'Chicken Swam', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(201, 37, 'Broiled Fish w/ Mango Salad', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(202, 37, 'Lechon Kawali w/ Sauce', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(203, 37, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(204, 37, 'Lagalaga Veg. Delight w/ Buro', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(205, 37, 'Banana', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(206, 37, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(207, 38, 'Sinampalukang Manok', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(208, 38, 'Pork Sisig', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(209, 38, 'Pinakbet', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(210, 38, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(211, 38, 'Fruit in Season', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(212, 38, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(213, 39, 'Batchoy Soup', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(214, 39, 'Fried Tilapia w/ Mango Sisig', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(215, 39, 'Broiled Eggplant w/ Binagoongan', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(216, 39, 'Chicken Barbeque', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(217, 39, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(218, 39, 'Buko Pandan', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(219, 39, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(220, 40, 'Tinolang Manok', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(221, 40, 'Pork Asado Chinese Style', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(222, 40, 'Relleno Bangus', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(223, 40, 'Rice', 'meal', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(224, 40, 'Sweetened Banana', 'dessert', '2025-10-01 10:24:37', '2025-10-01 10:24:37'),
(225, 40, 'Bottled Water', 'drink', '2025-10-01 10:24:37', '2025-10-01 10:24:37');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_12_143707_create_permission_tables', 1),
(5, '2025_09_12_144001_create_reservations_table', 1),
(6, '2025_09_12_144002_create_menus_table', 1),
(7, '2025_09_12_144004_create_inventory_items_table', 1),
(8, '2025_09_12_144006_create_payments_table', 1),
(9, '2025_09_12_144011_create_reports_table', 1),
(10, '2025_09_13_103208_create_audit_trails_table', 1),
(11, '2025_09_14_040849_create_menu_items_table', 1),
(12, '2025_09_14_040940_create_recipes_table', 1),
(13, '2025_09_26_065119_create_reservation_items_table', 1),
(14, '2025_09_26_071731_add_reservation_fk_to_reservation_items', 1),
(15, '2025_09_26_112441_add_meal_time_to_menus', 1),
(16, '2025_09_26_113616_add_name_to_menus', 1),
(17, '2025_09_27_124000_add_default_to_number_in_menus_table', 1),
(18, '2025_09_27_133228_add_decline_reason_to_reservations', 1),
(19, '2025_09_28_083535_drop_menu_type_and_day_no_from_menus_table', 1),
(20, '2025_09_28_091150_drop_number_from_menus_table', 1),
(21, '2025_10_01_174132_2025_09_26_065119_create_reservation_items_table', 1),
(22, '2025_10_01_174206_2025_09_26_065119_create_reservation_items_table', 1),
(23, '2025_10_01_182040_2025_09_14_040849_create_menu_items_table', 1),
(24, '2025_10_01_192700_add_foreign_keys_to_reservation_items_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
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

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('allennepomuceno8@gmail.com', '$2y$12$deTE4003KNoL4ECUZg5dsuSL/0L9tRsaddCFIkysRamCyKxa3VbP.', '2025-10-01 11:53:04');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_needed` decimal(10,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `number_of_persons` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `guests` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `decline_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `event_name`, `event_date`, `event_time`, `number_of_persons`, `special_requests`, `date`, `time`, `guests`, `status`, `decline_reason`, `created_at`, `updated_at`) VALUES
(1, 8, 'Catering Reservation', '2025-10-01', '12:00:00', 0, NULL, NULL, NULL, NULL, 'pending', NULL, '2025-10-01 11:05:25', '2025-10-01 11:05:25'),
(2, 8, 'Catering Reservation', '2025-10-01', '12:00:00', 0, NULL, NULL, NULL, NULL, 'pending', NULL, '2025-10-01 11:23:16', '2025-10-01 11:23:16'),
(3, 8, 'Catering Reservation', '2025-10-01', '12:00:00', 40, 'wdrefcevf', NULL, NULL, NULL, 'pending', NULL, '2025-10-01 11:24:29', '2025-10-01 11:24:29'),
(4, 8, 'Catering Reservation', '2025-10-01', '12:00:00', 27, NULL, NULL, NULL, NULL, 'approved', NULL, '2025-10-01 11:25:03', '2025-10-01 11:33:58'),
(5, 8, 'Catering Reservation', '2025-10-01', '12:00:00', 0, 'zfdgvdrfre', NULL, NULL, NULL, 'declined', 'kinginaka', '2025-10-01 11:31:52', '2025-10-01 11:33:15');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_items`
--

CREATE TABLE `reservation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('519hn6NNP9Yw8DR4kH1x8ytbXHLqZMT85dvQWot7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkRjdkdOVEtLNDlPUHdOaXlNNjhJZmhuZkFrRXBMRXVqdHMwMDlmSyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3N1cGVyYWRtaW4vdXNlcnMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1759315065),
('BPndHR17VHmu4QmBxykbsBZsAD1qtu7jMEQOyMf6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicWh5V2wxNmZrQXVoUmlpb0RUQURNVFNGOVdFMUNYYmtZNDFOanU5TyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759315080),
('IFIRtj5VZuDO2c3yMezqXu2k7iWv1AftOEXsFhjT', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNFB5aUM4cTh4SUhpUXhFelNRS3JTdUUyUnZTU3BQU2taUFQ2ckREbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly9sb2NhbGhvc3QvY2FmZXRlcmlhLXN5c3RlbS9wdWJsaWMvYWRtaW4vZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1759321403),
('StVyg3sa9eHkQHr1L1YNArX3khyTblExgkt228jG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkdIRnplclhWVUNvNk43VlEwc2hSZER5eGNNOGpiSkYyVlJLTURlViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zdXBlcmFkbWluL3VzZXJzIjt9fQ==', 1759314807);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `contact_no`, `department`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', NULL, '09123456789', NULL, 'superadmin@example.com', NULL, '$2y$12$wTzFVoZEwwYl8X6ELB692uVKvfIafhaMlQZ3h8OE6l8/JRdlqnrgK', 'superadmin', NULL, '2025-10-01 10:23:45', '2025-10-01 10:23:45'),
(2, 'Customer 1', NULL, '09123456781', NULL, 'customer1@example.com', NULL, '$2y$12$/xLmLTZA4OFntXUsOE2Js.P9CGpvpigzTK5pWEr.ulh67.KQGFHO6', 'customer', NULL, '2025-10-01 10:23:46', '2025-10-01 10:23:46'),
(3, 'Customer 2', NULL, '09123456782', NULL, 'customer2@example.com', NULL, '$2y$12$HgRZcjzNMi1vbRV4OoIIke8n.kGL9b4fAAMeYN7MBfoaJdXv4vsOW', 'customer', NULL, '2025-10-01 10:23:46', '2025-10-01 10:23:46'),
(4, 'Customer 3', NULL, '09123456783', NULL, 'customer3@example.com', NULL, '$2y$12$8T39GCb0m2/EIACdaskkYeihriyIB1NeRcHInsy8eKbK/ik3Qscii', 'customer', NULL, '2025-10-01 10:23:47', '2025-10-01 10:23:47'),
(5, 'Customer 4', NULL, '09123456784', NULL, 'customer4@example.com', NULL, '$2y$12$YLYyA5Vk33He67z1ZbJTtu/JLqGe1o2fKGCAfwiekICCyI.6SJU1C', 'customer', NULL, '2025-10-01 10:23:47', '2025-10-01 10:23:47'),
(6, 'Customer 5', NULL, '09123456785', NULL, 'customer5@example.com', NULL, '$2y$12$XkbrF/0bWpo1.1190ALktuQyuUO69q39dggaiZkMIV87G6ZEv8bLq', 'customer', NULL, '2025-10-01 10:23:48', '2025-10-01 10:23:48'),
(7, 'Admin One', NULL, NULL, NULL, 'admin@example.com', NULL, '$2y$12$8S14.bmSkv63H1YzcHj2BuWw3bPe21z9gvFeqW5Ya1xRVHIvq8st6', 'admin', NULL, '2025-10-01 10:33:18', '2025-10-01 11:51:56'),
(8, 'Johnella Nepomuceno', 'Agupalo weste, Lupao, Nueva Ecija', '09641973262', 'allennepomuceno8@gmail.com', 'allennepomuceno8@gmail.com', NULL, '$2y$12$YBZoROLxZLO9uxqPDxl4EuBxa6lyrSmNid2yNO3x2D1dVFMcU/srG', 'customer', NULL, '2025-10-01 10:44:52', '2025-10-01 10:44:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_trails`
--
ALTER TABLE `audit_trails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_trails_user_id_foreign` (`user_id`);

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_items_menu_id_name_unique` (`menu_id`,`name`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recipes_menu_item_id_inventory_item_id_unique` (`menu_item_id`,`inventory_item_id`),
  ADD KEY `recipes_inventory_item_id_foreign` (`inventory_item_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_user_id_foreign` (`user_id`);

--
-- Indexes for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_items_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_items_menu_id_foreign` (`menu_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_trails`
--
ALTER TABLE `audit_trails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservation_items`
--
ALTER TABLE `reservation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_trails`
--
ALTER TABLE `audit_trails`
  ADD CONSTRAINT `audit_trails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD CONSTRAINT `reservation_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_items_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
