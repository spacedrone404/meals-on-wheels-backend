<?php

$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';

header('Content-Type: application/json');

try {

    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
   
    $requestMenuName = isset($_GET['menuname']) ? $_GET['menuname'] : null;

    // Define the operation: status update / get current status
    if ($requestMenuName !== null && !empty($requestMenuName)) {
        // Menus
        $stmt_1 = $pdo->prepare('UPDATE settings SET menushow = false WHERE menushow = true;');
        $stmt_2 = $pdo->prepare('UPDATE settings SET menushow = true WHERE menuname = :menuname;');
        
        $result_1 = $stmt_1->execute();
        $result_2 = $stmt_2->execute([':menuname' => $requestMenuName]);

        echo json_encode([
            'success' => $result_2,
            'error' => $result_2 ? null : 'Update failed',
        ]);
    } else {
        // We query the currently active menu and limit the number of selectable entries
        $stmt_select = $pdo->prepare('SELECT * FROM settings WHERE menushow = true LIMIT 1;');
        $stmt_select->execute();
        $active_menu = $stmt_select->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'active_menu' => $active_menu,
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>