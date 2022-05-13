-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 13, 2022 at 08:26 AM
-- Server version: 10.5.6-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `shopping`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `quantity` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `image`, `status`, `quantity`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'Суитшърт Advance Academy', 'Суитшърта е любимото попълнение към аутфита на нашите преподаватели)) Попълни и ти твоя гардероб ;)', '45.00', '/public/catalog/product-1.jpg', 'active', 9999, '2022-01-01 00:00:00', 0, '2022-01-01 00:00:00', 0),
(2, 'Тениска Code by Advance Academy', 'Важните стъпки, които всеки програмист следва :) Отличи се от тълпата с интересна тениска', '24.00', '/public/catalog/product-2.jpg', 'active', 9999, '2022-01-01 00:00:00', 0, '2022-01-01 00:00:00', 0),
(3, 'Тениска FALSE by Advance Academy', 'Всеки програмист трябва да я има! Задай стил на аутфита си!', '24.00', '/public/catalog/product-3.jpg', 'active', 9999, '2022-01-01 00:00:00', 0, '2022-01-01 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'client'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_expired_at` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `phone_number`, `password`, `token`, `token_expired_at`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'John', 'Doe', 'admin@example.com', '+359889990030', '$2y$10$stCFDDoO.BzkaNvALokYbeW1.2mO7yN7DE2OC9D0IxUyOUvkkYG8e', '', 1650478029, '2022-01-01 10:00:00', 0, '2022-01-01 10:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 1, 2);
COMMIT;
