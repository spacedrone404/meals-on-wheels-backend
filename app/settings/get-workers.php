<?php

$host = 'localhost';
$dbname = 'menus';
$user = 'postgres';  
$password = 'DevDb4884_(_)#*';     


// Takes the value of the signatures fields from the database according to the template name

try {

    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    
    // Request for non-empty names of signatories
    $stmt = $dbh->prepare("SELECT DISTINCT signatures FROM settings WHERE signatures IS NOT NULL AND signatures != '' ORDER BY signatures ASC");
    $stmt->execute();
    
    // Query all signatories as a single-column array
    $workers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Returning a list of signers in JSON
    echo json_encode($workers);
} catch (PDOException $e) {
    
    echo json_encode(['error' => $e->getMessage()]);
}
?>