<?php
// Load Composer Autoloader & Dotenv
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'frsdb';

$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if(mysqli_connect_errno()){
    error_log("DB connection failed: " . mysqli_connect_error());
    die("Database connection error. Please try again later.");
}

// Ensure the connection speaks utf8mb4 so Vietnamese text is not corrupted.
// Tables are utf8mb4, but the client connection defaults to the server charset
// (often latin1) unless explicitly set.
mysqli_set_charset($con, 'utf8mb4');
?>
