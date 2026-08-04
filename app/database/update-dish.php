<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];	
    $code = $_POST['code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $weight = $_POST['weight'];
    $type = $_POST['type'];
    $workshop = $_POST['workshop'];


    // Check for duplicate code on another row
    $dup = $pdo->prepare('SELECT id FROM mainbase WHERE code = :code AND id <> :id LIMIT 1');
    $dup->execute(['code' => $code, 'id' => $id]);
    if ($dup->fetch()) {
        echo json_encode(["success" => false, "message" => "Dish ID already exists"]);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE mainbase SET code=:code, name=:name, description=:description, weight=:weight, type=:type, workshop=:workshop WHERE id=:id');
    $stmt->execute([
        'id' => $id,
        'code' => $code,
        'name' => $name,
        'description' => $description,
        'weight' => $weight,
        'type' => $type,
        'workshop' => $workshop,
    ]);

    if ($stmt->rowCount()) {
        echo json_encode(["success" => true]); 
    } else {
        echo json_encode(["success" => false, "message" => "Error during update"]);
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