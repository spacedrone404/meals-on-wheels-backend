<?php
$host = 'localhost';
$dbname = 'menus';
$user = 'postgres'; 
$password = 'DevDb4884_(_)#*';     

// Parses all template data from the settings table


try {
    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    
    // Reading raw JSON from the requested body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    // Checking that 'templates' exists in the array
    if (isset($data['templates']) && is_array($data['templates'])) {
        $templates = $data['templates'];
        $dates = [];
        foreach ($templates as $template) {
            $stmt = $dbh->prepare("SELECT menudata, menushow FROM settings WHERE menuname = :template");
            $stmt->execute([':template' => $template]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $dates[$template] = $result ? ['date' => $result['menudata'], 'show' => (bool)$result['menushow']] : ['date' => null, 'show' => false];
        }
        // Returning dates as JSON
        echo json_encode($dates);
    } else {
        // If 'templates' are missing or invalid
        echo json_encode(['error' => 'No templates were provided or invalid format']);
    }
} catch (PDOException $e) {

    echo json_encode(['error' => $e->getMessage()]);
}
?>