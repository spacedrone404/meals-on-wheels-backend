<?php
header('Content-Type: application/json');

$dsn = 'pgsql:host=localhost;port=5432;dbname=menus;user=postgres;password=DevDb4884_(_)#*';
try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Cant connect to the database: ' . $e->getMessage()]));
}

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data)) {
    http_response_code(400); // Code for Bad Request
    echo json_encode(['error' => 'No data in the request']);
    exit();
}

// Check required fields presence
$requiredFields = ['code', 'name', 'description', 'weight', 'type', 'workshop'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400); // Code for Bad Request
        echo json_encode(['error' => 'Field is missing: ' . $field]);
        exit();
    }
}

// Verify if a dish with given code already exists
$sql_check = "SELECT COUNT(*) FROM mainbase WHERE code=:code";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->bindValue(':code', intval($data['code']), PDO::PARAM_INT);
$stmt_check->execute();
$count = $stmt_check->fetchColumn();

if ($count > 0) { // If a dish with this code already exists
    http_response_code(409); // Conflict status code
    echo json_encode(['error' => 'This dish is already existing in the database!']);
    exit();
}

// Parse maximum element's ID
$sql_id_check = "SELECT COALESCE(MAX(id), 0) AS max_id FROM mainbase;";
$stmt_id = $pdo->query($sql_id_check);
$id_max = $stmt_id->fetchColumn();

$new_id = intval($id_max) + 1;

// Prepare SQL statement with correct data types
$sql = "INSERT INTO mainbase (id, code, name, description, weight, type, workshop)
        VALUES (:id, :code, :name, :description, :weight, :type, :workshop)";
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':id', $new_id, PDO::PARAM_INT); // Fields 'id' and 'code' are integers, others are strings
$stmt->bindValue(':code', intval($data['code']), PDO::PARAM_INT);
$stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
$stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
$stmt->bindValue(':weight', $data['weight'], PDO::PARAM_STR);
$stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
$stmt->bindValue(':workshop', $data['workshop'], PDO::PARAM_STR);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['message' => 'The dish successfuly added to the database']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Cant save the data, error: ' . implode(', ', $stmt->errorInfo())]);
}
?>