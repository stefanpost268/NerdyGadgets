-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 28 nov 2023 om 11:51
-- Serverversie: 10.4.27-MariaDB
-- PHP-versie: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nerdygadgets`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `TransactionBind`
--

CREATE TABLE `TransactionBind` (
  `transactionId` int(11) NOT NULL,
  `stockitemId` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `TransactionBind`
--

INSERT INTO `TransactionBind` (`transactionId`, `stockitemId`, `amount`) VALUES
(138, 78, 50);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `TransactionBind`
--
ALTER TABLE `TransactionBind`
  ADD PRIMARY KEY (`transactionId`,`stockitemId`),
  ADD KEY `stockitemId` (`stockitemId`);

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `TransactionBind`
--
ALTER TABLE `TransactionBind`
  ADD CONSTRAINT `transactionbind_ibfk_1` FOREIGN KEY (`transactionId`) REFERENCES `Transaction` (`id`),
  ADD CONSTRAINT `transactionbind_ibfk_2` FOREIGN KEY (`stockitemId`) REFERENCES `stockitems` (`StockItemID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
