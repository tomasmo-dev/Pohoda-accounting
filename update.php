<?php

exec('git pull', $out, $return);
echo "git pull done with return code: ".$return."<br>";
?>