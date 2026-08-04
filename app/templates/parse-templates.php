<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();

    // Handle API Logic
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

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>
