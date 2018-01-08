-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 08. Jan 2018 um 09:55
-- Server-Version: 5.7.20-0ubuntu0.17.10.1
-- PHP-Version: 7.1.11-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `flightlog_core`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `positions`
--

CREATE TABLE `positions` (
  `id` int(32) NOT NULL,
  `source` int(16) DEFAULT NULL,
  `hex` varchar(6) DEFAULT NULL,
  `squawk` int(4) DEFAULT NULL,
  `flight` varchar(16) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `altitude` int(16) DEFAULT NULL,
  `vert_rate` int(16) DEFAULT NULL,
  `track` int(16) DEFAULT NULL,
  `speed` int(16) DEFAULT NULL,
  `messages` int(16) DEFAULT NULL,
  `seen` int(16) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_2` (`source`,`hex`,`squawk`,`flight`,`date`,`timestamp`),
  ADD KEY `source` (`source`),
  ADD KEY `hex` (`hex`),
  ADD KEY `flight` (`flight`),
  ADD KEY `date` (`date`),
  ADD KEY `timestamp` (`timestamp`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
