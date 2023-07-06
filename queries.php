<?php

    function GetSql_CustIds($year, $month){
        $sql = 
        "
        SELECT DISTINCT d.cust_ID, MONTH(d.dispend) AS month, YEAR(d.dispend) AS year, d.dispapt
            FROM system.qb_revenue_items i
                LEFT JOIN myfbo_cz_copy.dispatches d ON d.dispatch_ID = i.dispatch_ID AND d.ddel = 0
            WHERE business_unit = 'CZ'
                AND MONTH(dispend) = $month AND YEAR(dispend) = $year
                AND i.item_type NOT IN ('FX', 'H', 'X', 'Z')
                GROUP BY d.cust_ID, d.dispapt;
        ";

        return $sql;
    }
?>