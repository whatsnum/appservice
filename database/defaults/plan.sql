-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 05, 2019 at 10:35 PM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `plan_master`
--

CREATE TABLE `plan` (
  `plan_id` int(11) NOT NULL,
  `plan_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_plan_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_type` enum('limited','unlimited') COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_of_contact` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_amount` decimal(16,2) NOT NULL,
  `plan_days` enum('30 days','365 days') COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_flag` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_flag` enum('active','deactive') COLLATE utf8mb4_unicode_ci NOT NULL,
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  `mysqltime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plan_master`
--

INSERT INTO `plans` (`id`, `name`, `sub_name`, `type`, `no_of_contact`, `amount`, `days`, `description`) VALUES
(1, 'FREE', 'Rooky', 'limited', '25', '0.00', '30 days', 'When they sign up, this allows them be able\r\nTo send requests to only 5 contacts daily.'),
(2, 'ELITES', 'Upgrade to ELITES', 'limited', '100', '726.35', '30 days', 'Request upto 25 contacts daily'),
(3, 'PRO', 'Upgrade to PRO', 'limited', '500', '2555.00', '30 days', 'Request upto 100 contacts daily.'),
(4, 'DIAMOND', 'Upgrade to DIAMOND', 'limited', '1000', '4380.00', '30 days', 'Request up to 500 Contacts Daily'),
(5, 'UNLIMITED', 'BLACK', 'unlimited', '3650000', '17885.00', '365 days', 'Unlimited Contacts');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `plan_master`
--
ALTER TABLE `plan_master`
  ADD PRIMARY KEY (`plan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
