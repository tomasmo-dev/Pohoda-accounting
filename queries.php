<?php
// group of queries for MySql Server ( connection created in pohoda_db.php )
// connection var: $dbconnect
// connection verified in pohoda_db.php

    // get all rows from pohoda_import table for given year and month
    // returns an array of assoc arrays
    // if none are found empty array is returned
    // (empty array is also probably returned if error occurs)
    function GetInternalRecords($connection, $year, $month)
    {
        $sql = "SELECT * FROM system.pohoda_import WHERE year = ? AND month = ?;";

        $stmt = $connection->prepare($sql);

        $stmt->bind_param("ss", $year, $month);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
        }

        $stmt->close();

        return $rows;
    }

    // returns an array of customer ids for given month and year
    function GetCustIds($year, $month, $connection){
        $CustIds = array(); // all customer ids
        $ValidCustIds = array(); // customer ids with non zero amount to pay

        $Limit = LIMIT == -1 ? "" : "LIMIT ".LIMIT;

        $sql = 
        'SELECT DISTINCT d.cust_ID, MONTH(d.dispend) AS month, YEAR(d.dispend) AS year, d.dispapt '.
            'FROM system.qb_revenue_items i '.
                'LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0 '.
            'WHERE business_unit = \'CZ\' '.
                'AND MONTH(dispend) = ? AND YEAR(dispend) = ? '.
                'AND i.item_type NOT IN (\'FX\', \'H\', \'X\', \'Z\') '.
                'GROUP BY d.cust_ID, d.dispapt '.$Limit.'; ';

        $stmt = $connection->prepare($sql);

        $stmt->bind_param("ss", $month, $year);

        $stmt->execute();

        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($CustIds, strval($row["cust_ID"]));
            }
        }

        $stmt->close();

        // check if customer has non zero amount to pay
        if (count($CustIds) < 1) {
            echo "No customers found for given date";
            return array();
        }

        foreach ($CustIds as $custId) {
            $sql_Amount = 'SELECT SUM(COALESCE(exbeCZK, 0)) AS exbe, SUM(COALESCE(staxCZK, 0)) AS stax '.
                          'FROM system.qb_revenue_items i '.
                          'LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0 '.
                          'LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID '.
                          'WHERE business_unit = \'CZ\' '.
                          'AND MONTH(dispend) = ? AND YEAR(dispend) = ? '.
                          'AND i.item_type NOT IN (\'FX\', \'H\', \'X\', \'Z\') '.
                          'AND d.cust_ID = ?; ';

            $stmt_Amount = $connection->prepare($sql_Amount);
            $stmt_Amount->bind_param("sss", $month, $year, $custId);

            $stmt_Amount->execute();

            $result_Amount = $stmt_Amount->get_result();

            if ($result_Amount->num_rows > 0) {
                while($row_Amount = $result_Amount->fetch_assoc()) {
                    if (($row_Amount["exbe"] + $row_Amount["stax"]) == 0) {
                        continue;
                    } // if amount to pay is non zero
                    else
                    {
                        array_push($ValidCustIds, $custId);
                    }
                }
            }


            $stmt_Amount->close();
        }


        return $ValidCustIds;
    }

    function GetInvoiceInfoForUser($custId, $connection){
        // returns array of invoice information for given user
        $res = array();

        $sql = 'SELECT * FROM myfbo_cz_copy.customers WHERE Cust_ID = ?;';

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $custId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            if($result->num_rows == 1){
                $res = $result->fetch_assoc();
            }
            else{
                echo "Error: more than one customer found for given id {$custId}";
                return array();
            }
        }
        else{
            echo "Error: no customer found for given id {$custId}";
            return array();
        }

        $stmt->close();
        return $res;
    }

    function GetInvoiceItemsForUser($id, $year, $month, $connection){

        $items = array(); // array of invoice items

        // fetch invoice items for given user by date
        $sql = "SELECT c.LastName, c.FirstName, c.Cust_ID, c.home_apt,
                    d.dispatch_ID, i.revenue_type, SUM(COALESCE(exbeCZK, 0)) AS exbe, SUM(COALESCE(staxCZK, 0)) AS stax, i.item_type
                FROM system.qb_revenue_items i
                        LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
                    LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID
                WHERE business_unit = 'CZ'
                    AND MONTH(dispend) = ? AND YEAR(dispend) = ?
                    AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                    AND d.cust_ID = ?
                    AND stax != 0
                /*GROUP BY i.revenue_type*/
            UNION ALL
            SELECT c.LastName, c.FirstName, c.Cust_ID, c.home_apt,
                    d.dispatch_ID, i.revenue_type, SUM(COALESCE(exbeCZK, 0)) AS exbe, SUM(COALESCE(staxCZK, 0)) AS stax, i.item_type
                FROM system.qb_revenue_items i
                        LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
                    LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID
                WHERE business_unit = 'CZ'
                    AND MONTH(dispend) = ? AND YEAR(dispend) = ?
                    AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                    AND d.cust_ID = ?
                    AND stax = 0
                /*GROUP BY i.revenue_type*/
                ORDER BY revenue_type;";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssss", $month, $year, $id, $month, $year, $id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                if (!is_null($row['LastName']) && !is_null($row['FirstName'])) // check if row is valid
                {
                    array_push($items, $row);
                }
            }
        }

        $stmt->close();

        return $items;
    }

    function GetTotalPrice($id, $year, $month, $connection) // gets total price for customer id
    {
        $sql = "SELECT SUM(COALESCE(exbeCZK, 0)) AS exbe, SUM(COALESCE(staxCZK, 0)) AS stax
                    FROM system.qb_revenue_items i
                            LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
                        LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID
                    WHERE business_unit = 'CZ'
                        AND MONTH(dispend) = ? AND YEAR(dispend) = ?
                        AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                        AND d.cust_ID = ?;";

        $total_price = 0; // exbe + stax

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $month, $year, $id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) 
        {
            $row = $result->fetch_assoc();
            
            $total_price = $row['exbe'] + $row['stax'];
        }
        else
        {
            $total_price = false; // if error make it easily catchable
        }

        $stmt->close();
        return $total_price;
    }

    // myfbo id = "CZ{$custId}"
    function GetICO_DIC($myfboId, $connection) // try to get ico & dic from pohoda_adresar
    {
        $ico_dic = array("", "");

        $sql = "SELECT * FROM system.pohoda_adresar WHERE VPrMyFboID = ?;";
        $stmt = $connection->prepare($sql);

        $stmt->bind_param("s", $myfboId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) 
        {
            $row = $result->fetch_assoc();
            
            $ico_dic = array($row['ICO'], $row['DIC']);
        }
        else if($result->num_rows > 1)
        {
            $ico_dic_found = false;
            while ($row = $result->fetch_assoc()) {
                if ($row['DIC'] != "") {
                    $ico_dic_found = true;
                    $ico_dic = array($row['ICO'], $row['DIC']);
                    break;
                }
            }

            $stmt->close();

            if (!$ico_dic_found) { // try to fetch atleast ico when dic not present
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $myfboId);

                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows > 1)
                {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ICO'] != "") {
                            $ico_dic_found = true;
                            $ico_dic = array($row['ICO'], $row['DIC']);
                            break;
                        }
                    }
                }

                $stmt->close();
            }


            return $ico_dic_found == true ? $ico_dic : array("", ""); // if ico & dic not found return empty array
        }
        else if($result->num_rows < 1)
        {
            echo "Error: no customer found for given id {$myfboId}<br>";
            return array("", "");
        }

        $stmt->close();

        return $ico_dic;
    }

    function InvoicesPohodaImport($connection, $custId, $year, $month, $varsym, $bal, $amount)
    {
        $sql_check = "SELECT * FROM system.pohoda_import WHERE variable_symbol = ? AND year = ? AND month = ?;";
        $sql_update = "UPDATE system.pohoda_import SET balance = ?, amount = ? WHERE variable_symbol = ? AND year = ? AND month = ?;";
        $sql_insert = "INSERT INTO system.pohoda_import (variable_symbol, balance, amount, created_d, year, month, customer_id) VALUES (?, ?, ?, NOW(), ?, ?, ?);";

        $stmt_check = $connection->prepare($sql_check);
        $stmt_check->bind_param("sss", $varsym, $year, $month);

        $stmt_check->execute();

        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            //echo "Invoice already exists";
            $stmt_check->close();

            $stmt_update = $connection->prepare($sql_update);
            $stmt_update->bind_param("ddsss", $bal, $amount, $varsym, $year, $month);

            $stmt_update->execute();

            $stmt_update->close();
            return;
        }

        $stmt_check->close();

        // check complete, insert invoice        

        $stmt_insert = $connection->prepare($sql_insert);
        $stmt_insert->bind_param("sddsss", $varsym, $bal, $amount, $year, $month, $custId);

        $stmt_insert->execute();

        $stmt_insert->close();
    }

    function IndexDb($connection) // indexes tables so queries are faster
    {
        $connection->query("ALTER TABLE `system`.`qb_revenue_items`
                            ADD INDEX `index1` (`dispatch_ID` ASC),
                            ADD INDEX `index2` (`item_ID` ASC),
                            ADD INDEX `index3` (`item_type` ASC);");
        
        $connection->query("ALTER TABLE `myfbo_cz_copy`.`dispatches`
                            ADD INDEX `index2` (`cust_ID` ASC),
                            ADD INDEX `index3` (`dispend` ASC);");

        $connection->query("ALTER TABLE `system`.`pohoda_adresar`
                            ADD INDEX `index2` (`VPrMyFboID` ASC);");
    }
?>