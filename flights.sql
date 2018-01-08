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
-- Tabellenstruktur für Tabelle `flights`
--

CREATE TABLE `flights` (
  `id` int(32) NOT NULL,
  `source` int(2) DEFAULT NULL,
  `hex` varchar(6) DEFAULT NULL,
  `first_squawk` int(4) DEFAULT NULL,
  `last_squawk` int(4) DEFAULT NULL,
  `callsign` varchar(16) DEFAULT NULL,
  `first_latitude` decimal(10,6) DEFAULT NULL,
  `first_longitude` decimal(10,6) DEFAULT NULL,
  `last_latitude` decimal(10,6) DEFAULT NULL,
  `last_longitude` decimal(10,6) DEFAULT NULL,
  `first_altitude` int(16) DEFAULT NULL,
  `last_altitude` int(16) DEFAULT NULL,
  `first_vert_rate` int(16) DEFAULT NULL,
  `last_vert_rate` int(16) DEFAULT NULL,
  `first_track` int(16) DEFAULT NULL,
  `last_track` int(16) DEFAULT NULL,
  `first_speed` int(16) DEFAULT NULL,
  `last_speed` int(16) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `first_timestamp` datetime DEFAULT NULL,
  `last_timestamp` datetime DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `callsign` (`callsign`,`date`,`hex`,`source`),
  ADD KEY `first_timestamp` (`first_timestamp`),
  ADD KEY `last_timestamp` (`last_timestamp`),
  ADD KEY `date` (`date`),
  ADD KEY `hex` (`hex`),
  ADD KEY `callsign_2` (`callsign`),
  ADD KEY `timestamp` (`timestamp`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
