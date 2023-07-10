<?php

    // returns an array of customer ids for given month and year
    function GetCustIds($year, $month, $connection){
        $CustIds = array();
        $ValidCustIds = array();

        $sql = 
        'SELECT DISTINCT d.cust_ID, MONTH(d.dispend) AS month, YEAR(d.dispend) AS year, d.dispapt '.
            'FROM system.qb_revenue_items i '.
                'LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0 '.
            'WHERE business_unit = \'CZ\' '.
                'AND MONTH(dispend) = ? AND YEAR(dispend) = ? '.
                'AND i.item_type NOT IN (\'FX\', \'H\', \'X\', \'Z\') '.
                'GROUP BY d.cust_ID, d.dispapt LIMIT 5; ';

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
            $sql_Amount = 'SELECT SUM(COALESCE(exbeUSD, 0)) AS exbe, SUM(COALESCE(staxUSD, 0)) AS stax '.
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
                    if ($row_Amount["exbe"] + $row_Amount["stax"] > 0) {
                        array_push($ValidCustIds, $custId);
                    }
                }
            }


            $stmt_Amount->close();
        }


        return $CustIds;
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
                    d.dispatch_ID, i.revenue_type, SUM(COALESCE(exbeUSD, 0)) AS exbe, SUM(COALESCE(staxUSD, 0)) AS stax, i.item_type
                FROM system.qb_revenue_items i
                        LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
                    LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID
                WHERE business_unit = 'CZ'
                    AND MONTH(dispend) = {$month} AND YEAR(dispend) = {$year}
                    AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                    AND d.cust_ID = {$id}
                    AND stax != 0
                /*GROUP BY i.revenue_type*/
            UNION ALL
            SELECT c.LastName, c.FirstName, c.Cust_ID, c.home_apt,
                    d.dispatch_ID, i.revenue_type, SUM(COALESCE(exbeUSD, 0)) AS exbe, SUM(COALESCE(staxUSD, 0)) AS stax, i.item_type
                FROM system.qb_revenue_items i
                        LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
                    LEFT JOIN myfbo_cz_copy.customers c ON c.Cust_ID = d.cust_ID
                WHERE business_unit = 'CZ'
                    AND MONTH(dispend) = {$month} AND YEAR(dispend) = {$year}
                    AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                    AND d.cust_ID = {$id}
                    AND stax = 0
                /*GROUP BY i.revenue_type*/
                ORDER BY revenue_type;";

        $result = $connection->query($sql);

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

        return $items;
    }
?>