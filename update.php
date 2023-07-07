<?php

exec('git pull', null, $return);
echo "git pull done with return code: ".$return."<br>";
?>