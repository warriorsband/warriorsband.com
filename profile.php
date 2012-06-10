<?php

/*
 *  profile.php
 *  
 *  Shows the profile of the requested user (but only the content that the requester
 *  is allowed to see).
 *  Also allows the requester to modify information where they have permission to do so
 *  (for example, a regular user modifying their own info, or an admin modifying another 
 *  user's info).
 *  Accepts the following via GET:
 *
 *    user_id: The ID of the user whose information is requested.
 *    error: A code indicating what sort of error occurred when submitting a profile change.
 *    success: A code indicating which element of the profile was updated successfully.
 */

session_start();
$redirect_url = $_SERVER['PHP_SELF'];
require('auth.php');
require_once('timeout.php');
//All the permissions and error message functions are stored here for readability
include('profile-functions.php');

//Sanitize input
function sanitize($data){
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

//Print out the profile's full name (only works once the query has been made)
function print_name() {
  if ((isset($row['first_name'])) && (isset($row['last_name']))) {
    echo $row['first_name']." ".$row['last_name'];
  }
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

//If no user ID is provided, assume the user is accessing their own profile.
//(note that since we've included auth.php, we can assume the user is logged in
//and thus that $_SESSION['user_id'] is set and valid)
$user_id = $_SESSION['user_id'];
if (isset($_GET['user_id'])) {
  $user_id = intval(sanitize($_GET['user_id']));
}

//Get all the user's details from the database; we'll need most of it anyway.
//If no row is found, print an error and exit.
if (!($row = mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `user_id`='".$user_id."'")))) {
  echo "No such user with that user_id.";
  exit();
}

$user_type = intval($row['user_type']);

//If user_type is editable, set some variables used for displaying
//radio buttons for user_type.
if (user_type_editable()) {
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
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band Profile: <?php print_name() ?></title>
    <link href="style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
      <h2>Warriors Band Profile</h2>
      <h3><?php print_name() ?></h3>
      <br />
<?php
if (isset($_GET['error'])) {
  print_errmsg($_GET['error']);
} elseif (isset($_GET['success'])) {
  print_successmsg($_GET['success']);
}
?>
      <table>
<?php if (email_viewable()) { ?>
        <tr <?php echo row_color() ?> >
          <th>E-mail</th>
<?php if (email_editable()) { ?>
          <form action="profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="email" maxlength="255" value="<?php echo $row['email']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php } else { echo "<td>".$row['email']."</td><td></td>"; } ?>
        </tr>
<?php } if (password_editable()) { ?>
        <tr <?php echo row_color() ?> >
          <th>Password</th>
          <form action="profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td style="width:250px">
              Current password: <input type="text" name="password" maxlength="64" /><br />
              New password: <input type="text" name="newpassword" maxlength="64" /><br />
              Retype password: <input type="text" name="newpassword1" maxlength="64" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
        </tr>
<?php } if (first_name_viewable()) { ?>
        <tr <?php echo row_color() ?> >
          <th>First name</th>
<?php if (first_name_editable()) { ?>
          <form action="profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="first_name" maxlength="255" value="<?php echo $row['first_name']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php } else { echo "<td>".$row['first_name']."</td><td></td>"; } ?>
        </tr>
<?php } if (last_name_viewable()) { ?>
        <tr <?php echo row_color() ?> >
          <th>Last name</th>
<?php if (last_name_editable()) { ?>
          <form action="profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="last_name" maxlength="255" value="<?php echo $row['last_name']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php } else { echo "<td>".$row['last_name']."</td><td></td>"; } ?>
        </tr>
<?php } if (user_type_viewable()) {
  if (user_type_editable()) { ?>
        <tr>
          <th>User type</th>
          <form action="profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="radio" name="user_type" value="1" <?php echo $check1; ?> /> Member
              <input type="radio" name="user_type" value="2" <?php echo $check2; ?> /> Exec
              <input type="radio" name="user_type" value="3" <?php echo $check3; ?> /> Admin
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
        </tr>
<?php } else { ?>
        <tr>
          <th>User type</th>
          <td><?php echo $user_type; ?></td>
          <td></td>
        </tr>
<?php }} ?>
      </table>

      <br /><br />
      <a href="index.php">Back to homepage</a>
    </center>
  </body>
</html>
