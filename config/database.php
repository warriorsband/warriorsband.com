<?php

/*
 *  database.php
 *
 *  Connect to the database with the provided parameters
 */

$username = "root";
$password = "shbang111";
$hostname = "localhost";
$database = "database";

$dbhandle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");
$selected = mysql_select_db($database,$dbhandle)
or die("Could not select $database");
?>
