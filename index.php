<?php

/*
 *  index.php
 *
 *  The main Warriors Band page!
 */

session_start(); 
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
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
<?php print_msg(); ?>
      <h1>WARRIORS BAND</h1>
      <h3>"One of the Bands in Canada"</h3>
      <br />
      <br />
<?php if (isset($_SESSION['first_name'])) {
  echo "Hello, ".$_SESSION['first_name']."!<br /><br />";
} ?>
      <table class="noborder">
        <tr class="noborder"><td class="noborder">
          <a href="/auth/login.php">Login page</a><br />
        </td></tr>
        <tr class="noborder"><td class="noborder">
          <a href="/users/profile.php">View/Edit your profile</a><br />
        </td></tr>
        <tr class="noborder"><td class="noborder">
          <a href="/users/users.php">View member list</a><br />
        </td></tr>
<?php if (logged_in()) {
if (user_type_greater_eq(2)) { ?>
        <tr class="noborder"><td class="noborder">
          <a href="/users/register.php">Register a new user</a><br />
        </td></tr>
<?php } ?>
        <tr class="noborder"><td class="noborder">
          <a href="/auth/logout.php">Logout</a><br />
        </td></tr>
<?php } ?>
      </table>
    </center>
  </body>
</html>
