<?php

exec('C:\\inetpub\\wwwroot\\robot\\Pohoda-accounting\\gitpull.bat', $out, $return);
echo "git pull done with return code: ".$return."<br>";
?>