

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'client');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'client');
define('DB_NAME', getenv('DB_NAME') ?: 'event_booking_system');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
