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

CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `authority` int(11) NOT NULL DEFAULT '0',
  `email` text,
  `name` text,
  `notification` int(11) NOT NULL DEFAULT '1',
  `create_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `authority`, `email`, `name`, `notification`, `create_date`, `modified_date`, `delete_date`) VALUES
(1005, 0, 'Bburger73@gmail.com', 'Asdf', 1, '2022-08-03 17:52:27 UTC', '2022-08-03 17:52:27 UTC', NULL),
(1057, 0, 'Bburger7@gmail.com', 'Asdf', 1, '2022-08-03 18:05:03 UTC', '2022-08-03 18:05:03 UTC', NULL),
(1058, 0, 'Bb@bb.com', 'Bdbx', 0, '2022-07-26 01:25:24 UTC', '2022-07-26 01:25:24 UTC', NULL),
(2312, 0, 'Bburger73@gmail.com', 'Asdf', 1, '2022-08-03 17:52:33 UTC', '2022-08-03 17:52:33 UTC', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_forgot`
--

CREATE TABLE `user_forgot` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_forgot`
--

INSERT INTO `user_forgot` (`user_id`, `token`, `create_date`, `expire_date`, `modified_date`, `delete_date`) VALUES
(1058, '$2y$10$tRYJYoNqx5UMrnGhGEAYH.AdoEeBc.P9CVa4llfArooB/zsqtqU9.', '2022-08-02 21:21:08 UTC', '2022-09-03 13:03:50 UTC', '2022-08-03 13:03:50 UTC', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_pass`
--

CREATE TABLE `user_pass` (
  `user_id` int(11) NOT NULL,
  `password` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_pass`
--

INSERT INTO `user_pass` (`user_id`, `password`, `create_date`, `expire_date`, `modified_date`, `delete_date`) VALUES
(1005, '$2y$10$TYJxt8qFiXMRoOt0ngMO1uedqNlXN7My.hLyToFmFiEBR3TG1E3.q', '2022-08-03 17:52:27 UTC', '2023-08-03 17:52:27 UTC', '2022-08-03 17:52:27 UTC', NULL),
(1037, '$2y$10$qn/bI3N8rquwXYdxUwVAB.PYiAYTUlcvMdFEl/F0dXSb/2zwNK.nu', '2022-08-03 17:54:05 UTC', '2023-08-03 17:54:05 UTC', '2022-08-03 17:54:05 UTC', NULL),
(1057, '$2y$10$VHXv6DGiglTLXZLM6rGeieKRgsNaf9.X4AZYS6nbdy3iVoabmjp7C', '2022-08-03 18:05:03 UTC', '2023-08-03 18:05:03 UTC', '2022-08-03 18:05:03 UTC', NULL),
(1058, '$2y$10$Q9iAnAd01kTLj2njpDUM.u6TJjnQsTQu5BCbFzZip2HZqFSdv8sjC', '2022-07-26 01:25:24 UTC', '2023-07-26 01:25:24 UTC', '2022-07-26 01:25:24 UTC', NULL),
(2312, '$2y$10$ldK1xkCYtfGoN9p5B4C9bumTlUYEZ1qcN3LhCYq2gkldUBOts0L0.', '2022-08-03 17:52:33 UTC', '2023-08-03 17:52:33 UTC', '2022-08-03 17:52:33 UTC', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_push`
--

CREATE TABLE `user_push` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `create_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_push`
--

INSERT INTO `user_push` (`user_id`, `token`, `create_date`, `modified_date`, `delete_date`) VALUES
(1005, 'ExponentPushToken[EZOZ22LkqSeLnnSa5Ps5hJ]', '2022-08-03 18:31:48 UTC', '2022-08-03 21:25:27 UTC', NULL),
(1058, 'ExponentPushToken[EZOZ22LkqSeLnnSa5Ps5hJ]', '2022-08-02 16:14:34 UTC', '2022-08-03 18:31:38 UTC', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_token`
--

CREATE TABLE `user_token` (
  `user_id` int(11) NOT NULL,
  `token` text,
  `reset_token` text,
  `create_date` text,
  `expire_date` text,
  `modified_date` text,
  `delete_date` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_token`
--

INSERT INTO `user_token` (`user_id`, `token`, `reset_token`, `create_date`, `expire_date`, `modified_date`, `delete_date`) VALUES
(1005, '$2y$10$0Op5dp7EHAC9Ly9F5V/iKOIjgLsRzBrAN53DwGNrk7k6loseugRMO', '$2y$10$/Wi.qLUJpLVwvja3VNcra.vX0MGMvjrVY4DAikQworkfrkZT2rENS', '2022-08-03 18:31:46 UTC', '2022-09-03 18:31:46 UTC', '2022-08-03 18:31:46 UTC', NULL),
(1058, '$2y$10$PE2nuagb2D1FCcXpTHYk5ubG5czFeZ/ciV9vCosKd6kX2F0B0reQO', '$2y$10$Mjm3GpRvpINY0VsQYULzGuaDYaTsFaRnxGJcE.FQ2/9g0ZwOou.Li', '2022-07-26 01:32:01 UTC', '2022-08-26 01:32:01 UTC', '2022-07-26 01:32:01 UTC', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_forgot`
--
ALTER TABLE `user_forgot`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_pass`
--
ALTER TABLE `user_pass`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_push`
--
ALTER TABLE `user_push`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_token`
--
ALTER TABLE `user_token`
  ADD PRIMARY KEY (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
