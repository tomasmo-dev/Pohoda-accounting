<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$return = 0;
$out = array();

chdir('C:\\inetpub\\wwwroot\\robot\\Pohoda-accounting');
exec('git pull > "C:\path\to\log\output.txt" 2> "C:\path\to\log\errors.txt"', $out, $return);

echo "git pull done with return code: ".$return."<br>";
?>