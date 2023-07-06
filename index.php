<?php
    // excluded from git for security reasons
    // mainly contains $dbconnect variable
    // !!! REQUIRES CLOSING !!!
    include_once '../pohoda_db.php'; 

    if (isset($_POST['date'])) {
        echo $_POST['date'];

        $date = DateTime::createFromFormat('Y-m', $_POST['date']);

        echo $date->format('Y');
        echo "<br>";
        echo $date->format('m');
    }


    $dbconnect->close();
?>

<?php
    // separate php section for function definitions

    function Get_UserIds_for_date($year, $month){
        // returns array of user ids for given date
        $CustIds = array();
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
</body>
</html>