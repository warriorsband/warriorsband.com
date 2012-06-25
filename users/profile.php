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

$redirect_page = "profile";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');

row_color(TRUE);

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
  $user_id = intval($_GET['user_id']);
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
if (auth_edit_user_type($user_id, $user_type)) {
  $user_label = user_type_to_str($user_type);
}

//Display the profile if it is permitted to do so, otherwise show an error
if (!auth_view_profile($user_id, $user_type)) {
  echo "You are allowed to view this user's profile.";
  exit();
} else {
?>

<h1>Member Profile</h1>
<h2><?php print_name(); ?></h2>
<table>
  <form action="/users/profile-exec.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
<?php
if (auth_view_email($user_id, $user_type)) { ?>
    <tr <?php echo row_color() ?> >
      <th>E-mail</th>
<?php echo "<td>".$row['email']."</td>"; ?>
    </tr>
<?php
}
if (auth_edit_password($user_id, $user_type)) { ?>
    <tr <?php echo row_color(); ?> >
      <th>Password</th>
      <td style="width:250px">
        Current password: <input type="password" name="password" maxlength="64" /><br />
        New password: <input type="password" name="newpassword" maxlength="64" /><br />
        Retype password: <input type="password" name="newpassword1" maxlength="64" />
      </td>
    </tr>
<?php
}
if (auth_view_first_name($user_id, $user_type)) { ?>
    <tr <?php echo row_color(); ?> >
      <th>First name</th>
<?php
if (auth_edit_first_name($user_id, $user_type)) { ?>
      <td>
        <input type="text" name="first_name" maxlength="255" value="<?php echo $row['first_name']; ?>" />
      </td>
<?php
  }
  else {
    echo "<td>".$row['first_name']."</td>";
  } ?>
    </tr>
<?php
}
if (auth_view_last_name($user_id, $user_type)) { ?>
    <tr <?php echo row_color() ?> >
      <th>Last name</th>
<?php
  if (auth_edit_last_name($user_id, $user_type)) { ?>
      <td>
        <input type="text" name="last_name" maxlength="255" value="<?php echo $row['last_name']; ?>" />
      </td>
<?php
  } else {
    echo "<td>".$row['last_name']."</td>";
  } ?>
    </tr>
<?php
}
if (auth_view_user_type($user_id, $user_type)) { ?>
    <tr <?php echo row_color(); ?> >
      <th>User type</th>
<?php
  if (auth_edit_user_type($user_id, $user_type)) { ?>
      <td>
        <input type="radio" name="user_type" value="1" <?php checked(1,$user_type); ?> /> Member
        <input type="radio" name="user_type" value="2" <?php checked(2,$user_type); ?> /> Exec
        <br />
        <input type="radio" name="user_type" value="3" <?php checked(3,$user_type); ?> /> Admin Exec
        <input type="radio" name="user_type" value="4" <?php checked(4,$user_type); ?> /> Admin
      </td>
    </tr>
<?php
  } else { ?>
    <?php echo "<td>" . user_type_to_str($user_type) . "</td>";
  } ?>
    </tr>
    <tr>
      <td style="text-align:center" colspan="2">
        <input type="submit" value="Update Profile" />
      </td>
    <tr>
  </form>
<?php
}
if (auth_delete_account($user_id, $user_type)) { ?>
  <tr <?php echo row_color(); ?>>
    <td colspan="3">
      <form action="/users/deleteuser-exec.php" method="POST">
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
<?php } ?>
