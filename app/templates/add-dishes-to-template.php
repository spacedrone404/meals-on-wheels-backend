<?php
$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';


$validTemplates = [
    'template_dinner_1', 'template_dinner_2', 'template_dinner_3', 'template_dinner_4', 'template_dinner_5',
    'template_cafe_1', 'template_cafe_2', 'template_cafe_3', 'template_cafe_4',
    'template_breakfast_1', 'template_breakfast_2', 'template_breakfast_3'
];


try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Getting input from a query
$data = json_decode(file_get_contents('php://input'), true);
$template = $data['template'] ?? '';
$dishes = $data['dishes'] ?? [];

// Template Check
if (!in_array($template, $validTemplates)) {
    echo json_encode(['error' => 'Invalid template']);
    exit;
}

// Checking an array of dishes
if (!is_array($dishes) || empty($dishes)) {
    echo json_encode(['error' => 'No dishes provided']);
    exit;
}

$successCount = 0;
$errors = [];
$skipped = [];

// Cycle through dishes
foreach ($dishes as $dishCode) {
    try {
        // Checking if a dish already exists in a template
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM \"$template\" WHERE code = :code");
        $checkStmt->execute([':code' => $dishCode]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $skipped[] = "Dish with code $dishCode already exists in $template";
            continue;
        }

        // Parsing dish parameters without price
        $stmt = $pdo->prepare('SELECT code, name, description, weight, type AS category FROM mainbase WHERE code = :code');
        $stmt->execute([':code' => $dishCode]);
        $dish = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dish) {
            
            $dish['price'] = '100'; // Default price

            //Inject to template
            $insertStmt = $pdo->prepare("INSERT INTO \"$template\" (code, name, description, weight, category, price) VALUES (:code, :name, :description, :weight, :category, :price)");
            $insertStmt->execute($dish);
            $successCount++;
        } else {
            //$errors[] = "Dish with code $dishCode not found in mainbase";
        }
    } catch (PDOException $e) {
        //$errors[] = "Error adding dish $dishCode: " . $e->getMessage();
    }
}

// Returning JSON
if ($successCount > 0 || !empty($skipped)) {
    echo json_encode(['success' => true, 'added' => $successCount, 'skipped' => $skipped, 'errors' => $errors]);
} else {
    echo json_encode(['success' => false, 'errors' => $errors]);
}
?>