<?php

include 'credentials.php';

if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'client');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASSWORD') ?: 'client');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'event_booking_system');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', getenv('DB_PORT') ?: '5432');
}

function getDbConnection(): PgSql\Connection
{
    $connection = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASS);
    if (!$connection) {
        die('Error: Unable to connect to the database.');
    }
    return $connection;
}
