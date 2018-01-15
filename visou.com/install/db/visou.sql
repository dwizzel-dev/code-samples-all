-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2017 at 03:42 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `visou`
--

-- --------------------------------------------------------

--
-- Table structure for table `basic_infos`
--

CREATE TABLE IF NOT EXISTS `basic_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(5) NOT NULL DEFAULT 'en_US',
  `exercise_id` int(11) NOT NULL,
  `exercise_code` varchar(64) NOT NULL,
  `short_title` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `pictures` varchar(512) NOT NULL,
  `video_code` varchar(512) NOT NULL,
  `video_id` int(11) NOT NULL,
  `video_name` varchar(64) NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `exercise_code` (`exercise_code`),
  KEY `locale` (`locale`),
  KEY `video_code` (`video_code`(333)),
  KEY `video_name` (`video_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47796 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(256) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  KEY `category_id` (`category_id`,`locale`),
  KEY `name` (`name`),
  KEY `ref_id` (`ref_id`),
  KEY `title` (`title`),
  KEY `description` (`description`(333)),
  KEY `date_modified` (`date_modified`),
  KEY `locale` (`locale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories_filters`
--

CREATE TABLE IF NOT EXISTS `categories_filters` (
  `category_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `locale` smallint(6) NOT NULL,
  PRIMARY KEY (`category_id`,`filter_id`),
  KEY `locale` (`locale`),
  KEY `category_id` (`category_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories_filters_exercises`
--

CREATE TABLE IF NOT EXISTS `categories_filters_exercises` (
  `category_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `locale` smallint(6) NOT NULL,
  PRIMARY KEY (`category_id`,`filter_id`,`exercise_id`),
  KEY `locale` (`locale`),
  KEY `category_id` (`category_id`),
  KEY `filter_id` (`filter_id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(2) NOT NULL,
  PRIMARY KEY (`country_id`),
  KEY `name` (`name`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE IF NOT EXISTS `exercises` (
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` smallint(6) NOT NULL DEFAULT '1',
  `short_title` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `thumb_0` text NOT NULL,
  `thumb_1` text NOT NULL,
  `pict_0` text NOT NULL,
  `pict_1` text NOT NULL,
  `video_id` int(11) NOT NULL,
  `video_code` text NOT NULL,
  `video_youtube` text NOT NULL,
  `notes` text NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `ref_id` int(11) NOT NULL,
  `url_title` varchar(256) NOT NULL,
  `meta_description` varchar(512) NOT NULL,
  `short_description` varchar(512) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ranking` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`exercise_id`),
  KEY `exercise_id` (`exercise_id`,`locale`),
  KEY `title` (`title`),
  KEY `short_title` (`short_title`),
  KEY `ref_id` (`ref_id`),
  KEY `url_title` (`url_title`),
  KEY `meta_description` (`meta_description`(333)),
  KEY `short_description` (`short_description`(333)),
  KEY `locale` (`locale`),
  KEY `thumb_0` (`thumb_0`(100)),
  KEY `thumb_1` (`thumb_1`(100)),
  KEY `video_youtube` (`video_youtube`(100))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37630 ;

-- --------------------------------------------------------

--
-- Table structure for table `exercises_categories`
--

CREATE TABLE IF NOT EXISTS `exercises_categories` (
  `exercise_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`exercise_id`,`category_id`),
  KEY `exercise_id` (`exercise_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exercises_filters`
--

CREATE TABLE IF NOT EXISTS `exercises_filters` (
  `exercise_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  PRIMARY KEY (`exercise_id`,`filter_id`),
  KEY `exercise_id` (`exercise_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exercises_keywords`
--

CREATE TABLE IF NOT EXISTS `exercises_keywords` (
  `exercise_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`exercise_id`,`keyword_id`),
  KEY `exercise_id` (`exercise_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

CREATE TABLE IF NOT EXISTS `filters` (
  `filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(256) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`filter_id`),
  KEY `filter_id` (`filter_id`,`locale`),
  KEY `name` (`name`),
  KEY `ref_id` (`ref_id`),
  KEY `title` (`title`),
  KEY `description` (`description`(333)),
  KEY `locale` (`locale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6406 ;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(256) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ranking` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyword_id`),
  KEY `keyword_id` (`keyword_id`,`locale`),
  KEY `name` (`name`),
  KEY `title` (`title`),
  KEY `ref_id` (`ref_id`),
  KEY `date_modified` (`date_modified`),
  KEY `locale` (`locale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45043 ;

-- --------------------------------------------------------

--
-- Table structure for table `locales`
--

CREATE TABLE IF NOT EXISTS `locales` (
  `locale_id` smallint(11) NOT NULL,
  `name` varchar(5) NOT NULL,
  PRIMARY KEY (`locale_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locales`
--

INSERT INTO `locales` (`locale_id`, `name`) VALUES
(1, 'en_US'),
(2, 'fr_CA'),
(3, 'es_MX'),
(4, 'nl_NL'),
(5, 'pt_PT');

-- --------------------------------------------------------

--
-- Table structure for table `slogan`
--

CREATE TABLE IF NOT EXISTS `slogan` (
  `slogan_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `locale` tinyint(4) NOT NULL,
  `country_id` smallint(6) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`slogan_id`),
  KEY `name` (`name`),
  KEY `locale` (`locale`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE IF NOT EXISTS `type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`type_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `url`
--

CREATE TABLE IF NOT EXISTS `url` (
  `url_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_long` varchar(1024) NOT NULL,
  `url_short` varchar(256) NOT NULL,
  `url_canonical` varchar(256) NOT NULL,
  `locale` tinyint(4) NOT NULL DEFAULT '1',
  `country` tinyint(4) NOT NULL DEFAULT '1',
  `caegory_ids` varchar(1024) NOT NULL,
  `filter_ids` varchar(1024) NOT NULL,
  `exercise_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

