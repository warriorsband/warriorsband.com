<?php

/*
 *  register.php
 *  
 *  A form which posts to register-exec.php with the details required for creating 
 *  a new user.
 */

session_start();
$redirect_url = $_SERVER['PHP_SELF'];
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');

//Ensure that the user has exec level or above
ensure_minimum_type(2);
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>New Member Registration</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
  </head>

  <body>
    <center>
      <h2>New Member Registration</h2>
      <br />
<?php print_msg() ?>
      <form action="/users/register-exec.php" method="POST">
        <table>
          <tr>
            <th>E-mail</th>
            <td><input type="text" name="email" maxlength="255" /></td>
          </tr>
          <tr class="alt" >
            <th>First name</th>
            <td><input type="text" name="first_name" maxlength="255" /></td>
          </tr>
          <tr>
            <th>Last name</th>
            <td><input type="text" name="last_name" maxlength="255" /></td>
          </tr>
          <tr class="alt" >
            <th>Custom message</th>
            <td><input type="text" name="comment" maxlength="255" /></td>
          </tr>
        </table>
        <br />
        <input style="width:150px" type="submit" value="Register New Member" />
      </form>

      <br /><br />
      <a href="/index.php">Back to homepage</a>
    </center>
  </body>
</html>
