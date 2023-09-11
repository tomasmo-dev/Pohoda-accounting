<?php
    include_once '../pohoda_db.php';
    include_once './ms_queries.php';

    $sql = "SELECT * FROM FA";

    $stmt = $ms_con->prepare($sql);

    $stmt->execute();

    echo "Query: " . $sql . "<br><pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    echo "</pre>";

?>