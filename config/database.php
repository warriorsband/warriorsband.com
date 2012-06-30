<?php

/*
 *  database.php
 *
 *  Connect to the database with the provided parameters
 */

//Database connection parameters
$username = "root";
$password = "shbang111";
$hostname = "localhost";
$database = "database";

//You should not need to edit this
$mysqli = new mysqli("localhost", $username, $password, $database);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL database.";
}
?>
