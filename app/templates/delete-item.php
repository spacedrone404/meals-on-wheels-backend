<?php
$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';
$password = 'DevDb4884_(_)#*';

header('Content-Type: application/json');

try {
    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Getting JSON from POST request
    $input = json_decode(file_get_contents('php://input'), true);

    if (!empty($input)) {
        $code = isset($input['code']) ? $input['code'] : null;
        $templateName = isset($input['templateName']) ? $input['templateName'] : null;

        // ID code and template name
        if ($code && $templateName) {

            $table = $templateName;

            $stmt = $dbh->prepare("DELETE FROM \"$table\" WHERE code = :code");
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
} catch (PDOException | Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
