-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2008 at 12:40 PM
-- Server version: 4.1.22
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `wsi`
--

-- --------------------------------------------------------

--
-- Table structure for table `icache`
--

DROP TABLE IF EXISTS `icache`;
CREATE TABLE `icache` (
  `id` int(12) NOT NULL auto_increment COMMENT 'Compiled Image ID',
  `userid` int(12) NOT NULL default '0' COMMENT 'User ID',
  `updated` int(12) NOT NULL default '0' COMMENT 'Last Updated Time',
  `data` blob NOT NULL COMMENT 'Compiled Image Data',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Compiled Images Cache';

--
-- Dumping data for table `icache`
--


-- --------------------------------------------------------

--
-- Table structure for table `ilayers`
--

DROP TABLE IF EXISTS `ilayers`;
CREATE TABLE `ilayers` (
  `id` int(12) NOT NULL auto_increment COMMENT 'Image ID',
  `userid` int(12) NOT NULL default '0' COMMENT 'User ID',
  `cache_bg` blob NOT NULL COMMENT 'Background Layer Cache',
  `cache_fields` blob NOT NULL COMMENT 'Field Layer Cache',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Image Layer Cache';

--
-- Dumping data for table `ilayers`
--


-- --------------------------------------------------------

--
-- Table structure for table `isettings`
--

DROP TABLE IF EXISTS `isettings`;
CREATE TABLE `isettings` (
  `id` int(12) NOT NULL auto_increment,
  `userid` int(12) NOT NULL default '0',
  `xmldata` text collate utf8_unicode_ci NOT NULL COMMENT 'XML Image Settings',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Image Settings Database';

--
-- Dumping data for table `isettings`
--


-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(12) NOT NULL auto_increment COMMENT 'Profile ID',
  `userid` int(12) NOT NULL default '0' COMMENT 'User ID',
  `realname` varchar(25) collate utf8_unicode_ci NOT NULL default '' COMMENT 'User''s Name',
  `location` varchar(25) collate utf8_unicode_ci NOT NULL default '' COMMENT 'Location',
  `wpjdate` int(12) NOT NULL default '0' COMMENT 'Whatpulse Join Date',
  `email` varchar(25) collate utf8_unicode_ci NOT NULL default '' COMMENT 'email address',
  `question` varchar(55) collate utf8_unicode_ci NOT NULL default '' COMMENT 'Password Question',
  `answer` varchar(55) collate utf8_unicode_ci NOT NULL default '' COMMENT 'Password Answer',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Profile Details';

--
-- Dumping data for table `profiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `pulse_data`
--

DROP TABLE IF EXISTS `pulse_data`;
CREATE TABLE `pulse_data` (
  `id` int(12) NOT NULL auto_increment COMMENT 'ID',
  `userid` int(12) NOT NULL default '0' COMMENT 'User ID',
  `keys` int(32) NOT NULL default '0' COMMENT 'User Keys',
  `clicks` int(32) NOT NULL default '0' COMMENT 'User Clicks',
  `rank` int(10) NOT NULL default '0' COMMENT 'User Rank',
  `tid` int(12) default NULL COMMENT 'Team ID',
  `trank` int(10) default NULL COMMENT 'Rank in Team',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Whatpulse Data';

--
-- Dumping data for table `pulse_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `pulse_teams`
--

DROP TABLE IF EXISTS `pulse_teams`;
CREATE TABLE `pulse_teams` (
  `id` int(12) NOT NULL auto_increment COMMENT 'Team ID',
  `name` varchar(32) collate utf8_unicode_ci NOT NULL default '' COMMENT 'Team Name',
  `keys` int(32) NOT NULL default '0' COMMENT 'Team Keys',
  `clicks` int(32) NOT NULL default '0' COMMENT 'Team Clicks',
  `members` int(10) NOT NULL default '0' COMMENT 'Team Members',
  `updated` int(10) NOT NULL default '0' COMMENT 'Last Updated',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Whatpulse Team Data';

--
-- Dumping data for table `pulse_teams`
--


-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `access` int(18) unsigned NOT NULL default '0',
  `data` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sessions Data Table';

--
-- Dumping data for table `sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL auto_increment COMMENT 'user id',
  `username` varchar(25) collate utf8_unicode_ci NOT NULL default '' COMMENT 'user name',
  `password` varchar(32) collate utf8_unicode_ci NOT NULL default '' COMMENT 'password',
  `date` int(12) NOT NULL default '0' COMMENT 'date joined',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Primary User Information Storage';

--
-- Dumping data for table `users`
--

