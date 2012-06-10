<?php

/*
 *  index.php
 *
 *  The main Warriors Band page!
 */
session_start(); 
require('timeout.php');
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
  </head>

  <body>
    <center>
      <h1>WARRIORS BAND</h1>
      <h3>"One of the Bands in Canada"</h3>
    </center>
    <br />
    <br />
<?php if (isset($_SESSION['first_name'])) {
  echo "Hello, ".$_SESSION['first_name']."!<br /><br />";
} ?>
    <a href="secured.php">Super secret secure page</a><br />
    <a href="login.php">Login page</a><br />
    <a href="profile.php">View/Edit your profile</a><br />
    <?php if (isset($_SESSION['logged_in'])) { ?><a href="logout.php">Logout</a><br /><?php } ?>
  </body>
</html>
