-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2017 at 04:07 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `visou_0`
--

-- --------------------------------------------------------

--
-- Table structure for table `xls_categories`
--

CREATE TABLE IF NOT EXISTS `xls_categories` (
  `locale` varchar(5) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='importation de la feuille excel de tom';

--
-- Dumping data for table `xls_categories`
--

INSERT INTO `xls_categories` (`locale`, `ref_id`, `description`) VALUES
('en_US', 1, 'any part of an organism such as an organ or extremity. the material part or nature of a human being. basically a part of a human body. muscles, articulations, etc.'),
('en_US', 2, ''),
('en_US', 3, 'motion. the act, result or process of moving. an act of changing physical location or position or of having this changed.'),
('en_US', 4, 'something that one''s efforts or actions are intended to attain or accomplish. a thing aimed at or sought. something worked toward or striven for&semi; a goal, purpose or target.'),
('en_US', 6, 'used to refer to a category that is different or distinct from the other ones already mentioned.'),
('en_US', 7, ''),
('en_US', 8, 'describing or identifying something precisely or of stating a precise requirement.'),
('en_US', 11, ''),
('en_US', 14, 'something &lpar;as an instrument&rpar; used in performing an operation or necessary in the practice of an exercise.'),
('en_US', 15, ''),
('en_US', 16, ''),
('en_US', 17, 'the intensity that must be exceeded for a certain reaction, phenomenon, result, or condition to occur or be manifested.'),
('en_US', 18, 'a careful plan or method for achieving a particular goal usually over a long period of time'),
('fr_CA', 1, 'partie du corps humain. appareil musculaire, vaisseaux sanguins, syst&egrave;me nerveux, respiratoire, digestif, urinaire, reproducteur, endocrinien, etc.'),
('fr_CA', 3, 'changement de position dans l''espace du corps ou d''une partie du corps d''un &ecirc;tre vivant. certains sont r&eacute;p&eacute;t&eacute;s pour l''apprentissage psychomoteur, l''exercice physique, la danse ou la r&eacute;&eacute;ducation.'),
('fr_CA', 4, 'un but, un r&eacute;sultat, que l''on cherche &agrave; atteindre. le point o&ugrave; l''on se propose d''arriver, ce que l''on vise. le mot est proche de la notion de fin.'),
('fr_CA', 6, 'r&eacute;f&egrave;re &agrave; une cat&eacute;gorie distincte, diff&eacute;rente des autres mentionn&eacute;es.'),
('fr_CA', 7, ''),
('fr_CA', 8, 'pr&eacute;cision de la caract&eacute;ristique que doit avoir un exercice. caract&eacute;ristique, pr&eacute;cision.'),
('fr_CA', 11, ''),
('fr_CA', 14, 'un &eacute;l&eacute;ment d''une activit&eacute; qui n''est qu''un moyen, un interm&eacute;diaire d''action, un instrument ayant l''objectif de r&eacute;aliser une op&eacute;ration d&eacute;termin&eacute;e.'),
('es_MX', 1, ''),
('es_MX', 3, ''),
('es_MX', 4, ''),
('es_MX', 6, ''),
('es_MX', 7, ''),
('es_MX', 8, ''),
('es_MX', 11, ''),
('nl_NL', 1, ''),
('nl_NL', 3, ''),
('nl_NL', 4, ''),
('nl_NL', 6, ''),
('nl_NL', 7, ''),
('nl_NL', 8, ''),
('pt_PT', 1, ''),
('pt_PT', 3, ''),
('pt_PT', 4, ''),
('pt_PT', 6, ''),
('pt_PT', 7, ''),
('pt_PT', 8, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
