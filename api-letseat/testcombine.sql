-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 05, 2022 at 09:56 AM
-- Server version: 5.6.41-84.1
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `baseapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE IF NOT EXISTS `user_account` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `authority` int(11) NOT NULL DEFAULT '0',
  `email` text,
  `name` text,
  `notification` int(11) NOT NULL DEFAULT '1',
  `create_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `user_forgot`
--

CREATE TABLE IF NOT EXISTS  `user_forgot` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_pass`
--

CREATE TABLE IF NOT EXISTS  `user_pass` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `password` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `user_push`
--

CREATE TABLE IF NOT EXISTS  `user_push` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `token` text,
  `create_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `user_token`
--

CREATE TABLE IF NOT EXISTS  `user_token` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `token` text,
  `reset_token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
