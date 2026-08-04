<?php

// Takes the values of the menudata and signatures fields from the database according to the template name
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
    if (isset($_GET['template'])) {
        $template = $_GET['template'];
        $stmt = $pdo->prepare("SELECT menudata, signatures, menushow FROM settings WHERE menuname = :template");
        $stmt->execute([':template' => $template]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $result['menushow'] = (bool)$result['menushow'];
            echo json_encode($result);
        } else {
            echo json_encode(['menudata' => null, 'signatures' => null, 'menushow' => false]);
        }
    } else {
        echo json_encode(['error' => 'Missing template parameter']);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>