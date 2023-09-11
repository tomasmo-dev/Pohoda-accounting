<?php
// group of queries for MS SQL Server ( connection created in pohoda_db.php )
// connection var: $ms_con
// connection verified in pohoda_db.php

// if false is returned it means that the query returned no rows or connection failed
function SelectVarsym($varsym, $bal, $amount)
{
    $ms_con = $GLOBALS['ms_con'];

    $sql = "SELECT * FROM FA WHERE Varsym = :varsym AND Bal = :bal AND Amount = :amount";

    $stmt = $ms_con->prepare($sql);

    $stmt->execute([
        'varsym' => $varsym,
        'bal' => $bal,
        'amount' => $amount
    ]);

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);



    if ($row) {
        return $row;
    } else {
        return false;
    }
}

?>