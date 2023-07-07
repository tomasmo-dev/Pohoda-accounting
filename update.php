<?php

exec('gitpull.bat', $out, $return);
echo "git pull done with return code: ".$return."<br>";
?>