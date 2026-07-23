<?php
$host = 'localhost';
$db   = 'menus';
$user = 'postgres'; 
$pass = 'DevDb4884_(_)#*'; 
$charset = 'utf8mb4';


header('Content-Type: application/json');

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die(json_encode(['error' => 'Error connecting to the database: ' . $e->getMessage()]));
}

$stmt = $pdo->prepare(
    'SELECT *
     FROM mainbase
     ORDER BY CASE 
                WHEN type = \'Salads\' THEN 1
                WHEN type = \'Cold dishes\' THEN 2
                WHEN type = \'Soups\' THEN 3
                WHEN type = \'Fish\' THEN 4
                WHEN type = \'Meat\' THEN 5
                WHEN type = \'Dairy\' THEN 6
                WHEN type = \'Vegetables\' THEN 7
                WHEN type = \'Side\' THEN 8
                WHEN type = \'Bread\' THEN 9
                WHEN type = \'Drinks\' THEN 10
                WHEN type = \'Baked\' THEN 11
              END'
);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// return JSON
echo json_encode($data);
?>