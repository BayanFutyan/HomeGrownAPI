-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 05, 2026 at 03:48 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homegrown_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `likes_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `product_id`, `user_id`, `comment`, `parent_id`, `likes_count`, `created_at`, `updated_at`) VALUES
(5, 1, 1, 'Test comment', NULL, 0, '2026-04-04 14:01:05', '2026-04-04 14:01:05'),
(6, 1, 2, 'Nice scent but took a bit longer to deliver.', NULL, 4, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(7, 1, 3, 'Lovely fragrance! Perfect for special occasions.', NULL, 7, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(8, 1, 4, 'Absolutely in love with this perfume. Ordered it for the third time now!', NULL, 11, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(9, 1, 5, 'The packaging was beautiful and the smell is amazing.', NULL, 5, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(10, 1, 6, 'Very elegant perfume. I would buy it again.', NULL, 3, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(11, 1, 7, 'Soft fragrance and really suitable as a gift.', NULL, 6, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(12, 1, 8, 'Good quality, but a bit expensive.', NULL, 2, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(13, 1, 9, 'I got many compliments when wearing this!', NULL, 8, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(14, 1, 10, 'Worth every penny!', NULL, 9, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(15, 1, 11, 'The scent lasts all day long.', NULL, 10, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(16, 1, 12, 'My favorite perfume!', NULL, 12, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(17, 1, 13, 'Excellent product, highly recommended.', NULL, 6, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(18, 2, 14, 'Makes my room smell amazing!', NULL, 5, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(19, 2, 15, 'Very relaxing scent.', NULL, 3, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(20, 2, 16, 'Great quality candle.', NULL, 4, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(21, 2, 17, 'Burns evenly and smells great.', NULL, 6, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(22, 2, 18, 'Perfect for gift giving.', NULL, 2, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(23, 4, 19, 'Beautiful watch, very elegant.', NULL, 7, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(24, 4, 20, 'Great quality and fast delivery.', NULL, 5, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(25, 4, 21, 'Looks exactly like the picture.', NULL, 4, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(26, 4, 22, 'Very comfortable to wear.', NULL, 3, '2026-04-04 14:06:08', '2026-04-04 14:06:08'),
(37, 1, 1, 'هذا تعليق تجريبي للاختبار', NULL, 0, '2026-04-04 16:47:25', '2026-04-04 16:47:25'),
(38, 1, 1, 'مرحبا', 5, 0, '2026-04-05 11:22:30', '2026-04-05 11:22:30'),
(39, 1, 1, 'انا صاحب المشروع', NULL, 0, '2026-04-05 11:24:06', '2026-04-05 11:24:06');

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
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` bigint UNSIGNED NOT NULL,
  `follower_id` bigint UNSIGNED NOT NULL,
  `following_id` bigint UNSIGNED NOT NULL,
  `rating` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(4, '2026_04_04_155922_create_products_table', 1),
(5, '2026_04_04_160112_create_offers_table', 1),
(6, '2026_04_04_160119_create_comments_table', 1),
(7, '2026_04_04_160125_create_followers_table', 1),
(8, '2026_04_04_160132_create_product_details_table', 1),
(9, '2026_04_04_171012_create_personal_access_tokens_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `discount_value` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `product_id`, `discount_value`, `start_date`, `end_date`, `discounted_price`, `created_at`, `updated_at`) VALUES
(3, 3, 15.00, '2026-02-01', '2026-03-31', 127.50, '2026-04-04 13:52:13', '2026-04-04 13:52:13'),
(4, 2, 20.00, '2026-04-05', '2026-04-13', 64.00, '2026-04-05 11:29:06', '2026-04-05 11:29:06'),
(5, 1, 20.00, '2026-04-05', '2026-04-06', 96.00, '2026-04-05 12:37:14', '2026-04-05 12:37:14');

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
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `seller_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes_count` int NOT NULL DEFAULT '0',
  `is_sale` tinyint(1) NOT NULL DEFAULT '0',
  `sales_count` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `name`, `category`, `description`, `price`, `stock`, `image`, `likes_count`, `is_sale`, `sales_count`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Amber Perfume.', 'Candle', 'Amber Perfume is a rich and warm fragrance blended with jasmine.', 120.00, 33, 'images/img5.jpg', 20, 1, 40, NULL, '2026-04-04 13:51:48', '2026-04-05 12:37:14'),
(2, 1, 'Luxury Candle', 'Candle', 'A premium handcrafted candle designed to create a calm atmosphere.', 80.00, 49, 'images/img6.jpg', 90, 1, 150, NULL, '2026-04-04 13:51:48', '2026-04-05 12:30:47'),
(3, 1, 'Woolen Scarf', 'Scarf', 'A soft handmade scarf that provides warmth and comfort.', 150.00, 0, 'images/img4.jpg', 135, 1, 120, NULL, '2026-04-04 13:51:48', '2026-04-04 13:51:48'),
(4, 1, 'Classic Watch', 'Watch', 'An elegant watch with a minimalist timeless design.', 350.00, 15, 'images/img7.jpg', 220, 0, 30, NULL, '2026-04-04 13:51:48', '2026-04-04 13:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `detail_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`id`, `product_id`, `detail_name`, `detail_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bottle Size', '250 ml', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(2, 1, 'Material', 'Glass', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(3, 1, 'Fragrance Family', 'Amber & Floral', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(4, 1, 'Longevity', '8-10 hours', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(5, 2, 'Weight', '200g', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(6, 2, 'Burn Time', '40 hours', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(7, 2, 'Material', 'Soy Wax', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(8, 2, 'Scent', 'Lavender & Vanilla', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(9, 3, 'Material', '100% Wool', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(10, 3, 'Size', '180cm x 60cm', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(11, 3, 'Color', 'Beige', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(12, 3, 'Care', 'Dry clean only', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(13, 4, 'Case Material', 'Stainless Steel', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(14, 4, 'Water Resistant', '50m', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(15, 4, 'Movement', 'Automatic', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(16, 4, 'Strap', 'Genuine Leather', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(17, 4, 'Warranty', '2 years', '2026-04-04 15:03:49', '2026-04-04 15:03:49'),
(18, 1, 'colore', 'red', '2026-04-05 11:25:27', '2026-04-05 11:25:27');

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
('wdySJsr9eQetNVkNoHcLqLNd9OsG3KYx8fgmlIdR', NULL, '127.0.0.1', 'PostmanRuntime/7.51.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid29JN0NHTnE4SVMyZGVhZjY0aFNTbTVINkRQVFhoWTlsRU9YZ3ZIbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775330195);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` enum('admin','artisan','project_owner','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `profile_image`, `address`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Artisan User', 'artisan@example.com', NULL, NULL, NULL, 'artisan', '$2y$12$2s2.tbMKR22agWeTsNfegOe7EUD3F/cbVelOB546gyTwtpdiinpKC', '2026-04-04 13:44:05', '2026-04-04 14:02:16'),
(2, 'User 101', 'user101@example.com', NULL, NULL, NULL, 'user', '$2y$12$CyNmWS4nsbKivxjjwl.FuuVPYyjrLS3wUzRjYtx04f/I1VliBaLdC', '2026-04-04 13:50:27', '2026-04-04 13:50:27'),
(3, 'User 102', 'user102@example.com', NULL, NULL, NULL, 'user', '$2y$12$RTziybDWV/W1JNLO7wLnpeAyLZZ0buRsD6htyhw0OZQqnZYBCdIly', '2026-04-04 13:50:28', '2026-04-04 13:50:28'),
(4, 'User 103', 'user103@example.com', NULL, NULL, NULL, 'user', '$2y$12$bXlkkFmjjSIXtR/T/1xhG.MoUxXe9VkKBVXNONdYkdXQeI0Dohf56', '2026-04-04 13:50:28', '2026-04-04 13:50:28'),
(5, 'User 104', 'user104@example.com', NULL, NULL, NULL, 'user', '$2y$12$5NiRQThN3BULItLgmNcEm.rINer9FX7NVT.cnVQSwy8hfqJIU2O7K', '2026-04-04 13:50:29', '2026-04-04 13:50:29'),
(6, 'User 105', 'user105@example.com', NULL, NULL, NULL, 'user', '$2y$12$kPkBw7PgWSmalk2tS5yg9eTzGwDG1bE/KQWpQ7X1KK1CC95Uk30NW', '2026-04-04 13:50:29', '2026-04-04 13:50:29'),
(7, 'User 106', 'user106@example.com', NULL, NULL, NULL, 'user', '$2y$12$kUeTKXDZYpSXxlbihQ3t9O1pDxer2z3IxdXURrWArF28t5BCM9J6m', '2026-04-04 13:50:30', '2026-04-04 13:50:30'),
(8, 'User 107', 'user107@example.com', NULL, NULL, NULL, 'user', '$2y$12$dHJnOKJ1sDJNYNy8c1FlsuU8LcZxXDoXLSWwB6/82RuxRVJmr.Ys2', '2026-04-04 13:50:30', '2026-04-04 13:50:30'),
(9, 'User 108', 'user108@example.com', NULL, NULL, NULL, 'user', '$2y$12$WNvLRSxSBbpK3GPz.vPkleYSYdn3X50TU1AKcW7Ed9CulbVb82DEO', '2026-04-04 13:50:31', '2026-04-04 13:50:31'),
(10, 'User 109', 'user109@example.com', NULL, NULL, NULL, 'user', '$2y$12$HJAg9cWW.bhfFFsGV3C/4Or/GIZrtlPYVpcW61iZBhF6s4RYpHeqG', '2026-04-04 13:50:31', '2026-04-04 13:50:31'),
(11, 'User 110', 'user110@example.com', NULL, NULL, NULL, 'user', '$2y$12$BvDk88/WjMwja3qkKAZNs.t77genxvkMxbk8GXajX4HaaWIabWHIK', '2026-04-04 13:50:32', '2026-04-04 13:50:32'),
(12, 'User 111', 'user111@example.com', NULL, NULL, NULL, 'user', '$2y$12$YD0bLKZVkecnyA3cg.vJE.NZaUjwh0irVI1fudsQXPDtBjC7u7YLC', '2026-04-04 13:50:32', '2026-04-04 13:50:32'),
(13, 'User 112', 'user112@example.com', NULL, NULL, NULL, 'user', '$2y$12$qbkGh2ufjfhgDW9LaqUjSul5rve93geIjqmAF5SqVquipL6ECxZje', '2026-04-04 13:50:33', '2026-04-04 13:50:33'),
(14, 'User 113', 'user113@example.com', NULL, NULL, NULL, 'user', '$2y$12$9RBo3FPBNDjpU8a4ZwSmJurD2GneE3ieHzZvJcu1oe/3fvZS/DKom', '2026-04-04 13:50:34', '2026-04-04 13:50:34'),
(15, 'User 114', 'user114@example.com', NULL, NULL, NULL, 'user', '$2y$12$HMvz5LPs/pYbtd242FTUVuO0itcIyrEsviHX4PjOb9FxknxK/9x9G', '2026-04-04 13:50:34', '2026-04-04 13:50:34'),
(16, 'User 115', 'user115@example.com', NULL, NULL, NULL, 'user', '$2y$12$oFSBihlbinnRf0GcnUgPv.ix0qhAbfuhHBOyhk3y6iv5H9vhbj7ZO', '2026-04-04 13:50:35', '2026-04-04 13:50:35'),
(17, 'User 116', 'user116@example.com', NULL, NULL, NULL, 'user', '$2y$12$h.DUnZ4H03Nkh9qDMqGgW.30ETDhcZmcM7gv8bOn/l5SgdKetRGlW', '2026-04-04 13:50:36', '2026-04-04 13:50:36'),
(18, 'User 117', 'user117@example.com', NULL, NULL, NULL, 'user', '$2y$12$ZxjQ6gz4rsghxRF.iIaEYOfiUGFkiks5HdrWIVNZvlan251ow.ztG', '2026-04-04 13:50:36', '2026-04-04 13:50:36'),
(19, 'User 118', 'user118@example.com', NULL, NULL, NULL, 'user', '$2y$12$e/eMF1HtgeCwJJgfVHaPd.v8DI3X/YnmlLD/0DpL.8WTg95zIjeU2', '2026-04-04 13:50:37', '2026-04-04 13:50:37'),
(20, 'User 119', 'user119@example.com', NULL, NULL, NULL, 'user', '$2y$12$aCVDJfrlITfbFanHZnXj5eDxbKkb1A0UENWx55Tvp/iqWF0p6NvZS', '2026-04-04 13:50:38', '2026-04-04 13:50:38'),
(21, 'User 120', 'user120@example.com', NULL, NULL, NULL, 'user', '$2y$12$a9EwVVnWInSRDze0H57sIOIR/vsU2mxIX1Z8PeVudmOMNXEnFLJNm', '2026-04-04 13:50:39', '2026-04-04 13:50:39'),
(22, 'User 121', 'user121@example.com', NULL, NULL, NULL, 'user', '$2y$12$FF8wU.8es0A5RipXzqcj6u5xzifXJbhF0S7.ySr/.2pXLrVg40T7G', '2026-04-04 13:50:40', '2026-04-04 13:50:40'),
(23, 'User 122', 'user122@example.com', NULL, NULL, NULL, 'user', '$2y$12$/sG2MFaFsK5RonuByZjlgeS9alV6u.Jfl/t9MO8o0ImlXpf1PF0Bi', '2026-04-04 13:50:40', '2026-04-04 13:50:40'),
(24, 'User 123', 'user123@example.com', NULL, NULL, NULL, 'user', '$2y$12$dARHbshm17P5.1xpDkCw.ucWVrBqJygtjp0X6YYPzILCScjhLg1C6', '2026-04-04 13:50:41', '2026-04-04 13:50:41'),
(25, 'User 124', 'user124@example.com', NULL, NULL, NULL, 'user', '$2y$12$U1Ah4quHyIpfSL8wGr7vl.e4Xm0CCtlolIIZ.bGLXJTBerP6j2nLi', '2026-04-04 13:50:42', '2026-04-04 13:50:42'),
(26, 'User 125', 'user125@example.com', NULL, NULL, NULL, 'user', '$2y$12$3lEvnDlonBfVzpiHRGQ.j.wlSomZZXUVWKwrLBEkGCiClPtpaC4Ca', '2026-04-04 13:50:42', '2026-04-04 13:50:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_product_id_index` (`product_id`),
  ADD KEY `comments_user_id_index` (`user_id`),
  ADD KEY `comments_parent_id_index` (`parent_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `followers_follower_id_following_id_unique` (`follower_id`,`following_id`),
  ADD KEY `followers_follower_id_index` (`follower_id`),
  ADD KEY `followers_following_id_index` (`following_id`);

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
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offers_product_id_index` (`product_id`),
  ADD KEY `offers_start_date_end_date_index` (`start_date`,`end_date`);

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
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_seller_id_deleted_at_index` (`seller_id`,`deleted_at`),
  ADD KEY `products_category_index` (`category`),
  ADD KEY `products_is_sale_index` (`is_sale`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_details_product_id_index` (`product_id`);

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `product_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
