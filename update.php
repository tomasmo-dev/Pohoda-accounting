<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$descriptorspec = [
    0 => ['pipe', 'r'], // stdin
    1 => ['pipe', 'w'], // stdout
    2 => ['pipe', 'w'], // stderr
];

$process = proc_open('git pull', $descriptorspec, $pipes, 'C:\\inetpub\\wwwroot\\robot\\Pohoda-accounting');

if (is_resource($process)) {
    $output = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);

    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $returnCode = proc_close($process);

    echo "git pull done with return code: $returnCode<br>";
    echo "git pull output:<br>";
    echo "<pre>$output</pre>";
    echo "git pull errors:<br>";
    echo "<pre>$errors</pre>";
    echo "<br>test";
}

?>