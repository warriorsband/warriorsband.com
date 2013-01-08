<?php

/*
 *  database.php
 *
 *  Connect to the database with the provided parameters
 */

//Database connection parameters
$username = "warriorsband";
$password = "ccwb2007";
$hostname = "localhost";
$database = "warriorsband_db";

//You should not need to edit this
$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL database.";
}
?>
