-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 05, 2019 at 08:53 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1-log
-- PHP Version: 5.6.37-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_csmgr`
--

-- --------------------------------------------------------

--
-- Table structure for table `db_finance_assets`
--

CREATE TABLE `db_finance_assets` (
  `id` int(11) NOT NULL,
  `name` char(60) NOT NULL,
  `bank` char(30) NOT NULL,
  `moneytype` char(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `year_rate` decimal(10,2) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `createtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_finance_assets`
--

INSERT INTO `db_finance_assets` (`id`, `name`, `bank`, `moneytype`, `amount`, `year_rate`, `startdate`, `enddate`, `createtime`, `description`) VALUES
(1, 'ffefe', 'CMB', 'USD', '0.00', '0.00', '0000-00-00', '2019-07-09', '2019-08-06 11:11:53', ''),
(2, 'dfefe', 'ICBC', 'USD', '0.00', '0.00', '2019-08-28', '2019-09-06', '2019-08-06 11:13:39', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `db_finance_assets`
--
ALTER TABLE `db_finance_assets`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
