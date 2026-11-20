-- Run as an administrator in phpMyAdmin's SQL tab or MySQL Workbench.
-- This leaves the existing root password unchanged.
CREATE DATABASE IF NOT EXISTS t1_health CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 't1_local'@'localhost' IDENTIFIED BY '';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER
ON t1_health.* TO 't1_local'@'localhost';
-- If t1_local already exists, this script does not reset its password.
