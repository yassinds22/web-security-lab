<?php
// Database configuration
$host = '127.0.0.1';
$dbname = 'lab_security_db';
$db_user = 'root';
$db_pass = ''; // Set your MySQL password here

try {
    // Establishing PDO Connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // In production, do not leak raw error messages
    $db_error = "فشل الاتصال بقاعدة البيانات: " . $e->getMessage();
}
?>
