<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
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
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>