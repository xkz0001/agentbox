-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 08, 2011 at 02:02 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `agentbox`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `orgName` varchar(100) DEFAULT NULL,
  `orgTitle` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `phoneNumber` varchar(100) DEFAULT NULL,
  `googleID` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `userID`, `name`, `emailAddress`, `orgName`, `orgTitle`, `website`, `phoneNumber`, `googleID`, `is_deleted`, `updated`) VALUES
(24, 1, 'google conact111', '', 'comany123456', 'title', '', '123', 'http://www.google.com/m8/feeds/contacts/demo.test120%40gmail.com/base/5ed3dee8085595f4', 1, '2011-11-07 12:46:33'),
(25, 1, 'google contact2', '', '', '', '', '123456', 'http://www.google.com/m8/feeds/contacts/demo.test120%40gmail.com/base/6aadbafb8df86df1', 0, '2011-11-07 14:22:35');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `guest` varchar(500) NOT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `owner` varchar(50) DEFAULT NULL,
  `googleID` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `userID`, `title`, `description`, `location`, `guest`, `starttime`, `endtime`, `owner`, `googleID`, `is_deleted`, `updated`) VALUES
(8, 1, 'local events', 'test', 'home update', 'cdq1230@ww.com,demo.test@gmail.co,demo.test120@gmail.com', '2011-10-25 09:00:00', '2011-10-25 14:00:00', '', 'http://www.google.com/calendar/feeds/default/private/full/ifsqei9s8vd4ou5u2f9rhi45o8', 1, '2011-10-26 05:58:35'),
(9, 1, 'calender2', '', '', 'demo.test120@gmail.com', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 'http://www.google.com/calendar/feeds/default/private/full/elq9a17k9skjn8hf0l0ssk3eg8', 0, '2011-10-26 06:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `file_system`
--

CREATE TABLE IF NOT EXISTS `file_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `tags` varchar(60) NOT NULL,
  `owner` int(11) NOT NULL,
  `password` varchar(30) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `is_folder` tinyint(1) NOT NULL,
  `pname` varchar(100) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `permission` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `PARENTID` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `file_system`
--

INSERT INTO `file_system` (`id`, `title`, `type`, `tags`, `owner`, `password`, `parent_id`, `is_folder`, `pname`, `position`, `permission`) VALUES
(1, 'foler1', '', '', 1, '', 0, 0, NULL, 0, 3),
(62, 'folder2', 'folder', '', 0, '', 0, 1, NULL, 1, 0),
(63, 'file1', '', '', 0, '', 1, 0, NULL, 2, 0),
(64, 'file1', '', '', 1, '', 68, 0, NULL, 0, 0),
(65, 'file2', '', '', 1, '', 621, 0, NULL, 1, 0),
(66, 'xxx', '', '', 1, '', 1, 1, NULL, 2, 0),
(67, 'New Text Document.txt', '.txt', 'xsss', 1, '', 62, 0, '1319375775.txt', 1, 0),
(68, 'ddd', '', '', 1, '', 66, 1, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `google_sync_time`
--

CREATE TABLE IF NOT EXISTS `google_sync_time` (
  `userID` int(11) NOT NULL,
  `contacts` datetime DEFAULT NULL,
  `tasks` datetime DEFAULT NULL,
  `events` datetime DEFAULT NULL,
  UNIQUE KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `google_sync_time`
--

INSERT INTO `google_sync_time` (`userID`, `contacts`, `tasks`, `events`) VALUES
(1, '2011-11-08 01:22:52', '1970-01-01 00:00:01', '1970-01-01 00:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(300) NOT NULL,
  `status` varchar(50) NOT NULL,
  `googleID` varchar(200) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `userID`, `title`, `date`, `description`, `status`, `googleID`, `is_deleted`, `updated`) VALUES
(25, 1, 'dddssseeee', NULL, '', 'needsAction', 'MDc1NjAxNDIxNzcxOTE2ODU0Njg6MDoxMjY2MTk0MDU', 1, '2011-10-25 07:54:22'),
(26, 1, 'sss', NULL, '', 'needsAction', 'MDc1NjAxNDIxNzcxOTE2ODU0Njg6MDo4OTAwNzk1OTk', 1, '2011-10-25 08:00:18'),
(27, 1, 'google task', '2011-10-28', 'hello', 'completed', 'MDc1NjAxNDIxNzcxOTE2ODU0Njg6MDoxNjcyNTM4NzQz', 0, '2011-10-26 00:55:58'),
(28, 1, 'task 4 title', '2011-10-29', 'task 4', 'needsAction', 'MDc1NjAxNDIxNzcxOTE2ODU0Njg6MDo1MDYyNTc1OQ', 0, '2011-10-26 00:57:53'),
(29, 1, 'task5', '2011-10-27', 'task 5 note local updated', 'needsAction', 'MDc1NjAxNDIxNzcxOTE2ODU0Njg6MDo3NDM1NjA1NDM', 0, '2011-10-26 00:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `tree_elements`
--

CREATE TABLE IF NOT EXISTS `tree_elements` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `ownerEl` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'parent',
  `slave` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tree_elements`
--

INSERT INTO `tree_elements` (`Id`, `name`, `position`, `ownerEl`, `slave`) VALUES
(2, 'hello', 0, 0, '0'),
(3, 'sss', 1, 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL,
  `group` tinyint(4) NOT NULL,
  `google_username` varchar(50) DEFAULT NULL,
  `google_password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `group`, `google_username`, `google_password`) VALUES
(1, 'master', '123456', 2, 'demo.test120@gmail.com', '@gentb0x');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
