<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
    // Getting JSON from POST request
    $input = json_decode(file_get_contents('php://input'), true);

    if (!empty($input)) {
        $code = isset($input['code']) ? $input['code'] : null;
        $templateName = isset($input['templateName']) ? $input['templateName'] : null;

        // ID code and template name
        if ($code && $templateName) {

            $table = $templateName;

            $stmt = $pdo->prepare("DELETE FROM \"$table\" WHERE code = :code");
            $stmt->bindParam(':code', $code);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Error deleting row from table '$table'.");
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'One required parametr is missing!'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Required data is missing'
        ]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>