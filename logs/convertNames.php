<?php
$files = glob("log_*");
for($i = 0; $i < count($files); $i++)
{
    $date = explode(".", explode("_", $files[$i])[1])[0];
    $timestamp = strtotime($date);
    rename($files[$i], "log_" . date("Y-m-d", $timestamp) . ".log");
}
?>
