<?php

/*
 *  config.php
 *
 *  Contains database connection settings, and other variables used globally
 */

///////////////////////////////////////
//START OF USER CONFIGURATION/////////
/////////////////////////////////////

//Define MySQL database parameters

$username = "root";
$password = "shbang111";
$hostname = "localhost";
$database = "database";

//Define your canonical domain excluding trailing slash!, example:
$domain= "http://warriorsband.dyndns.org";

//Define length of salt,minimum=10, maximum=35
$length_salt=15;

//Define session timeout in seconds
//minimum 60 (for one minute)
$sessiontimeout=1800;

//Define login and 403 pages
$loginpage_url= $domain.'/auth/login.php';
$forbidden_url= $domain.'/403.php';
////////////////////////////////////
//END OF USER CONFIGURATION/////////
////////////////////////////////////

//DO NOT EDIT ANYTHING BELOW!

$dbhandle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");
$selected = mysql_select_db($database,$dbhandle)
or die("Could not select $database");
?>
