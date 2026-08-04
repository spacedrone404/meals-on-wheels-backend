<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();


//forming a query to select tables for which [menushow = true],
//i.e., they should be displayed on the screen for visitors

    $stmt = $pdo->prepare("SELECT menuname FROM settings WHERE menushow = true");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultsFormat = reset($results);
    if($resultsFormat){
        $firstElement = reset($resultsFormat);
        $stmt_2 = $pdo->prepare("SELECT * FROM $firstElement");
        $stmt_2->execute();
        $results_2 = $stmt_2->fetchAll(PDO::FETCH_ASSOC);


        $tableName = ['tableName' => $firstElement];   
        array_unshift($results_2, $tableName);



        echo json_encode($results_2);
    } else {

        echo json_encode(['error' => 'No correct tables to showcase']);
    };

	} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>

