<?php

    // returns an array of customer ids for given month and year
    function GetCustIds($year, $month, $connection){
        $CustIds = array();

        $sql = 
        'SELECT DISTINCT d.cust_ID, MONTH(d.dispend) AS month, YEAR(d.dispend) AS year, d.dispapt '.
            'FROM system.qb_revenue_items i '.
                'LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0 '.
            'WHERE business_unit = \'CZ\' '.
                'AND MONTH(dispend) = \'?\' AND YEAR(dispend) = \'?\' '.
                'AND i.item_type NOT IN (\'FX\', \'H\', \'X\', \'Z\') '.
                'GROUP BY d.cust_ID, d.dispapt; ';


        $stmt = $connection->prepare($sql);
        echo 'stmt prepared';

        $stmt->bind_param("ss", $month, $year);
        echo 'stmt binded';

        $stmt->execute();
        echo 'stmt executed';

        $result = $stmt->get_result();
        echo 'stmt result';

        echo $connection->error;
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($CustIds, $row["cust_ID"]);
            }
        }

        $stmt->close();
        return $CustIds;
    }
?>