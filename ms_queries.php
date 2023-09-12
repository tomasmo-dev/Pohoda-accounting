<?php
// group of queries for MS SQL Server ( connection created in pohoda_db.php )
// connection var: $ms_con
// connection verified in pohoda_db.php

// if false is returned it means that the query returned no rows or connection failed
function SelectVarsym($ms_con, $varsym, $bal, $amount, $verbose=true)
{

    $sql = "SELECT * FROM FA 
                WHERE VarSym=:varsym"; // check col names they are not correct rn

    $stmt = $ms_con->prepare($sql);
    $stmt->bindParam(':varsym', $varsym, PDO::PARAM_STR);

    $stmt->execute();

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$row)
    {
        return false;
    }

    $count = 0;
    foreach ($row as $r)
    {
        $count++;
    }

    if ($count == 1) {
        return $row[0];
    } 
    else if ($count == 0)
    {
        echo $verbose == true ? "No rows returned for varsym in MSSQL Pohoda: " . $varsym . "<br>" : "";
        return false;
    }
    else if ($count > 1)
    {
        echo $verbose == true ? "Multiple rows returned for varsym in MSSQL Pohoda: " . $varsym . "<br>" : "";
        return false;
    }
    else 
    {
        echo $verbose == true ? "Error in MSSQL Pohoda: " . $varsym . "<br>" : "";
        return false;
    }
}

?>