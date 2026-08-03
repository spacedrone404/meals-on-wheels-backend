<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../connection.php';
    
    $sql = "SELECT COUNT(*) FROM mainbase";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $count = $stmt->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => (int)$count]);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Ошибка при выборке данных из БД'
    ], JSON_UNESCAPED_UNICODE);
}
?>