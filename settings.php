<?php

/*
 *  preferences.php
 *  
 *  Allows an authenticated user to change their account preferences.
 */

session_start();
$redirect_url = $_SERVER['PHP_SELF'];
require('auth.php');
require_once('timeout.php');

//If no user ID is provided, assume the user is modifying their own account.
//(note that since we've included auth.php, we can assume the user is logged in
//and thus that $_SESSION['used_id'] is set and valid)
if (!isset($_GET['user_id'])) {
  $user_id = $_SESSION['user_id'];
}
//Otherwise, check if the user has the authority to modify the requested account;
//if not, display an error and exit, and if so, proceed
else {
  $user_id = $_GET['user_id'];

  //Regular members cannot modify other users' preferences
  if (($_SESSION['user_type'] == 1) && ($_SESSION['user_id'] != $user_id)) {
    echo "You do not have permission to edit this user's settings."
    exit();
  }
}

//Get all the user's details from the database; we'll need most of it anyway
$row = mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `user_id`='".$user_id."'"));
$user_type = intval(row['user_type']);
if ($user_type == 1) {
  $user_label = "Member";
  $check1 = "checked=\"checked\"";
  $check2 = "";
  $check3 = "";
} elseif ($user_type == 2) {
  $user_label = "Exec";
  $check1 = "";
  $check2 = "checked=\"checked\"";
  $check3 = "";
} elseif ($user_type == 3) {
  $user_label = "Admin";
  $check1 = "";
  $check2 = "";
  $check3 = "checked=\"checked\"";
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band Preferences</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
    <h2>Account Preferences</h2>
    <br />

    <form action="settings-exec.php" method="POST">
      <table>
        <tr>
          <th>First name</th>
          <td>
<?php if ($_SESSION['user_type'] >= 2) { ?>
            <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>">
<?php } else { echo $row['first_name']; } ?>
          </td>
        </tr>
        <tr class="alt">
          <th>Last name</th>
          <td>
<?php if ($_SESSION['user_type'] >= 2) { ?>
            <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>">
<?php } else { echo $row['last_name']; } ?>
          </td>
        </tr>
        <tr>
          <th>E-mail address</th>
          <td>
<?php if ($_SESSION['user_type'] >= 2) { ?>
            <input type="text" name="email" value="<?php echo $row['email']; ?>">
<?php } else { echo $row['email']; } ?>
          </td>
        </tr>
<?php if ($_SESSION['user_type'] == 2) { ?>
        <tr class="alt">
          <th>User type</th>
          <td><?php echo $user_label; ?></td>
        </tr>
<?php } elseif ($_SESSION['user_type'] == 3) { ?>
        <tr class="alt">
          <th>User type</th>
          <td>
            <input type="radio" name="user_type" value="1" <?php echo $check1; ?> /> Member
            <input type="radio" name="user_type" value="2" <?php echo $check2; ?> /> Exec
            <input type="radio" name="user_type" value="3" <?php echo $check3; ?> /> Admin
          </td>
        </tr>
<?php } ?>
      </table>
    </form>

    <br /><br />
    <a href="index.php">Back to homepage</a>
    </center>
  </body>
</html>
