<?php

/**
 * Description of remote_addr
 *
 * @author Samumon
 */
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
else $ip = "UNKNOWN";
echo $ip;
?>
