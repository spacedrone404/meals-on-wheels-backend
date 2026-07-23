<?php

$host = 'localhost'; 
$dbname = 'menus';   
$user = 'postgres';  
$password = 'DevDb4884_(_)#*';      

try {    
    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);   

//forming a query to select tables for which [menushow = true],
//i.e., they should be displayed on the screen for visitors

    $stmt = $dbh->prepare("SELECT menuname FROM settings WHERE menushow = true");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultsFormat = reset($results);
    if($resultsFormat){
        $firstElement = reset($resultsFormat);
        $stmt_2 = $dbh->prepare("SELECT * FROM $firstElement");
        $stmt_2->execute();
        $results_2 = $stmt_2->fetchAll(PDO::FETCH_ASSOC);


        $tableName = ['tableName' => $firstElement];   
        array_unshift($results_2, $tableName);



        echo json_encode($results_2);
    } else {

        echo json_encode(['error' => 'No correct tables to showcase']);
    };

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

