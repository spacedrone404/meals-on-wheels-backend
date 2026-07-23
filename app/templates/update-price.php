<?php
header('Content-Type: application/json');

require dirname(__DIR__) . '../connection.php'; 

try {
    
    $input = file_get_contents('php://input');
    $params = json_decode($input, true);
    

    if (!$params || !isset($params['id'], $params['price'], $params['template'])) {
        throw new Exception('Missing required field');
    }
    
    $id = intval($params['id']); // ID -> number 
    $newPrice = strval($params['price']); // Price -> string
    $template = $params['template']; // Template name

    $allowedTables = [
        'template_dinner_1',
        'template_dinner_2',
        'template_dinner_3',
        'template_dinner_4',
        'template_dinner_5',
        'template_breakfast_1',
        'template_breakfast_2',
        'template_breakfast_3',
        'template_cafe_1',
        'template_cafe_2',
        'template_cafe_3',
        'template_cafe_4',
        'template_cafe_5'
    ];

    // Protection from invalid injection
    if (!in_array($template, $allowedTables)) {
        throw new Exception('Incorrect template');
    }

    // Request to update price in the selected template
    $sql = "UPDATE {$template} SET price = :newPrice WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newPrice', $newPrice, PDO::PARAM_STR); // Price as string
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ID as number
    $result = $stmt->execute();

    if ($result === false) { 
        throw new Exception('Error changing the price');
    }
    echo json_encode(['status' => 'ok']); 
} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['error' => $e->getMessage()]); 
}
?>