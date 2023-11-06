<?php
// Database configuration, fill up with my own db host user
define('DB_SERVER','localhost');
define('DB_USERNAME','');
define('DB_PASSWORD','password');
define('DB_NAME','social_network');

// Mail server configuration
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect". mysqli_connect_error());
}
?>
