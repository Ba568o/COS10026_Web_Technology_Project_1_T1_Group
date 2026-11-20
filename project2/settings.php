<?php
// Shared local MySQL settings for every Project 2 page.
// Keep the password empty as required by the assignment.
// Create the t1_health database once before using database_connection().
$host = 'localhost';
$user = 't1_local';
$password = '';
$database = 't1_health';

function database_connection(): PDO
{
    global $host, $user, $password, $database;
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }
    $connection = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $connection;
}
