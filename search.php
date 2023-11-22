<?php
require_once "config.php";
session_start();

$search = "";
$search_user = [];
$search_post = [];

if (isset($_GET["query"]))
?>
