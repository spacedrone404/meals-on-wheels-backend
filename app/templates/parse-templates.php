<?php

$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';
$charset = 'utf8mb4';

$dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

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