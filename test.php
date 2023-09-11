<?php
    include_once '../pohoda_db.php';
    //include_once './ms_queries.php';

    $sql = "SELECT TOP 100 * FROM FA";

    $stmt = $ms_con->prepare($sql);

    $success = $stmt->execute();

    echo "Query stmt normal: " . $sql . "<br><pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    echo "</pre><br><br>";
/*
    $stmt = $ms_con->prepare($sql);

    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Query stmt fetchAll: " . $sql . "<br><pre>";
    print_r($data);
    echo "</pre><br><br>";
*/
?>