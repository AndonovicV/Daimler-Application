-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8888
-- Generation Time: May 06, 2024 at 12:33 AM
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
-- Database: `dom_cockpit_dummy`
--

-- --------------------------------------------------------

--
-- Table structure for table `mt_agenda`
--

CREATE TABLE `mt_agenda` (
  `item_id` float NOT NULL,
  `GFT` varchar(255) DEFAULT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Change_Request` varchar(255) DEFAULT NULL,
  `Task` varchar(255) DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `Milestone` varchar(255) DEFAULT NULL,
  `Responsible` varchar(255) DEFAULT NULL,
  `Start` varchar(255) DEFAULT NULL,
  `New_Row` enum('Yes','No') DEFAULT NULL,
  `Delete_Row` enum('Yes','No') DEFAULT NULL,
  `agenda_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mt_agenda`
--

INSERT INTO `mt_agenda` (`item_id`, `GFT`, `Topic`, `Status`, `Change_Request`, `Task`, `Comment`, `Milestone`, `Responsible`, `Start`, `New_Row`, `Delete_Row`, `agenda_id`) VALUES
(1, 'GFT1', 'Topic1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(1.5, 'GFT2', 'Topic2', '3', '3', '3', '3', '3', '3', '3', 'Yes', 'No', 1),
(2, 'GFT1', 'Topic1', '2', '2', '2', '2', '2', '2', '2', 'Yes', 'No', 2),
(2.5, 'Change Request1', 'Topic2', '3', '3', '3', '3', '3', '3', '3', 'Yes', 'No', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mt_agenda`
--
ALTER TABLE `mt_agenda`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `agenda_id` (`agenda_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mt_agenda`
--
ALTER TABLE `mt_agenda`
  ADD CONSTRAINT `mt_agenda_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `mt_agenda_list` (`agenda_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
