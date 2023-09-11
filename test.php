<?php
    include_once '../pohoda_db.php';

    $row = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $varsym = $_POST['varsym'];
        echo "POST varsym: " . $varsym . "<br>";

        $sql = "SELECT * FROM FA
                    WHERE VarSym=:varsym";

        $stmt = $ms_con->prepare($sql);
        $stmt->bindParam(':varsym', $varsym, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    $sql = "SELECT TOP 1 * FROM FA";

    $stmt = $ms_con->prepare($sql);

    $success = $stmt->execute();

    echo "Query stmt normal: " . $sql . "<br><pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    echo "</pre><br><br>";

    $stmt = $ms_con->prepare($sql);

    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Query stmt fetchAll: " . $sql . "<br><pre>";
    print_r($data);
    echo "</pre><br><br>";
    */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Fetch row by varsym</h1>
    <form method="POST">
        <label for="varsym">Varsym</label>
        <input type="text" name="varsym" id="varsym" required>
        <input type="submit" value="Submit">
    </form>


    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            echo 'POST <br>';

            echo "<pre>";
            var_dump($row);
            echo "</pre>";
        }
    ?>
</body>
</html>