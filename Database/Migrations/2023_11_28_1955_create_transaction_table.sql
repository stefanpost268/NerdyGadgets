-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 28 nov 2023 om 11:02
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
-- Tabelstructuur voor tabel `Transaction`
--

CREATE TABLE `Transaction` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('open','canceled','failed','paid') NOT NULL DEFAULT 'open',
  `payment` decimal(10,2) NOT NULL,
  `postalcode` varchar(255) NOT NULL,
  `housenr` varchar(255) NOT NULL,
  `residence` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `UserId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Transaction`
--

INSERT INTO `Transaction` (`id`, `transaction_id`, `status`, `payment`, `postalcode`, `housenr`, `residence`, `created_at`, `updated_at`, `UserId`) VALUES
(133, 'tr_q923Y2GoHJ', 'open', '49824.35', '7681 AD', '6', 'Vroomshoop', '2023-11-28 10:02:22', '2023-11-28 10:02:22', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Transaction`
--
ALTER TABLE `Transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Transaction`
--
ALTER TABLE `Transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Transaction`
--
ALTER TABLE `Transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `User` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
