-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Ven 08 Mars 2013 à 14:40
-- Version du serveur: 5.5.28
-- Version de PHP: 5.4.4-14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `promo_konkours`
--

-- --------------------------------------------------------

--
-- Structure de la table `contests_to_prizes`
--

CREATE TABLE IF NOT EXISTS `contests_to_prizes` (
  `contest_id` mediumint(8) unsigned NOT NULL,
  `prize_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`contest_id`,`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
