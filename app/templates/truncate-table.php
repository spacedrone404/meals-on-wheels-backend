<?php
$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';


try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'truncate-table' && isset($data['template'])) {
            $template = $data['template'];
            
            $validTemplates = [
                'template_dinner_1', 'template_dinner_2', 'template_dinner_3', 'template_dinner_4', 'template_dinner_5',
                'template_cafe_1', 'template_cafe_2', 'template_cafe_3', 'template_cafe_4',
                'template_breakfast_1', 'template_breakfast_2', 'template_breakfast_3'
            ];

            // Check templates name 
            if (in_array($template, $validTemplates)) {
                // Getting the sequence name for the id column
                $sequenceName = "\"{$template}_id_seq\""; 
                
                $sqlTruncateTable = "TRUNCATE TABLE \"$template\" RESTART IDENTITY CASCADE;";
                $pdo->exec($sqlTruncateTable);
                                
                // If everything is OK return empty object
                echo json_encode([]);
            } else {
                echo json_encode(['error' => 'Incorrect template']);
            }
        } else {
            echo json_encode(['error' => 'Paramaters missing']);
        }
    } else {
        echo json_encode(['error' => 'Incorrect request']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error during processing: ' . $e->getMessage()]);
}
?>