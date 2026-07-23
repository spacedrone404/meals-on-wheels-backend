<?php

$host     = 'localhost';      
$port     = '5432';           
$dbname   = 'menus';         
$username = 'postgres';   
$password = 'DevDb4884_(_)#*'; 

try {

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};options='--client_encoding=UTF8'";
    $pdo = new PDO($dsn, $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "Connected to the database: '{$dbname}'" . PHP_EOL;

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