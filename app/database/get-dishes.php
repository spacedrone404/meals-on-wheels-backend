<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

try {
    $db_env = getenv('DATABASE_URL');
    if (!$db_env) {
        throw new Exception("Error: DATABASE_URL environment variable is not set.");
    }

    $db_parsed = parse_url($db_env);

    $host     = $db_parsed["host"] ?? 'localhost';
    $port     = $db_parsed["port"] ?? 5432;
    $user     = $db_parsed["user"] ?? '';
    $password = $db_parsed["pass"] ?? ''; 
    $dbname   = ltrim($db_parsed["path"] ?? '', "/"); 

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);

    $sql = 'SELECT *
            FROM mainbase
            ORDER BY CASE 
                WHEN type = \'Salads\' THEN 1
                WHEN type = \'Cold dishes\' THEN 2
                WHEN type = \'Soups\' THEN 3
                WHEN type = \'Fish\' THEN 4
                WHEN type = \'Meat\' THEN 5
                WHEN type = \'Dairy\' THEN 6
                WHEN type = \'Vegetables\' THEN 7
                WHEN type = \'Side\' THEN 8
                WHEN type = \'Bread\' THEN 9
                WHEN type = \'Drinks\' THEN 10
                WHEN type = \'Baked\' THEN 11
                ELSE 12
            END';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetchAll();

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>