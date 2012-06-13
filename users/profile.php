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
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');

//Print out the profile's full name (only works once the query has been made)
function print_name() {
  global $row;
  if ((isset($row['first_name'])) && (isset($row['last_name']))) {
    echo $row['first_name']." ".$row['last_name'];
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
if (user_type_viewable($user_id, $user_type)) {
  $user_label = "Member";
  $check1 = "checked=\"checked\"";
  $check2 = "";
  $check3 = "";
  $check4 = "";
  if ($user_type == 2) {
    $user_label = "Exec";
    $check1 = "";
    $check2 = "checked=\"checked\"";
    $check3 = "";
    $check4 = "";
  } elseif ($user_type == 3) {
    $user_label = "Admin";
    $check1 = "";
    $check2 = "";
    $check3 = "checked=\"checked\"";
    $check4 = "";
  } elseif ($user_type == 4) {
    $user_label = "Admin";
    $check1 = "";
    $check2 = "";
    $check3 = "";
    $check4 = "checked=\"checked\"";
  }
}

//Display the profile if it is permitted to do so, otherwise show an error
if (!profile_viewable($user_id, $user_type)) {
  echo "You are allowed to view this user's profile.";
  exit();
} else {
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band Profile: <?php print_name(); ?></title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
      <h2>Warriors Band Profile</h2>
      <h3><?php print_name(); ?></h3>
      <br />
<?php print_msg(); ?>
      <table class="noborder">
<?php
if (email_viewable($user_id, $user_type)) { ?>
        <tr <?php echo row_color() ?> >
          <th>E-mail</th>
<?php
  if (email_editable($user_id, $user_type)) { ?>
          <form action="/users/profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="email" maxlength="255" value="<?php echo $row['email']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php
  } else {
    echo "<td>".$row['email']."</td><td></td>";
  } ?>
        </tr>
<?php
}
if (password_editable($user_id, $user_type)) { ?>
        <tr <?php echo row_color(); ?> >
          <th>Password</th>
          <form action="/users/profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td style="width:250px">
              Current password: <input type="password" name="password" maxlength="64" /><br />
              New password: <input type="password" name="newpassword" maxlength="64" /><br />
              Retype password: <input type="password" name="newpassword1" maxlength="64" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
        </tr>
<?php
}
if (first_name_viewable($user_id, $user_type)) { ?>
        <tr <?php echo row_color(); ?> >
          <th>First name</th>
<?php
  if (first_name_editable($user_id, $user_type)) { ?>
          <form action="/users/profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="first_name" maxlength="255" value="<?php echo $row['first_name']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php
  }
  else {
    echo "<td>".$row['first_name']."</td><td></td>";
  } ?>
        </tr>
<?php
}
if (last_name_viewable($user_id, $user_type)) { ?>
        <tr <?php echo row_color() ?> >
          <th>Last name</th>
<?php
  if (last_name_editable($user_id, $user_type)) { ?>
          <form action="/users/profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="text" name="last_name" maxlength="255" value="<?php echo $row['last_name']; ?>" />
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
<?php
  } else {
    echo "<td>".$row['last_name']."</td><td></td>";
  } ?>
        </tr>
<?php
}
if (user_type_viewable($user_id, $user_type)) { ?>
        <tr <?php echo row_color(); ?> >
          <th>User type</th>
<?php
  if (user_type_editable($user_id, $user_type)) { ?>
          <form action="/users/profile-exec.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <td>
              <input type="radio" name="user_type" value="1" <?php echo $check1; ?> /> Member
              <input type="radio" name="user_type" value="2" <?php echo $check2; ?> /> Exec
              <br />
              <input type="radio" name="user_type" value="3" <?php echo $check3; ?> /> Admin Exec
              <input type="radio" name="user_type" value="4" <?php echo $check4; ?> /> Admin
            </td>
            <td>
              <input type="submit" value="Update" />
            </td>
          </form>
        </tr>
<?php
  } else { ?>
          <td><?php echo $user_label; ?></td><td></td>
<?php
  } ?>
        </tr>
<?php
} ?>
<?php
if (account_deletable($user_id, $user_type)) { ?>
        <tr <?php echo row_color(); ?> class="noborder">
          <td colspan="3" class="noborder">
            <form action="/users/delete.php" method="POST">
              <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
<?php
  if ((isset($_GET['msg'])) && ($_GET['msg'] == "confirmdelete")) { ?>
              <input type="hidden" name="confirm" value="true" />
<?php
  } ?>
              <div align="right"><input style="width:150px" type="submit" value="Delete this account" /></div>
            </form>
          </td>
        </tr>
<?php } ?>
      </table>

      <br /><br />
      <a href="/index.php">Back to homepage</a>
    </center>
  </body>
</html>
<?php } ?>
