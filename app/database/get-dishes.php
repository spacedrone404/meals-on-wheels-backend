<?php
// Set headers for JSON response
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

try {
    // 1. Fetch and parse the Database URL from environment variables
    $db_env = getenv('DATABASE_URL');

    // NOTE: For local development, you can set this in your .env or server config.
    // Example: DATABASE_URL="pgsql://postgres:pass@localhost:5432/menus"
    if (!$db_env) {
        throw new Exception("Error: DATABASE_URL environment variable is not set.");
    }

    $db_parsed = parse_url($db_env);

    $host     = $db_parsed["host"] ?? 'localhost';
    $port     = $db_parsed["port"] ?? 5432;
    $user     = $db_parsed["user"] ?? '';
    $password = $db_parsed["pass"] ?? ''; 
    $dbname   = ltrim($db_parsed["path"] ?? '', "/"); 

    // 2. Construct the DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    // 3. Connect using PDO with secure attributes
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);

    // 4. Handle API Logic
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

    // Using prepare/execute consistently, even for static queries, is a good habit
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetchAll();

    // Return JSON
    echo json_encode($data);

} catch (Exception $e) {
    // Catches both connection errors and database query errors gracefully as JSON
    // In production, you may want to log $e->getMessage() to a file and return a generic error to the client
    echo json_encode(['error' => $e->getMessage()]);
}
?>