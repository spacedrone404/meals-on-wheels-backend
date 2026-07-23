<?php

//$host = 'localhost';
//$dbname = 'menus';
//$user = 'postgres';
//$password = 'DevDb4884_(_)#*';
//$charset = 'utf8mb4';

// $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

// 1. Fetch the Internal Database URL from Render's environment
$db_env = getenv('DATABASE_URL');

if (!$db_env) {
    die("Error: DATABASE_URL environment variable is not set.");
}

// 2. Parse the URL into its components
$db_parsed = parse_url($db_env);

$host = $db_parsed["host"];
$port = $db_parsed["port"] ?? 5432;
$user = $db_parsed["user"];
$password = $db_parsed["pass"];
// The path comes with a leading slash (e.g., "/menus"), so we trim it
$dbname = ltrim($db_parsed["path"], "/"); 

// 3. Construct the DSN (Data Source Name)
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

// 4. Connect using PDO
try {
    $pdo = new PDO($dsn, $user, $password, [
        // Enforce strict error throwing for easier debugging
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

try {
   
    // Displaying a list of dishes in specified templates
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
                
        $stmt = $dbh->prepare("SELECT id, code, name, description, weight, category, price FROM \"$template\" WHERE category = :category ORDER BY code ASC");
        $stmt->execute([':category' => $category]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($results);
    } else {
        echo json_encode(['error' => 'Missing parameters']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>