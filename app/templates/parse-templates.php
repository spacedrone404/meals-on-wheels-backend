<?php
// Set headers for JSON response and CORS if needed
header('Content-Type: application/json');

try {
    // 1. Fetch and parse the Internal Database URL from Render's environment
    $db_env = getenv('DATABASE_URL');

    if (!$db_env) {
        throw new Exception("Error: DATABASE_URL environment variable is not set.");
    }

    $db_parsed = parse_url($db_env);

    $host = $db_parsed["host"];
    $port = $db_parsed["port"] ?? 5432;
    $user = $db_parsed["user"];
    $password = $db_parsed["pass"];
    $dbname = ltrim($db_parsed["path"], "/"); 

    // 2. Construct the DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    // 3. Connect using PDO (using $pdo consistently)
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // 4. Handle API Logic
    if (!empty($_GET['category']) && !empty($_GET['template'])) {
        $category = $_GET['category'];
        $template = $_GET['template'];
        
        $validTemplates = [
            'template_dinner_1',
            'template_dinner_2',
            'template_dinner_3',
            'template_dinner_4',
            'template_dinner_5',
            'template_cafe_1',
            'template_cafe_2',
            'template_cafe_3',
            'template_cafe_4',
            'template_breakfast_1',
            'template_breakfast_2',
            'template_breakfast_3'
        ];

        if (!in_array($template, $validTemplates)) {
            echo json_encode(['error' => 'Incorrect template']);
            exit;
        }
                
        // Using $pdo instead of undefined $dbh
        $stmt = $pdo->prepare("SELECT id, code, name, description, weight, category, price FROM \"$template\" WHERE category = :category ORDER BY code ASC");
        $stmt->execute([':category' => $category]);
        
        $results = $stmt->fetchAll();
        
        echo json_encode($results);
    } else {
        echo json_encode(['error' => 'Missing parameters']);
    }

} catch (Exception $e) {
    // Catches both connection errors and database query errors gracefully as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?>
