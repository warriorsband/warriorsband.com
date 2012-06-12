<?php

/*
 *  index.php
 *
 *  The main Warriors Band page!
 */

session_start(); 
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
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
    <a href="/auth/login.php">Login page</a><br />
    <a href="/users/profile.php">View/Edit your profile</a><br />
    <a href="/users/users.php">View member list</a><br />
    <?php if (isset($_SESSION['logged_in'])) { ?><a href="/auth/logout.php">Logout</a><br /><?php } ?>
  </body>
</html>
