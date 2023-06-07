-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2023 at 02:31 PM
-- Server version: 10.1.39-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `live_gpro_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `passport_infos`
--

CREATE TABLE `passport_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `salutation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_copy` varchar(264) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date NOT NULL,
  `citizenship` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_residence_country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visa_residence` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multiple_entry_visa_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multiple_entry_visa` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` longtext COLLATE utf8mb4_unicode_ci,
  `admin_remark` longtext COLLATE utf8mb4_unicode_ci,
  `admin_letter` varchar(264) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Approve','Reject') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `admin_status` enum('Pending','Approved','Decline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `user_confirm` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yes',
  `sponsorship_letter` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_letter` varchar(264) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_spanish_letter` varchar(264) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('Sponsorship','Financial','Sponsorship+Financial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sponsorship',
  `diplomatic_passport` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `passport_valid` varchar(264) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_provide_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_provide_email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visa_not_ranted_comment` longtext COLLATE utf8mb4_unicode_ci,
  `visa_granted` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `passport_infos`
--
ALTER TABLE `passport_infos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `passport_infos`
--
ALTER TABLE `passport_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
