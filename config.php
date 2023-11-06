<?php
define('DB_SERVER','localhost');
define('DB_USERNAME','timc6t');
define('DB_PASSWORD','password');
define('DB_NAME','social_network');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect" . mysqli_connect_error());
}
?>
