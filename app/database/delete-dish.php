<?php

require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
// Handling post request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];

    $stmt = $pdo->prepare('DELETE FROM mainbase WHERE code = :code');
    $stmt->execute(['code' => $code]);

    if ($stmt->rowCount()) {
        echo json_encode(["success" => true]); 
    } else {
        echo json_encode(["success" => false, "message" => "Error during removal"]);
    }
}
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>