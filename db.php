<?php
// db.php
// Simple PDO helper for connecting to the Mint OPD database.
// Usage: require_once __DIR__ . '/db.php';
// Then use $pdo for queries.

$config = require __DIR__ . '/config.php';

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config['host'],
    $config['port'],
    $config['dbname'],
    $config['charset']
);

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<h1>Database connection failed</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}
