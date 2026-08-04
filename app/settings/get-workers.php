<?php
// Takes the value of the signatures fields from the database according to the template name
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();
    
    // Request for non-empty names of signatories
    $stmt = $pdo->prepare("SELECT DISTINCT signatures FROM settings WHERE signatures IS NOT NULL AND signatures != '' ORDER BY signatures ASC");
    $stmt->execute();
    
    // Query all signatories as a single-column array
    $workers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Returning a list of signers in JSON
    echo json_encode($workers);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>