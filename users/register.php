<?php

/*
 *  register.php
 *  
 *  A form which posts to register-exec.php with the details required for creating 
 *  a new user.
 */

session_start();
$redirect_url = $_SERVER['PHP_SELF'];
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');
//All the permissions and error message functions are stored here for readability
require($_SERVER['DOCUMENT_ROOT'].'/config/profile-functions.php');

//Sanitize input
function sanitize($data){
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

//Outputs 'class="alt"' on even-numbered rows, so that it can be used to define the 
//class of rows and :alt" can be given a different colour in the css style
function row_color() {
  static $row_count = 0;
  if ($odd = ++$row_count % 2) {
    return '';
  } else {
    return 'class="alt"';
  }
}

//Ensure that the user has exec level or above
if ($_SESSION['user_type'] < 2) {
  echo "Members cannot register other members.";
  exit();
} ?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>New Member Registration</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
      <h2>New Member Registration</h2>
      <br />
<?php if (isset($_GET['error'])) { }?>
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
              <th>User type</th>
              <td>
                <input type="radio" name="user_type" value="1" /> Member
                <input type="radio" name="user_type" value="2" /> Exec
                <br />
                <input type="radio" name="user_type" value="3" /> Admin Exec
                <input type="radio" name="user_type" value="4" /> Admin
              </td>
            </tr>
        </table>
        <br /><br />
        <input style="width:150px" type="submit" value="Register New Member" />
      </form>

      <br /><br />
      <a href="/index.php">Back to homepage</a>
    </center>
  </body>
</html>
