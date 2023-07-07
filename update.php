<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir('C:\\inetpub\\wwwroot\\robot\\Pohoda-accounting');
$output = shell_exec('git pull');

echo "git pull output:<br>";
echo "<pre>$output</pre>";
?>