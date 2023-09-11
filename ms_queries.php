<?php
// group of queries for MS SQL Server ( connection created in pohoda_db.php )
// connection var: $ms_con
// connection verified in pohoda_db.php

// if false is returned it means that the query returned no rows or connection failed
function SelectVarsym($varsym, $bal, $amount)
{
    $ms_con = $GLOBALS['ms_con'];

    $sql = "SELECT * FROM FA 
                WHERE VarSym=:varsym"; // check col names they are not correct rn

    $stmt = $ms_con->prepare($sql);

    $stmt->execute([
        'varsym' => $varsym
    ]);

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$row)
    {
        return false;
    }

    $count = 0;
    foreach ($row as $key => $assocArr) {
        $count++;
    }

    if ($count == 1) {
        return $row[0];
    } else {
        return false;
    }
}

?>