<?php
// Adds a new date to the template when the Save button is clicked.
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['template']) && isset($input['date'])) {
        $template = $input['template'];
        $date = $input['date'];
        
        $stmt = $pdo->prepare("SELECT id FROM settings WHERE menuname = :template");
        $stmt->execute([':template' => $template]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) { // Record exists
            $stmt = $pdo->prepare("UPDATE settings SET menudata = :date WHERE menuname = :template");
            $stmt->execute([':date' => $date, ':template' => $template]);
        } else { // No record
            $stmt = $pdo->prepare("INSERT INTO settings (menuname, menudata) VALUES (:template, :date)");
            $stmt->execute([':template' => $template, ':date' => $date]);
        }

        echo json_encode(['success' => true]); 
    } else {
        http_response_code(400); 
        echo json_encode(['error' => 'Template or date parameters were not passed.']); 
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>