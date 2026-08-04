<?php
require_once __DIR__ . '/../connection.php';

try {
    $pdo = getDbConnection();



    // RESTART IDENTITY -> reset auto increment
    // CASCADE -> external keys 
    $pdo->exec("TRUNCATE TABLE mainbase RESTART IDENTITY CASCADE;");

    echo "'mainbase' wiped successfully." . PHP_EOL;
    echo "All rows successfully cleared and ID seq reseted to 1" . PHP_EOL;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Close connection
$pdo = null;
?>