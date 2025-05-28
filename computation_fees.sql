-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 03:58 PM
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
-- Table structure for table `computation_fees`
--

CREATE TABLE `computation_fees` (
  `id` int(11) NOT NULL,
  `program` varchar(255) NOT NULL,
  `unit_new` decimal(10,0) DEFAULT NULL,
  `unit_old` decimal(10,0) DEFAULT NULL,
  `lab_old` decimal(10,0) DEFAULT NULL,
  `lab_new` decimal(10,0) DEFAULT NULL,
  `comp_new` decimal(10,0) DEFAULT NULL,
  `comp_old` decimal(10,0) DEFAULT NULL,
  `nstp_old` decimal(10,0) DEFAULT NULL,
  `nstp_new` decimal(10,0) DEFAULT NULL,
  `status` enum('Enabled','Disabled') DEFAULT 'Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `computation_fees`
--

INSERT INTO `computation_fees` (`id`, `program`, `unit_new`, `unit_old`, `lab_old`, `lab_new`, `comp_new`, `comp_old`, `nstp_old`, `nstp_new`, `status`) VALUES
(2, 'CHRM', 400, 300, 300, 400, 400, 300, 150, 200, 'Enabled'),
(3, 'CCS', 400, 300, 300, 400, 400, 300, 150, 150, 'Enabled'),
(4, 'BSBA', 400, 300, 300, 400, 400, 300, 0, 0, 'Enabled'),
(5, 'ACET', 400, 300, 300, 400, 400, 300, 0, 0, 'Enabled'),
(6, 'ABA', 400, 300, 300, 400, 400, 300, 0, 0, 'Enabled'),
(7, 'AAIS', 500, 400, 400, 500, 500, 400, 300, 300, 'Enabled'),
(8, 'AHRD', 500, 400, 400, 500, 500, 400, 300, 300, 'Enabled'),
(9, 'BSIT', 400, 300, 300, 400, 400, 300, 150, 150, 'Enabled'),
(10, 'BSOA', 400, 300, 300, 400, 400, 300, 150, 150, 'Enabled'),
(13, 'COA', 400, 300, 300, 400, 400, 300, 150, 150, 'Enabled'),
(15, 'AHRT', 500, 400, 400, 500, 500, 400, 300, 300, 'Enabled');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `computation_fees`
--
ALTER TABLE `computation_fees`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `computation_fees`
--
ALTER TABLE `computation_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
