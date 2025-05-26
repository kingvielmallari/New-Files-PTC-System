-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 04:19 PM
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
-- Database: `ptcdatabase3`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment_fees`
--

CREATE TABLE `assessment_fees` (
  `id` int(11) NOT NULL,
  `payment_name` varchar(255) NOT NULL,
  `paying_new` decimal(10,0) DEFAULT NULL,
  `paying_old` decimal(10,0) DEFAULT NULL,
  `unifast_new` decimal(10,0) DEFAULT NULL,
  `unifast_old` decimal(10,0) DEFAULT NULL,
  `executive_old` decimal(10,0) DEFAULT NULL,
  `executive_new` decimal(10,0) DEFAULT NULL,
  `status` enum('Enabled','Disabled') DEFAULT 'Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment_fees`
--

INSERT INTO `assessment_fees` (`id`, `payment_name`, `paying_new`, `paying_old`, `unifast_new`, `unifast_old`, `executive_old`, `executive_new`, `status`) VALUES
(2, 'Library Fee', 1, 1, 1, 1, 40, 200, 'Enabled'),
(3, 'Publication (PLUMAGE)', 1, 1, 1, 1, 75, 175, 'Enabled'),
(4, 'Athletic Fee', 1, 1, 1, 1, 15, 115, 'Enabled'),
(5, 'Cultural Fee', 1, 1, 1, 1, 15, 115, 'Enabled'),
(6, 'Supreme Student Council (SSC)', 1, 1, 1, 1, 75, 175, 'Enabled'),
(7, 'Guidance Fee', 1, 1, 1, 1, 100, 100, 'Enabled'),
(8, 'Career Development', 1, 1, 1, 1, 50, 150, 'Enabled'),
(9, '', 0, 0, 0, 0, 0, 0, 'Enabled'),
(10, 'Medical and Dental Fee / Insurance Fee', 1, 1, 1, 1, 50, 300, 'Enabled'),
(12, 'ID Validation Fee', 1, 1, 1, 1, 150, 250, 'Enabled'),
(15, 'LMS', 2, 1, 4, 3, 300, 300, 'Enabled');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment_fees`
--
ALTER TABLE `assessment_fees`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessment_fees`
--
ALTER TABLE `assessment_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
