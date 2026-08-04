<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $db_env = getenv('DATABASE_URL');
    if (!$db_env) {
        throw new Exception('DATABASE_URL environment variable is not set.');
    }

    $db_parsed = parse_url($db_env);
    $host     = $db_parsed['host'] ?? 'localhost';
    $port     = $db_parsed['port'] ?? 5432;
    $user     = $db_parsed['user'] ?? '';
    $password = $db_parsed['pass'] ?? '';
    $dbname   = ltrim($db_parsed['path'] ?? '', '/');

    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;client_encoding=UTF8",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    return $pdo;
}
?>