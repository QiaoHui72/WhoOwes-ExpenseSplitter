-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 08:57 PM
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
-- Database: `whoowes`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `paid_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'general',
  `include_sst` tinyint(1) NOT NULL DEFAULT 0,
  `include_service_charge` tinyint(1) NOT NULL DEFAULT 0,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `split_method` enum('equal','exact','percentage','shares') NOT NULL DEFAULT 'equal',
  `expense_date` date NOT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `group_id`, `paid_by`, `title`, `description`, `amount`, `category`, `include_sst`, `include_service_charge`, `tax_amount`, `total_amount`, `split_method`, `expense_date`, `receipt_url`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Grocery Run (Aeon)', 'Monthly grocery shopping', 230.00, 'shopping', 0, 0, 0.00, 230.00, 'equal', '2025-04-01', NULL, '2025-04-01 02:00:00', '2025-04-01 02:00:00'),
(2, 1, 2, 'Internet Bill (Unifi)', 'April internet subscription', 99.00, 'utilities', 0, 0, 0.00, 99.00, 'equal', '2025-04-02', NULL, '2025-04-02 03:00:00', '2025-04-02 03:00:00'),
(3, 1, 3, 'Electricity Bill (TNB)', 'April electricity', 185.50, 'utilities', 0, 0, 0.00, 185.50, 'equal', '2025-04-05', NULL, '2025-04-05 01:30:00', '2025-04-05 01:30:00'),
(4, 1, 1, 'House Cleaning Supplies', 'Broom, detergent, mop', 65.80, 'shopping', 0, 0, 0.00, 65.80, 'equal', '2025-04-10', NULL, '2025-04-10 06:00:00', '2025-04-10 06:00:00'),
(5, 1, 4, 'Petrol for Housemate Outing', 'Petrol to Mid Valley', 120.00, 'transport', 0, 0, 0.00, 120.00, 'equal', '2025-04-14', NULL, '2025-04-14 10:00:00', '2025-04-14 10:00:00'),
(6, 1, 2, 'Water Bill (SYABAS)', 'April water bill', 42.00, 'utilities', 0, 0, 0.00, 42.00, 'equal', '2025-04-20', NULL, '2025-04-20 02:00:00', '2025-04-20 02:00:00'),
(7, 1, 1, 'Grocery Run (Jaya Grocer)', 'May groceries', 310.50, 'shopping', 0, 0, 0.00, 310.50, 'equal', '2025-05-03', NULL, '2025-05-03 03:00:00', '2025-05-03 03:00:00'),
(8, 1, 3, 'Astro Subscription', 'May TV subscription', 79.90, 'entertainment', 0, 0, 0.00, 79.90, 'equal', '2025-05-05', NULL, '2025-05-05 01:00:00', '2025-05-05 01:00:00'),
(9, 2, 3, 'Airbnb Booking', '2 nights at George Town Airbnb', 1200.00, 'travel', 0, 0, 0.00, 1200.00, 'equal', '2025-10-25', NULL, '2025-10-25 00:00:00', '2025-10-25 00:00:00'),
(10, 2, 1, 'Dinner at Gurney Drive', 'Famous hawker stalls', 245.00, 'food', 1, 1, 44.10, 289.10, 'equal', '2025-10-25', NULL, '2025-10-25 11:00:00', '2025-10-25 11:00:00'),
(11, 2, 2, 'Penang Hill Cable Car', '4 return tickets', 108.00, 'travel', 0, 0, 0.00, 108.00, 'equal', '2025-10-26', NULL, '2025-10-26 02:00:00', '2025-10-26 02:00:00'),
(12, 2, 4, 'Souvenirs at Chowrasta', 'Batik, keropok, nutmeg products', 80.00, 'shopping', 0, 0, 0.00, 80.00, 'equal', '2025-10-26', NULL, '2025-10-26 07:00:00', '2025-10-26 07:00:00'),
(13, 2, 1, 'Lunch at Kafe Pullman', 'Char kway teow & nasi kandar', 165.00, 'food', 1, 1, 29.70, 194.70, 'equal', '2025-10-26', NULL, '2025-10-26 05:00:00', '2025-10-26 05:00:00'),
(14, 2, 3, 'Fuel & Toll (KL to Penang)', 'North-South Highway toll + petrol', 220.00, 'transport', 0, 0, 0.00, 220.00, 'equal', '2025-10-25', NULL, '2025-10-24 23:00:00', '2025-10-24 23:00:00'),
(15, 2, 2, 'Street Food Tour', 'Assam laksa, cendol, rojak', 96.00, 'food', 0, 0, 0.00, 96.00, 'equal', '2025-10-27', NULL, '2025-10-27 03:00:00', '2025-10-27 03:00:00'),
(16, 2, 1, 'Fort Cornwallis Entry', '4 tickets', 28.00, 'travel', 0, 0, 0.00, 28.00, 'equal', '2025-10-27', NULL, '2025-10-27 06:00:00', '2025-10-27 06:00:00'),
(17, 3, 2, 'Nescafe & Milo (Monthly)', 'Office pantry drinks Feb', 55.00, 'food', 0, 0, 0.00, 55.00, 'equal', '2025-02-01', NULL, '2025-02-01 01:00:00', '2025-02-01 01:00:00'),
(18, 3, 5, 'Sugar, Creamer, Biscuits', 'Pantry restocking', 38.50, 'food', 0, 0, 0.00, 38.50, 'equal', '2025-02-14', NULL, '2025-02-14 02:00:00', '2025-02-14 02:00:00'),
(19, 3, 6, 'Coffee Machine Filter', 'Replacement filters', 22.00, 'utilities', 0, 0, 0.00, 22.00, 'equal', '2025-02-20', NULL, '2025-02-20 03:00:00', '2025-02-20 03:00:00'),
(20, 3, 7, 'Team Lunch (Nasi Lemak)', 'Restoran Nasi Lemak Antarabangsa', 48.00, 'food', 1, 0, 3.84, 51.84, 'equal', '2025-03-01', NULL, '2025-03-01 05:00:00', '2025-03-01 05:00:00'),
(21, 3, 2, 'Nescafe & Milo (Monthly)', 'Office pantry drinks Mar', 55.00, 'food', 0, 0, 0.00, 55.00, 'equal', '2025-03-01', NULL, '2025-03-01 01:00:00', '2025-03-01 01:00:00'),
(22, 3, 5, 'Birthday Cake (Kenji)', 'Strawberry cake from Secret Recipe', 85.00, 'food', 0, 0, 0.00, 85.00, 'equal', '2025-03-15', NULL, '2025-03-15 08:00:00', '2025-03-15 08:00:00'),
(23, 4, 1, 'January Rent', 'Monthly rent for unit B-12', 1500.00, 'rent', 0, 0, 0.00, 1500.00, 'equal', '2026-01-01', NULL, '2025-12-31 23:00:00', '2025-12-31 23:00:00'),
(24, 4, 3, 'February Rent', 'Monthly rent for unit B-12', 1500.00, 'rent', 0, 0, 0.00, 1500.00, 'equal', '2025-02-01', NULL, '2026-01-31 23:00:00', '2026-01-31 23:00:00'),
(25, 4, 1, 'March Rent', 'Monthly rent for unit B-12', 1500.00, 'rent', 0, 0, 0.00, 1500.00, 'equal', '2025-03-01', NULL, '2025-02-29 23:00:00', '2025-02-29 23:00:00'),
(26, 4, 3, 'April Rent', 'Monthly rent for unit B-12', 1500.00, 'rent', 0, 0, 0.00, 1500.00, 'equal', '2025-04-01', NULL, '2025-03-31 23:00:00', '2025-03-31 23:00:00'),
(27, 4, 1, 'Parking Season Pass', 'Covered parking Jan-Jun', 480.00, 'transport', 0, 0, 0.00, 480.00, 'equal', '2026-01-05', NULL, '2026-01-05 01:00:00', '2026-01-05 01:00:00'),
(28, 5, 5, 'Resorts World Hotel 2N', '2 nights Twin Room deluxe', 820.00, 'travel', 0, 0, 0.00, 820.00, 'equal', '2026-01-01', NULL, '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(29, 5, 1, 'SkyAvenue Dinner', 'New Year dinner buffet x5', 475.00, 'food', 1, 1, 85.50, 560.50, 'equal', '2026-01-01', NULL, '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(30, 5, 6, 'Genting Awana Cable Car', '5 return tickets', 150.00, 'travel', 0, 0, 0.00, 150.00, 'equal', '2026-01-02', NULL, '2026-01-02 02:00:00', '2026-01-02 02:00:00'),
(31, 5, 8, 'Breakfast at Coffee Terrace', 'Buffet breakfast x5 for 2 mornings', 250.00, 'food', 1, 1, 45.00, 295.00, 'equal', '2026-01-02', NULL, '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(32, 5, 7, 'Petrol & Toll (KL-Genting)', 'Return trip fuel & highway toll', 90.00, 'transport', 0, 0, 0.00, 90.00, 'equal', '2026-01-01', NULL, '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(33, 5, 5, 'Theme Park Tickets', 'Outdoor Theme Park x5', 375.00, 'entertainment', 0, 0, 0.00, 375.00, 'equal', '2026-01-02', NULL, '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(34, 2, 1, 'dinner', NULL, 0.00, 'food', 0, 0, 0.00, 0.00, 'equal', '2026-04-17', NULL, '2026-04-17 09:19:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_splits`
--

CREATE TABLE `expense_splits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `shares` int(10) UNSIGNED DEFAULT NULL,
  `is_settled` tinyint(1) NOT NULL DEFAULT 0,
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_splits`
--

INSERT INTO `expense_splits` (`id`, `expense_id`, `user_id`, `amount`, `percentage`, `shares`, `is_settled`, `settled_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 57.50, 25.00, NULL, 1, '2025-04-01 02:00:00', '2025-04-01 02:00:00', '2025-04-01 02:00:00'),
(2, 1, 2, 57.50, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-01 02:00:00', '2025-04-15 01:00:00'),
(3, 1, 3, 57.50, 25.00, NULL, 1, '2025-04-16 02:00:00', '2025-04-01 02:00:00', '2025-04-16 02:00:00'),
(4, 1, 4, 57.50, 25.00, NULL, 0, NULL, '2025-04-01 02:00:00', '2025-04-01 02:00:00'),
(5, 2, 1, 24.75, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-02 03:00:00', '2025-04-15 01:00:00'),
(6, 2, 2, 24.75, 25.00, NULL, 1, '2025-04-02 03:00:00', '2025-04-02 03:00:00', '2025-04-02 03:00:00'),
(7, 2, 3, 24.75, 25.00, NULL, 1, '2025-04-16 02:00:00', '2025-04-02 03:00:00', '2025-04-16 02:00:00'),
(8, 2, 4, 24.75, 25.00, NULL, 0, NULL, '2025-04-02 03:00:00', '2025-04-02 03:00:00'),
(9, 3, 1, 46.38, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-05 01:30:00', '2025-04-15 01:00:00'),
(10, 3, 2, 46.38, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-05 01:30:00', '2025-04-15 01:00:00'),
(11, 3, 3, 46.37, 25.00, NULL, 1, '2025-04-05 01:30:00', '2025-04-05 01:30:00', '2025-04-05 01:30:00'),
(12, 3, 4, 46.37, 25.00, NULL, 0, NULL, '2025-04-05 01:30:00', '2025-04-05 01:30:00'),
(13, 4, 1, 16.45, 25.00, NULL, 1, '2025-04-10 06:00:00', '2025-04-10 06:00:00', '2025-04-10 06:00:00'),
(14, 4, 2, 16.45, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-10 06:00:00', '2025-04-15 01:00:00'),
(15, 4, 3, 16.45, 25.00, NULL, 1, '2025-04-16 02:00:00', '2025-04-10 06:00:00', '2025-04-16 02:00:00'),
(16, 4, 4, 16.45, 25.00, NULL, 0, NULL, '2025-04-10 06:00:00', '2025-04-10 06:00:00'),
(17, 5, 1, 30.00, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-14 10:00:00', '2025-04-15 01:00:00'),
(18, 5, 2, 30.00, 25.00, NULL, 1, '2025-04-15 01:00:00', '2025-04-14 10:00:00', '2025-04-15 01:00:00'),
(19, 5, 3, 30.00, 25.00, NULL, 1, '2025-04-16 02:00:00', '2025-04-14 10:00:00', '2025-04-16 02:00:00'),
(20, 5, 4, 30.00, 25.00, NULL, 1, '2025-04-14 10:00:00', '2025-04-14 10:00:00', '2025-04-14 10:00:00'),
(21, 6, 1, 10.50, 25.00, NULL, 1, '2025-04-22 01:00:00', '2025-04-20 02:00:00', '2025-04-22 01:00:00'),
(22, 6, 2, 10.50, 25.00, NULL, 1, '2025-04-20 02:00:00', '2025-04-20 02:00:00', '2025-04-20 02:00:00'),
(23, 6, 3, 10.50, 25.00, NULL, 1, '2025-04-22 02:00:00', '2025-04-20 02:00:00', '2025-04-22 02:00:00'),
(24, 6, 4, 10.50, 25.00, NULL, 0, NULL, '2025-04-20 02:00:00', '2025-04-20 02:00:00'),
(25, 7, 1, 77.63, 25.00, NULL, 1, '2025-05-03 03:00:00', '2025-05-03 03:00:00', '2025-05-03 03:00:00'),
(26, 7, 2, 77.63, 25.00, NULL, 0, NULL, '2025-05-03 03:00:00', '2025-05-03 03:00:00'),
(27, 7, 3, 77.63, 25.00, NULL, 0, NULL, '2025-05-03 03:00:00', '2025-05-03 03:00:00'),
(28, 7, 4, 77.61, 25.00, NULL, 0, NULL, '2025-05-03 03:00:00', '2025-05-03 03:00:00'),
(29, 8, 1, 19.98, 25.00, NULL, 0, NULL, '2025-05-05 01:00:00', '2025-05-05 01:00:00'),
(30, 8, 2, 19.98, 25.00, NULL, 0, NULL, '2025-05-05 01:00:00', '2025-05-05 01:00:00'),
(31, 8, 3, 19.97, 25.00, NULL, 1, '2025-05-05 01:00:00', '2025-05-05 01:00:00', '2025-05-05 01:00:00'),
(32, 8, 4, 19.97, 25.00, NULL, 0, NULL, '2025-05-05 01:00:00', '2025-05-05 01:00:00'),
(33, 9, 1, 300.00, 25.00, NULL, 0, NULL, '2025-10-25 00:00:00', '2025-10-25 00:00:00'),
(34, 9, 2, 300.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-25 00:00:00', '2025-11-01 02:00:00'),
(35, 9, 3, 300.00, 25.00, NULL, 1, '2025-10-25 00:00:00', '2025-10-25 00:00:00', '2025-10-25 00:00:00'),
(36, 9, 4, 300.00, 25.00, NULL, 0, NULL, '2025-10-25 00:00:00', '2025-10-25 00:00:00'),
(37, 10, 1, 72.28, 25.00, NULL, 1, '2025-10-25 11:00:00', '2025-10-25 11:00:00', '2025-10-25 11:00:00'),
(38, 10, 2, 72.28, 25.00, NULL, 0, NULL, '2025-10-25 11:00:00', '2025-10-25 11:00:00'),
(39, 10, 3, 72.28, 25.00, NULL, 0, NULL, '2025-10-25 11:00:00', '2025-10-25 11:00:00'),
(40, 10, 4, 72.26, 25.00, NULL, 0, NULL, '2025-10-25 11:00:00', '2025-10-25 11:00:00'),
(41, 11, 1, 27.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-26 02:00:00', '2025-11-01 02:00:00'),
(42, 11, 2, 27.00, 25.00, NULL, 1, '2025-10-26 02:00:00', '2025-10-26 02:00:00', '2025-10-26 02:00:00'),
(43, 11, 3, 27.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-26 02:00:00', '2025-11-01 02:00:00'),
(44, 11, 4, 27.00, 25.00, NULL, 0, NULL, '2025-10-26 02:00:00', '2025-10-26 02:00:00'),
(45, 12, 1, 20.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-26 07:00:00', '2025-11-01 02:00:00'),
(46, 12, 2, 20.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-26 07:00:00', '2025-11-01 02:00:00'),
(47, 12, 3, 20.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-26 07:00:00', '2025-11-01 02:00:00'),
(48, 12, 4, 20.00, 25.00, NULL, 1, '2025-10-26 07:00:00', '2025-10-26 07:00:00', '2025-10-26 07:00:00'),
(49, 13, 1, 48.68, 25.00, NULL, 1, '2025-10-26 05:00:00', '2025-10-26 05:00:00', '2025-10-26 05:00:00'),
(50, 13, 2, 48.68, 25.00, NULL, 0, NULL, '2025-10-26 05:00:00', '2025-10-26 05:00:00'),
(51, 13, 3, 48.67, 25.00, NULL, 0, NULL, '2025-10-26 05:00:00', '2025-10-26 05:00:00'),
(52, 13, 4, 48.67, 25.00, NULL, 0, NULL, '2025-10-26 05:00:00', '2025-10-26 05:00:00'),
(53, 14, 1, 55.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-24 23:00:00', '2025-11-01 02:00:00'),
(54, 14, 2, 55.00, 25.00, NULL, 1, '2025-11-01 02:00:00', '2025-10-24 23:00:00', '2025-11-01 02:00:00'),
(55, 14, 3, 55.00, 25.00, NULL, 1, '2025-10-24 23:00:00', '2025-10-24 23:00:00', '2025-10-24 23:00:00'),
(56, 14, 4, 55.00, 25.00, NULL, 0, NULL, '2025-10-24 23:00:00', '2025-10-24 23:00:00'),
(57, 15, 1, 24.00, 25.00, NULL, 0, NULL, '2025-10-27 03:00:00', '2025-10-27 03:00:00'),
(58, 15, 2, 24.00, 25.00, NULL, 1, '2025-10-27 03:00:00', '2025-10-27 03:00:00', '2025-10-27 03:00:00'),
(59, 15, 3, 24.00, 25.00, NULL, 0, NULL, '2025-10-27 03:00:00', '2025-10-27 03:00:00'),
(60, 15, 4, 24.00, 25.00, NULL, 0, NULL, '2025-10-27 03:00:00', '2025-10-27 03:00:00'),
(61, 16, 1, 7.00, 25.00, NULL, 1, '2025-10-27 06:00:00', '2025-10-27 06:00:00', '2025-10-27 06:00:00'),
(62, 16, 2, 7.00, 25.00, NULL, 0, NULL, '2025-10-27 06:00:00', '2025-10-27 06:00:00'),
(63, 16, 3, 7.00, 25.00, NULL, 0, NULL, '2025-10-27 06:00:00', '2025-10-27 06:00:00'),
(64, 16, 4, 7.00, 25.00, NULL, 0, NULL, '2025-10-27 06:00:00', '2025-10-27 06:00:00'),
(65, 17, 2, 13.75, 25.00, NULL, 1, '2025-02-01 01:00:00', '2025-02-01 01:00:00', '2025-02-01 01:00:00'),
(66, 17, 5, 13.75, 25.00, NULL, 1, '2025-02-15 01:00:00', '2025-02-01 01:00:00', '2025-02-15 01:00:00'),
(67, 17, 6, 13.75, 25.00, NULL, 1, '2025-02-15 01:00:00', '2025-02-01 01:00:00', '2025-02-15 01:00:00'),
(68, 17, 7, 13.75, 25.00, NULL, 1, '2025-02-15 01:00:00', '2025-02-01 01:00:00', '2025-02-15 01:00:00'),
(69, 18, 2, 9.63, 25.00, NULL, 1, '2025-02-28 02:00:00', '2025-02-14 02:00:00', '2025-02-28 02:00:00'),
(70, 18, 5, 9.63, 25.00, NULL, 1, '2025-02-14 02:00:00', '2025-02-14 02:00:00', '2025-02-14 02:00:00'),
(71, 18, 6, 9.63, 25.00, NULL, 1, '2025-02-28 02:00:00', '2025-02-14 02:00:00', '2025-02-28 02:00:00'),
(72, 18, 7, 9.61, 25.00, NULL, 1, '2025-02-28 02:00:00', '2025-02-14 02:00:00', '2025-02-28 02:00:00'),
(73, 19, 2, 5.50, 25.00, NULL, 1, '2025-02-28 03:00:00', '2025-02-20 03:00:00', '2025-02-28 03:00:00'),
(74, 19, 5, 5.50, 25.00, NULL, 1, '2025-02-28 03:00:00', '2025-02-20 03:00:00', '2025-02-28 03:00:00'),
(75, 19, 6, 5.50, 25.00, NULL, 1, '2025-02-20 03:00:00', '2025-02-20 03:00:00', '2025-02-20 03:00:00'),
(76, 19, 7, 5.50, 25.00, NULL, 1, '2025-02-28 03:00:00', '2025-02-20 03:00:00', '2025-02-28 03:00:00'),
(77, 20, 2, 12.96, 25.00, NULL, 1, '2025-03-07 01:00:00', '2025-03-01 05:00:00', '2025-03-07 01:00:00'),
(78, 20, 5, 12.96, 25.00, NULL, 1, '2025-03-07 01:00:00', '2025-03-01 05:00:00', '2025-03-07 01:00:00'),
(79, 20, 6, 12.96, 25.00, NULL, 1, '2025-03-07 01:00:00', '2025-03-01 05:00:00', '2025-03-07 01:00:00'),
(80, 20, 7, 12.96, 25.00, NULL, 1, '2025-03-01 05:00:00', '2025-03-01 05:00:00', '2025-03-01 05:00:00'),
(81, 21, 2, 13.75, 25.00, NULL, 1, '2025-03-01 01:00:00', '2025-03-01 01:00:00', '2025-03-01 01:00:00'),
(82, 21, 5, 13.75, 25.00, NULL, 1, '2025-03-15 01:00:00', '2025-03-01 01:00:00', '2025-03-15 01:00:00'),
(83, 21, 6, 13.75, 25.00, NULL, 1, '2025-03-15 01:00:00', '2025-03-01 01:00:00', '2025-03-15 01:00:00'),
(84, 21, 7, 13.75, 25.00, NULL, 1, '2025-03-15 01:00:00', '2025-03-01 01:00:00', '2025-03-15 01:00:00'),
(85, 22, 2, 21.25, 25.00, NULL, 1, '2025-03-20 01:00:00', '2025-03-15 08:00:00', '2025-03-20 01:00:00'),
(86, 22, 5, 21.25, 25.00, NULL, 1, '2025-03-20 01:00:00', '2025-03-15 08:00:00', '2025-03-20 01:00:00'),
(87, 22, 6, 21.25, 25.00, NULL, 1, '2025-03-20 01:00:00', '2025-03-15 08:00:00', '2025-03-20 01:00:00'),
(88, 22, 7, 21.25, 25.00, NULL, 1, '2025-03-15 08:00:00', '2025-03-15 08:00:00', '2025-03-15 08:00:00'),
(89, 23, 1, 750.00, 50.00, NULL, 1, '2025-12-31 23:00:00', '2025-12-31 23:00:00', '2025-12-31 23:00:00'),
(90, 23, 3, 750.00, 50.00, NULL, 1, '2026-01-05 02:00:00', '2025-12-31 23:00:00', '2026-01-05 02:00:00'),
(91, 24, 1, 750.00, 50.00, NULL, 1, '2025-02-01 01:00:00', '2026-01-31 23:00:00', '2025-02-01 01:00:00'),
(92, 24, 3, 750.00, 50.00, NULL, 1, '2026-01-31 23:00:00', '2026-01-31 23:00:00', '2026-01-31 23:00:00'),
(93, 25, 1, 750.00, 50.00, NULL, 1, '2025-02-29 23:00:00', '2025-02-29 23:00:00', '2025-02-29 23:00:00'),
(94, 25, 3, 750.00, 50.00, NULL, 1, '2025-03-03 02:00:00', '2025-02-29 23:00:00', '2025-03-03 02:00:00'),
(95, 26, 1, 750.00, 50.00, NULL, 0, NULL, '2025-03-31 23:00:00', '2025-03-31 23:00:00'),
(96, 26, 3, 750.00, 50.00, NULL, 1, '2025-03-31 23:00:00', '2025-03-31 23:00:00', '2025-03-31 23:00:00'),
(97, 27, 1, 240.00, 50.00, NULL, 1, '2026-01-05 01:00:00', '2026-01-05 01:00:00', '2026-01-05 01:00:00'),
(98, 27, 3, 240.00, 50.00, NULL, 1, '2026-01-10 01:00:00', '2026-01-05 01:00:00', '2026-01-10 01:00:00'),
(99, 28, 5, 164.00, 20.00, NULL, 1, '2026-01-01 04:00:00', '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(100, 28, 1, 164.00, 20.00, NULL, 0, NULL, '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(101, 28, 6, 164.00, 20.00, NULL, 0, NULL, '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(102, 28, 8, 164.00, 20.00, NULL, 0, NULL, '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(103, 28, 7, 164.00, 20.00, NULL, 0, NULL, '2026-01-01 04:00:00', '2026-01-01 04:00:00'),
(104, 29, 1, 112.10, 20.00, NULL, 1, '2026-01-01 11:00:00', '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(105, 29, 5, 112.10, 20.00, NULL, 0, NULL, '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(106, 29, 6, 112.10, 20.00, NULL, 0, NULL, '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(107, 29, 8, 112.10, 20.00, NULL, 0, NULL, '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(108, 29, 7, 112.10, 20.00, NULL, 0, NULL, '2026-01-01 11:00:00', '2026-01-01 11:00:00'),
(109, 30, 5, 30.00, 20.00, NULL, 1, '2026-01-03 02:00:00', '2026-01-02 02:00:00', '2026-01-03 02:00:00'),
(110, 30, 1, 30.00, 20.00, NULL, 0, NULL, '2026-01-02 02:00:00', '2026-01-02 02:00:00'),
(111, 30, 6, 30.00, 20.00, NULL, 1, '2026-01-02 02:00:00', '2026-01-02 02:00:00', '2026-01-02 02:00:00'),
(112, 30, 8, 30.00, 20.00, NULL, 0, NULL, '2026-01-02 02:00:00', '2026-01-02 02:00:00'),
(113, 30, 7, 30.00, 20.00, NULL, 0, NULL, '2026-01-02 02:00:00', '2026-01-02 02:00:00'),
(114, 31, 5, 59.00, 20.00, NULL, 0, NULL, '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(115, 31, 1, 59.00, 20.00, NULL, 0, NULL, '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(116, 31, 6, 59.00, 20.00, NULL, 0, NULL, '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(117, 31, 8, 59.00, 20.00, NULL, 1, '2026-01-02 00:30:00', '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(118, 31, 7, 59.00, 20.00, NULL, 0, NULL, '2026-01-02 00:30:00', '2026-01-02 00:30:00'),
(119, 32, 5, 18.00, 20.00, NULL, 1, '2025-12-31 23:30:00', '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(120, 32, 1, 18.00, 20.00, NULL, 0, NULL, '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(121, 32, 6, 18.00, 20.00, NULL, 0, NULL, '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(122, 32, 8, 18.00, 20.00, NULL, 0, NULL, '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(123, 32, 7, 18.00, 20.00, NULL, 1, '2025-12-31 23:30:00', '2025-12-31 23:30:00', '2025-12-31 23:30:00'),
(124, 33, 5, 75.00, 20.00, NULL, 1, '2026-01-02 06:00:00', '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(125, 33, 1, 75.00, 20.00, NULL, 0, NULL, '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(126, 33, 6, 75.00, 20.00, NULL, 0, NULL, '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(127, 33, 8, 75.00, 20.00, NULL, 0, NULL, '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(128, 33, 7, 75.00, 20.00, NULL, 0, NULL, '2026-01-02 06:00:00', '2026-01-02 06:00:00'),
(129, 34, 1, 29.00, NULL, NULL, 0, NULL, NULL, NULL),
(130, 34, 2, 29.00, NULL, NULL, 0, NULL, NULL, NULL),
(131, 34, 4, 5.80, NULL, NULL, 0, NULL, NULL, NULL),
(132, 34, 3, 5.80, NULL, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) NOT NULL DEFAULT 'group',
  `image_url` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MYR',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `icon`, `image_url`, `currency`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Housemates', 'Shared house expenses at Sentul, KL', 'house', NULL, 'MYR', 1, '2026-01-10 01:00:00', '2026-01-10 01:00:00'),
(2, 'Trip to Penang', 'Penang getaway Oct 25-27, 2024', 'flight_takeoff', NULL, 'MYR', 1, '2025-10-01 00:00:00', '2026-04-17 09:19:19'),
(3, 'Office Coffee', 'Daily coffee & snacks for the team', 'coffee', NULL, 'MYR', 2, '2025-02-01 00:00:00', '2025-02-01 00:00:00'),
(4, 'Rent & Bills', 'Monthly rent and utility bills', 'receipt', NULL, 'MYR', 1, '2025-12-31 23:00:00', '2025-12-31 23:00:00'),
(5, 'Genting Trip', 'New Year trip to Genting Highlands Jan 2025', 'landscape', NULL, 'MYR', 5, '2025-12-20 02:00:00', '2025-12-20 02:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `role`, `joined_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'admin', '2026-01-10 01:00:00', '2026-01-10 01:00:00', '2026-01-10 01:00:00'),
(2, 1, 2, 'member', '2026-01-10 01:10:00', '2026-01-10 01:10:00', '2026-01-10 01:10:00'),
(3, 1, 3, 'member', '2026-01-10 01:15:00', '2026-01-10 01:15:00', '2026-01-10 01:15:00'),
(4, 1, 4, 'member', '2026-01-10 01:20:00', '2026-01-10 01:20:00', '2026-01-10 01:20:00'),
(5, 2, 1, 'admin', '2025-10-01 00:00:00', '2025-10-01 00:00:00', '2025-10-01 00:00:00'),
(6, 2, 2, 'member', '2025-10-01 00:05:00', '2025-10-01 00:05:00', '2025-10-01 00:05:00'),
(7, 2, 3, 'member', '2025-10-01 00:10:00', '2025-10-01 00:10:00', '2025-10-01 00:10:00'),
(8, 2, 4, 'member', '2025-10-01 00:15:00', '2025-10-01 00:15:00', '2025-10-01 00:15:00'),
(9, 3, 2, 'admin', '2025-02-01 00:00:00', '2025-02-01 00:00:00', '2025-02-01 00:00:00'),
(10, 3, 5, 'member', '2025-02-01 00:05:00', '2025-02-01 00:05:00', '2025-02-01 00:05:00'),
(11, 3, 6, 'member', '2025-02-01 00:10:00', '2025-02-01 00:10:00', '2025-02-01 00:10:00'),
(12, 3, 7, 'member', '2025-02-01 00:15:00', '2025-02-01 00:15:00', '2025-02-01 00:15:00'),
(13, 4, 1, 'admin', '2025-12-31 23:00:00', '2025-12-31 23:00:00', '2025-12-31 23:00:00'),
(14, 4, 3, 'member', '2025-12-31 23:05:00', '2025-12-31 23:05:00', '2025-12-31 23:05:00'),
(15, 5, 5, 'admin', '2025-12-20 02:00:00', '2025-12-20 02:00:00', '2025-12-20 02:00:00'),
(16, 5, 1, 'member', '2025-12-20 02:05:00', '2025-12-20 02:05:00', '2025-12-20 02:05:00'),
(17, 5, 6, 'member', '2025-12-20 02:10:00', '2025-12-20 02:10:00', '2025-12-20 02:10:00'),
(18, 5, 8, 'member', '2025-12-20 02:15:00', '2025-12-20 02:15:00', '2025-12-20 02:15:00'),
(19, 5, 7, 'member', '2025-12-20 02:20:00', '2025-12-20 02:20:00', '2025-12-20 02:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `settlements`
--

CREATE TABLE `settlements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `paid_by` bigint(20) UNSIGNED NOT NULL,
  `paid_to` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','duitnow','credit_card','other') NOT NULL DEFAULT 'bank_transfer',
  `note` text DEFAULT NULL,
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settlements`
--

INSERT INTO `settlements` (`id`, `group_id`, `paid_by`, `paid_to`, `amount`, `payment_method`, `note`, `settled_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 57.50, 'duitnow', 'April grocery share', '2025-04-15 01:00:00', '2025-04-15 01:00:00', '2025-04-15 01:00:00'),
(2, 1, 3, 1, 46.38, 'bank_transfer', 'April electricity', '2025-04-16 02:00:00', '2025-04-16 02:00:00', '2025-04-16 02:00:00'),
(3, 1, 2, 3, 24.75, 'duitnow', 'April internet', '2025-04-16 02:00:00', '2025-04-16 02:00:00', '2025-04-16 02:00:00'),
(4, 1, 4, 1, 30.00, 'cash', 'Petrol reimbursement', '2025-04-14 10:30:00', '2025-04-14 10:30:00', '2025-04-14 10:30:00'),
(5, 2, 2, 1, 100.00, 'duitnow', 'Partial airbnb', '2025-11-01 02:00:00', '2025-11-01 02:00:00', '2025-11-01 02:00:00'),
(6, 2, 3, 2, 55.00, 'bank_transfer', 'Fuel reimbursement', '2025-11-01 02:00:00', '2025-11-01 02:00:00', '2025-11-01 02:00:00'),
(7, 3, 5, 2, 55.00, 'duitnow', 'Feb pantry share', '2025-02-28 02:00:00', '2025-02-28 02:00:00', '2025-02-28 02:00:00'),
(8, 3, 6, 2, 55.00, 'bank_transfer', 'Feb & Mar pantry', '2025-03-20 01:00:00', '2025-03-20 01:00:00', '2025-03-20 01:00:00'),
(9, 4, 3, 1, 750.00, 'bank_transfer', 'Jan rent share', '2026-01-05 02:00:00', '2026-01-05 02:00:00', '2026-01-05 02:00:00'),
(10, 4, 1, 3, 750.00, 'bank_transfer', 'Feb rent share', '2025-02-01 01:00:00', '2025-02-01 01:00:00', '2025-02-01 01:00:00'),
(11, 4, 3, 1, 750.00, 'bank_transfer', 'Mar rent share', '2025-03-03 02:00:00', '2025-03-03 02:00:00', '2025-03-03 02:00:00'),
(12, 5, 6, 5, 30.00, 'duitnow', 'Cable car tickets', '2026-01-03 02:00:00', '2026-01-03 02:00:00', '2026-01-03 02:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MYR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `avatar_url`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'Qiao Hui', 'qiao.hui@gmail.com', 'pass123', '+60123456789', NULL, 'MYR', '2026-01-05 01:00:00', '2026-01-05 01:00:00'),
(2, 'Fatima Zahra', 'fatima.zahra@gmail.com', 'pass123', '+60112345678', NULL, 'MYR', '2026-01-06 02:15:00', '2026-01-06 02:15:00'),
(3, 'Tan Wei Ming', 'tanweiming@gmail.com', 'pass123', '+60198765432', NULL, 'MYR', '2026-01-07 03:00:00', '2026-01-07 03:00:00'),
(4, 'Sarah Johari', 'sarah.johari@gmail.com', 'pass123', '+60172839456', NULL, 'MYR', '2026-01-08 00:30:00', '2026-01-08 00:30:00'),
(5, 'Razif Ahmad', 'razif.ahmad@gmail.com', 'pass123', '+60163748291', NULL, 'MYR', '2025-02-01 01:45:00', '2025-02-01 01:45:00'),
(6, 'Nurul Tahira', 'nurul.tahira@gmail.com', 'pass123', '+60154839271', NULL, 'MYR', '2025-02-10 06:00:00', '2025-02-10 06:00:00'),
(7, 'Kenji Chong', 'kenji.chong@gmail.com', 'pass123', '+60182938475', NULL, 'MYR', '2025-03-01 02:00:00', '2025-03-01 02:00:00'),
(8, 'Priya Devi', 'priya.devi@gmail.com', 'pass123', '+60193847562', NULL, 'MYR', '2025-03-15 03:30:00', '2025-03-15 03:30:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exp_group` (`group_id`),
  ADD KEY `fk_exp_payer` (`paid_by`);

--
-- Indexes for table `expense_splits`
--
ALTER TABLE `expense_splits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_split` (`expense_id`,`user_id`),
  ADD KEY `fk_split_user` (`user_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_groups_created_by` (`created_by`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_group_member` (`group_id`,`user_id`),
  ADD KEY `fk_gm_user` (`user_id`);

--
-- Indexes for table `settlements`
--
ALTER TABLE `settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sett_group` (`group_id`),
  ADD KEY `fk_sett_payer` (`paid_by`),
  ADD KEY `fk_sett_payee` (`paid_to`);

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
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `expense_splits`
--
ALTER TABLE `expense_splits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `settlements`
--
ALTER TABLE `settlements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_exp_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exp_payer` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `expense_splits`
--
ALTER TABLE `expense_splits`
  ADD CONSTRAINT `fk_split_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_split_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `fk_groups_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `fk_gm_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settlements`
--
ALTER TABLE `settlements`
  ADD CONSTRAINT `fk_sett_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sett_payee` FOREIGN KEY (`paid_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_sett_payer` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
