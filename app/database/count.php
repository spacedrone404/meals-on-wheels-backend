<?php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'menus';
$port = '5432';           
$user = 'postgres';
$pass = 'DevDb4884_(_)#*';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $sql = "SELECT COUNT(*) FROM mainbase";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $count = $stmt->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => (int)$count]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>