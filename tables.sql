-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 07, 2017 at 03:19 PM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cosmictracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `neighbours`
--

CREATE TABLE IF NOT EXISTS `neighbours` (
`id` int(11) NOT NULL,
  `system` text NOT NULL,
  `system_id` text NOT NULL,
  `neighbour` text NOT NULL,
  `neighbour_id` text NOT NULL,
  `sec_status` text NOT NULL,
  `const` text NOT NULL,
  `const_id` text NOT NULL,
  `region` text NOT NULL,
  `region_id` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3091 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE IF NOT EXISTS `signatures` (
`id` int(11) NOT NULL,
  `system` text NOT NULL,
  `system_id` text NOT NULL,
  `const_name` text NOT NULL,
  `const_id` text NOT NULL,
  `region_name` text NOT NULL,
  `region_id` text NOT NULL,
  `sig_id` text NOT NULL,
  `sig_type` text NOT NULL,
  `sig_name` text NOT NULL,
  `reported` text NOT NULL,
  `reported_id` text NOT NULL,
  `corp_id` text NOT NULL,
  `alliance_id` text NOT NULL,
  `report_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `user` text NOT NULL,
  `user_id` text NOT NULL,
  `corp` text NOT NULL,
  `corp_id` text NOT NULL,
  `alliance` text NOT NULL,
  `alliance_id` text NOT NULL,
  `relic_sites` int(11) NOT NULL,
  `data_sites` int(11) NOT NULL,
  `gas_sites` int(11) NOT NULL,
  `combat_sites` int(11) NOT NULL,
  `wormholes` int(11) NOT NULL,
  `total_scanned` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `neighbours`
--
ALTER TABLE `neighbours`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `neighbours`
--
ALTER TABLE `neighbours`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3091;
--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=250;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=151;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;