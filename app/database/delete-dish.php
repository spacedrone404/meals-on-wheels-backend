<?php

$host = 'localhost';
$db   = 'menus';
$user = 'postgres';
$pass = 'DevDb4884_(_)#*'; 

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die('Error connecting to the database: ' . $e->getMessage());
}

// Handling post request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];

    $stmt = $pdo->prepare('DELETE FROM mainbase WHERE code = :code');
    $stmt->execute(['code' => $code]);

    if ($stmt->rowCount()) {
        echo json_encode(["success" => true]); 
    } else {
        echo json_encode(["success" => false, "message" => "Error during removal"]);
    }
}
?>