<?php

exec('git pull', $out, $return);
echo "asdfghjgit pull done with return code: ".$return."<br>";
?>