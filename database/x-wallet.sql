-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 07:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `x-wallet`
--

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `p2p_fees` decimal(5,2) NOT NULL DEFAULT 15.00,
  `withdrawls` decimal(5,2) NOT NULL DEFAULT 15.00,
  `QR_pay` decimal(5,2) NOT NULL DEFAULT 15.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `userId`, `p2p_fees`, `withdrawls`, `QR_pay`) VALUES
(1, 14, 30.00, 0.00, 0.00),
(2, 15, 0.00, 0.00, 0.00),
(9, 26, 0.00, 0.00, 0.00),
(11, 28, 0.00, 0.00, 0.00),
(12, 1, 30.00, 0.00, 0.00),
(16, 7, 5.00, 10.00, 15.00),
(17, 29, 0.00, 0.00, 0.00),
(18, 30, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `userId`, `message`, `is_deleted`, `createdAt`) VALUES
(1, 1, 'Updated notification text', 1, '2025-02-26 19:19:52'),
(2, 1, 'recivied test test', 1, '2025-02-26 19:20:36'),
(3, 1, 'recivied test test', 1, '2025-02-26 19:20:38'),
(8, 1, 'New update available', 0, '2025-03-01 09:57:49'),
(9, 7, 'New update available', 0, '2025-03-01 09:58:12'),
(10, 7, 'New update available', 0, '2025-03-01 10:10:38'),
(11, 7, 'New update available', 0, '2025-03-01 10:11:18'),
(12, 7, 'New update available', 0, '2025-03-01 10:12:16'),
(13, 7, 'New update available', 0, '2025-03-01 10:14:00'),
(14, 7, 'New update available', 0, '2025-03-01 10:17:46'),
(15, 7, 'New update available', 0, '2025-03-01 10:20:07'),
(16, 7, 'New update available', 0, '2025-03-01 10:22:36'),
(17, 7, 'New update available', 0, '2025-03-01 10:22:54'),
(18, 7, 'New update available', 0, '2025-03-01 10:49:32'),
(19, 7, 'New update available', 0, '2025-03-01 10:49:49'),
(20, 7, 'New update available', 0, '2025-03-01 10:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `receiverId` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `type` enum('p2p','withdraw','QR_pay') NOT NULL,
  `fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `senderId`, `receiverId`, `wallet_id`, `amount`, `currency`, `type`, `fees`, `createdAt`, `status`) VALUES
(5, 7, 13, 2, 100.00, 'USD', 'p2p', 2.00, '2024-02-27 08:27:32', 'accepted'),
(6, 12, 7, 1, 50.00, 'LBP', 'QR_pay', 2.00, '2025-02-27 08:46:25', 'accepted'),
(7, 7, 13, 1, 100.00, 'USD', 'p2p', 2.00, '2025-02-28 19:53:00', 'accepted'),
(8, 7, 26, 1, 1.00, 'LBP', 'withdraw', 2.00, '2025-03-02 10:54:06', 'accepted'),
(9, 7, 26, 1, 97.00, 'LBP', 'withdraw', 5.00, '2025-03-02 11:20:29', 'accepted'),
(10, 7, 26, 1, 1.00, 'LBP', 'p2p', 5.00, '2025-03-02 11:22:25', 'accepted'),
(11, 7, 26, 1, 1.00, 'LBP', 'p2p', 5.00, '2025-03-02 11:23:07', 'accepted'),
(12, 7, 26, 1, 1.00, 'LBP', 'p2p', 5.00, '2025-03-02 11:23:17', 'accepted'),
(13, 7, 26, 1, 1.00, 'LBP', 'p2p', 0.05, '2025-03-02 11:27:58', 'accepted'),
(14, 7, 26, 10, 1000.00, 'USD', 'p2p', 50.00, '2025-03-04 20:54:40', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) DEFAULT 0,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_type` enum('un_verified','email','passport_Id') NOT NULL DEFAULT 'un_verified',
  `wallet_id` int(11) DEFAULT NULL,
  `tier` enum('basic','standard','premium') NOT NULL DEFAULT 'basic',
  `id_document` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `password`, `isAdmin`, `createdAt`, `verification_type`, `wallet_id`, `tier`, `id_document`) VALUES
(1, 'name', 'username', 'emain@gmail.com', '76134924', '$2y$10$zfArYij4o9joSR7fOQjjQ.pnMf.b0EdBrvaCatOktgflxoQdFWJB6', 1, '2025-02-26 09:49:48', 'un_verified', NULL, 'premium', NULL),
(7, 'mahditalal', 'mahditalaldev', 'emain21@gmail.com', '1231231111', '$2y$10$L0G/UxDFJx18IK4I8mXDM.n2lXXJ0LaxIEU01yN6zBu6ZGSJS63P.', 0, '2025-02-26 10:09:27', 'un_verified', 1, 'basic', 'backend-structure.png'),
(8, 'name', 'username3', 'email@gmail.com', '761349243', '$2y$10$pOxFbQpAqtbpEEHrjRNx4ujDR36olMW3RdXB5Ojibb0xeuD/BhqVm', 0, '2025-02-26 10:33:19', 'un_verified', NULL, 'basic', NULL),
(10, 'name', 'username4', 'email2@gmail.com', '761349241', '$2y$10$KGfviDwnxAFcE5ee7g2ToegoaiDbEkc.9rDKRxd5kPi12DRXjY8.6', 0, '2025-02-26 10:33:57', 'un_verified', NULL, 'basic', NULL),
(11, 'name', 'username5', 'email3@gmail.com', '761349242', '$2y$10$T7W0jN0mHuOsY3CGIdPLwegxnF.B6kKs1.0jKuSTYxkSbPXR0YUTq', 0, '2025-02-26 21:29:14', 'un_verified', NULL, 'basic', NULL),
(12, 'name', 'username6', 'email4@gmail.com', '7613242', '$2y$10$Tt5TpXP11frPiTmR1Im.xuisyEO./T1w2aUoVCBsocEqfMuwSvRjC', 0, '2025-02-26 21:30:48', 'un_verified', 1, 'basic', NULL),
(13, 'name', 'username7', 'email66@gmail.com', '76132421', '$2y$10$jPp0CPB7x2rCgzDTUP.feO2CfkgY7AdbxklIvEdThGX9Bp1zQauBG', 0, '2025-02-26 21:54:57', 'un_verified', 2, 'basic', NULL),
(14, 'name', 'usernames7', 'email66@gmasil.com', '76132421s', '$2y$10$sjTpGVrSEKmq44/IUX0kXuZQgOqv/Fo0zOB5v5PH8BujnQkdwb.Bq', 0, '2025-02-26 22:24:35', 'un_verified', 3, 'basic', NULL),
(15, 'testing name', 'test', 'mahdi@gmasil.com', '761234', '$2y$10$8kdaBiMZsUBQAn2j1oNa.uQNuBh.9ZEv7pN9b7rrKFjHIzLd0QW0u', 0, '2025-02-28 19:30:12', 'un_verified', 11, 'basic', NULL),
(19, 'testing name', 'test2', 'mahdi1@gmasil.com', '7612341', '$2y$10$Ay.vHAj13oSsj4Hs1P.AVOjSfOHjYvlpSiJEofBKkvoN1BW97mLyi', 0, '2025-02-28 21:09:12', 'un_verified', 15, 'basic', NULL),
(26, 'testing name', 'test23', 'mahdi13@gmasil.com', '76123413', '$2y$10$.soe0OMSFfqNX./NzZAaGuuN0Y7s3uUyAvraZQmpKPJaR0t9G9eee', 0, '2025-02-28 21:13:23', 'un_verified', 22, 'basic', NULL),
(28, 'testing name', 'test23444', 'mahdi134@gmasil.com', '761234134', '$2y$10$OBnmg38DAFkNuU8lMsDndeithVm.MyRHSGeJSsYb9BaUwlhS9XjAu', 0, '2025-02-28 21:18:12', 'un_verified', 24, 'basic', NULL),
(29, 'mahdi', 'mahditalal123', 'mahditalal@gmail.com', '76125478', '$2y$10$UWPQdNp1gr3acjjL/D/99.PIIgd40CIP7oEYDw97wBxgHtVFo5rWy', 0, '2025-03-04 20:33:31', 'un_verified', 25, 'basic', NULL),
(30, 'john', 'salt2010', 'mahdi1345@gmasil.com', '76134922', '$2y$10$19FFOoDIFu/sGn6FOes.aeXcxNXGdqKJzsN1oks3gj1qXLgMqiPH6', 0, '2025-03-05 17:05:48', 'un_verified', 26, 'basic', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `limits` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'USD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `userId`, `balance`, `limits`, `currency`) VALUES
(1, 7, 125.95, 6600.00, 'LBP'),
(2, 13, 350.00, 0.00, 'USD'),
(3, 14, 0.00, 0.00, 'USD'),
(10, 7, 28950.00, 20.00, 'USD'),
(11, 15, 0.00, 0.00, 'USD'),
(15, 19, 0.00, 0.00, 'USD'),
(22, 26, 204.00, 0.00, 'LBP'),
(24, 28, 0.00, 0.00, 'USD'),
(25, 29, 0.00, 0.00, 'USD'),
(26, 30, 0.00, 0.00, 'USD');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId` (`userId`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId` (`senderId`),
  ADD KEY `receiverId` (`receiverId`),
  ADD KEY `wallet_id` (`wallet_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_wallet` (`wallet_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`receiverId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
