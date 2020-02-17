<?php

$filename = "config/log/application.log";
$fp = fopen($filename, 'rb');

// send the right headers
header("Content-Type: text/plain");
header("Content-Length: " . filesize($filename));

// dump the picture and stop the script
fpassthru($fp);
exit;

