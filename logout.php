<?php

/*
 *  logout.php
 *
 *  Ends a session if it exists.
 */

session_start();
require('config.php'); ?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
  </head>

  <body>
    <center>

<?php
//If the user is logged in, end the session and inform the user
if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in'] == TRUE)) {
  $_SESSION['logged_in'] = FALSE;
  session_destroy();   
  session_unset();
  echo "Logged out successfully.";
}
//Otherwise if the user was not logged in, tell them they're an idiot
else {
  echo "You must be logged in before you can log out.";
} ?>

      <br /><br />
      <a href="index.php">Back to homepage</a>
    </center>
  </body>
</html>
