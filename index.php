<?php
    // error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    // excluded from git for security reasons
    // mainly contains $dbconnect variable
    // !!! REQUIRES CLOSING !!!
    include_once '../pohoda_db.php';
    // queries for pohoda database (they are long, so they are in separate file)
    include_once 'queries.php';

    $dateAssigned = false;

    if (isset($_POST['date'])) {
        $dateAssigned = true;

        if ($_POST['date'] != "") {
            
            $date = DateTime::createFromFormat('Y-m', $_POST['date']);
            
            $month = $date->format('m');
            $year = $date->format('Y');
        }
        else{
            $dateAssigned = false;
        }
    }

    function Get_UserIds_for_date($year, $month){
        // returns array of user ids for given date

        $CustIds = GetCustIds($year, $month, $GLOBALS['dbconnect']);

        return $CustIds;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pohoda</title>
    </head>
    <body>
        <form method="POST" action="">
            <input type="month" name="date">
            <input type="submit" value="Odeslat">
        </form>
        <br>
        <?php

        if ($dateAssigned) {
            $ids = Get_UserIds_for_date($year, $month);

            foreach ($ids as $cust_id) {
                echo $cust_id;
                echo "<br>";
            }
        }
        else{
            echo "no datum";
        }

        ?>
        <script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
        </script>
    </body>
</html>

<?php

    $dbconnect->close();
?>