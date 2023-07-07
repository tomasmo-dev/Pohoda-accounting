<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$return = 0;
$out = array();

exec('C:\\inetpub\\wwwroot\\robot\\Pohoda-accounting\\gitpull.bat', $out, $return);
echo "git pull done with return code: ".$return."<br>";
?>