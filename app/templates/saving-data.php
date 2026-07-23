<?php
require dirname(__DIR__) . '../connection.php'; 

//$host = 'localhost';
//$dbname = 'menus';
//$user = 'postgres';
//$password = 'DevDb4884_(_)#*';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from POST request
    $data = json_decode(file_get_contents('php://input'), true);

    // Checking fields
    if (!$data || empty($data['template_id']) || empty($data['date_value'])) {
        http_response_code(400);
        echo json_encode(["error" => "Required fields missing"]);
        exit;
    }

    $template_id = $data['template_id'];
    $date_value = $data['date_value'];
    $menu_data = isset($data['menu_data']) ? $data['menu_data'] : '';
    $signatures = isset($data['signatures']) ? $data['signatures'] : '';

    try {
        $sql = "
            INSERT INTO settings (menudata, menuname, signatures, menushow)
            VALUES (:menudata, :menuname, :signatures, TRUE)
        ";

        $stmt = $pdo->prepare($sql);
        //$stmt->bindParam(':menuid', $template_id);
        $stmt->bindParam(':menudata', $date_value);
        $stmt->bindParam(':menuname', $template_id); // use template_id as menuname
        $stmt->bindParam(':signatures', $signatures);
        $stmt->execute();

        http_response_code(200);
        echo json_encode(["message" => "Date updated successfully"]);
    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

?>