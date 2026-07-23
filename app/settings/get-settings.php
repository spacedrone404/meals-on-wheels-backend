<?php
$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';

// Takes the values of the menudata and signatures fields from the database according to the template name

try {
    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    if (isset($_GET['template'])) {
        $template = $_GET['template'];
        $stmt = $dbh->prepare("SELECT menudata, signatures, menushow FROM settings WHERE menuname = :template");
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
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>