-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 28 nov 2023 om 11:51
-- Serverversie: 10.4.27-MariaDB
-- PHP-versie: 8.0.25

-- With the help of query, you can add a new user to the database. This user has the right so select/see the tables listed with the
-- GRANT SELECT ON statement, it can add items to the tables listed with the GRANT INSERT ON statement, and it can update tables with
-- the GRANT UPDATE ON statement. This way, the user for the website has all the right and permissions it needs to let the site run 
-- smoothly, while also perserving the safety of the site by not granting it permissions it doesn't need.

CREATE USER 'nerdygadgetsuser'@'localhost' IDENTIFIED BY 'HalwHbrOLdr';

GRANT SELECT ON stockgroups TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON stockitemstockgroups TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON stockitems TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON stockitemholdings TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON stockitemimages TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON user TO 'nerdygadgetsuser'@'localhost';
GRANT SELECT ON transaction TO 'nerdygadgetsuser'@'localhost';

GRANT INSERT ON user TO 'nerdygadgetsuser'@'localhost';
GRANT INSERT ON transaction TO 'nerdygadgetsuser'@'localhost';
GRANT INSERT ON transactionbind TO 'nerdygadgetsuser'@'localhost';

GRANT UPDATE ON user TO 'nerdygadgetsuser'@'localhost';
GRANT UPDATE ON stockitemholdings TO 'nerdygadgetsuser'@'localhost';
GRANT UPDATE ON transaction TO 'nerdygadgetsuser'@'localhost';
