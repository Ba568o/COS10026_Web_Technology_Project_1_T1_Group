<?php
// Shared local MySQL settings for every Project 2 page.
// Keep the password empty as required by the assignment.
// Create the t1_health database once before using database_connection().
$host = 'localhost';
$user = 't1_local';
$password = '';
$database = 't1_health';
// Keep these values global even when this file is included from inside a function.
$GLOBALS['host'] = $host;
$GLOBALS['user'] = $user;
$GLOBALS['password'] = $password;
$GLOBALS['database'] = $database;

/** Returns one shared MySQL connection for the t1_health database. */
function database_connection(): PDO
{
    $host = $GLOBALS['host'];
    $user = $GLOBALS['user'];
    $password = $GLOBALS['password'];
    $database = $GLOBALS['database'];
    static $connection = null;
    // Later calls in the same request reuse this connection.
    if ($connection instanceof PDO) {
        return $connection;
    }
    // t1_local has an empty password, so $password is ''.
    $connection = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw on SQL errors.
        PDO::ATTR_EMULATE_PREPARES => false, // Let MySQL prepare the statement.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Rows come back as name => value.
    ]);
    return $connection;
}
