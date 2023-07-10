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
    // includes all functions for working with xml invoices
    include_once 'xml_format.php';

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

    function test($year, $month){
        // returns array of user ids for given date

        date_default_timezone_set('Europe/Prague');

        $CustIds = GetCustIds($year, $month, $GLOBALS['dbconnect']);

        foreach ($CustIds as $id) {
            echo 'Id : '.$id ."<br>";
            $info = GetInvoiceInfoForUser($id, $GLOBALS['dbconnect']);

            $invoice_id = "{$year}-{$month}-{$id}"; // year-month-cust_id

            $created_d = date('d. m. Y - H:i');
            $invoice_d = date('t. m. Y');

            $description = "Pilot training";

            $bank_account = "nevim";
            $company_name = "nevim co sem napsat";
            $full_name = "nevim co sem napsat2";
            $city = "nevim co sem napsat3";
            $address = "nevim co sem napsat4";
            $zip = "nevim zip";
            $ico = "nevim ico";
            $vat = "nevim vat";

            $invoice_xml = RetrieveXml($invoice_id, $created_d, $invoice_d, $invoice_d, $description, $bank_account, $company_name, 
                                       $full_name, $city, $address, $zip, $ico, $vat);

            echo '<textarea style=\'border: none;\'>';
            echo $invoice_xml;
            echo '</textarea>';

            echo "--------------------------------------------<br><br>";
        }
    }

?>

<!-- ------------------------- HTML ------------------------- -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pohoda</title>

        <style>
            body{
                margin:0;
                padding:0;
            }
            #load{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                background:url("assets/loaders/Hourglass.gif") no-repeat center center rgba(0,0,0,0.25);

                visibility: hidden;
            }
        </style>
    </head>
    <body>
        <div id="load"></div>
        <form method="POST" action="">
            <input type="month" name="date">
            <input type="submit" value="Odeslat" onclick="load()">
        </form>
        <br>
        <?php

        if ($dateAssigned) {
            test($year, $month);
        }
        else{
            echo "no datum";
        }

        ?>
        <script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }

            function load(){
                document.getElementById("load").style.visibility = "visible";
            }
        </script>
    </body>
</html>

<?php

    $dbconnect->close();
?>