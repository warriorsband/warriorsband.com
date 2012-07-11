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
 */

$redirect_page = "profile";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');

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
if (isset($_GET['user_id'])) {
  $user_id = intval($_GET['user_id']);
} else {
  $user_id = $_SESSION['user_id'];
}

//Get all the user's details from the database; we'll need most of it anyway.
//If no row is found, print an error and exit.
$user_row = $mysqli->query(
  "SELECT * " .
  "FROM `users` " .
  "WHERE `user_id`='$user_id'"
  )->fetch_assoc();
handle_sql_error($mysqli);
if (!$user_row) {
  print_and_exit("No such user with that user ID.");
}

$user_type = intval($user_row['user_type']);

//Display the profile if it is permitted to do so, otherwise show an error
if (!auth_view_profile($user_id, $user_type)) {
  print_and_exit("You are not allowed to view this user's profile.");
}
?>

<h1>Member Profile</h1>
<h2><?php print_name(); ?></h2>
<table>
<?php
if (auth_edit_profile($user_id, $user_type)) {
?>
  <form action="/users/profile-exec.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
<?php
}

if (auth_view_email($user_id, $user_type)) {
?>
    <tr <?php echo row_color() ?> >
      <th>E-mail</th>
<?php
  if (auth_edit_email($user_id, $user_type)) {
?>
      <td>
        <input type="text" name="email" maxlength="255" value="<?php echo $user_row['email']; ?>" />
      </td>
<?php
  } else {
?>
      <td><?php echo $user_row['email']; ?></td>
<?php
  }
?>
    </tr>
<?php
}

if (auth_view_name($user_id, $user_type)) {
?>
    <tr <?php echo row_color(); ?> >
<?php
  if (auth_edit_name($user_id, $user_type)) {
?>
      <th>First name</th>
      <td>
        <input type="text" name="first_name" maxlength="64" value="<?php echo $user_row['first_name']; ?>" />
      </td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Last name</th>
      <td>
        <input type="text" name="last_name" maxlength="64" value="<?php echo $user_row['last_name']; ?>" />
      </td>
<?php
  } else {
?>
      <th>Name</th>
      <td><?php echo $user_row['first_name'] . " " . $user_row['last_name']; ?></td>
<?php
  }
?>
    </tr>
<?php
}

if (auth_edit_password($user_id, $user_type)) {
?>
    <tr <?php echo row_color(); ?> >
      <th>Password<br /><span class="tiph">(Minimum 6 characters)</span></th>
      <td style="width:250px">
        Current password: <input style="width: 100%" type="password" name="password" maxlength="64" /><br />
        New password: <input style="width: 100%" type="password" name="newpassword" maxlength="64" /><br />
        Retype password: <input style="width: 100%" type="password" name="newpassword1" maxlength="64" />
      </td>
    </tr>
<?php
}

if (auth_view_misc_info($user_id, $user_type)) {
  if (auth_edit_misc_info($user_id, $user_type)) {
?>
    <tr <?php echo row_color(); ?> >
      <th>Program</th>
      <td>
        <input type="text" name="program" maxlength="64" value="<?php echo $user_row['program']; ?>" />
      </td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Term</th>
      <td>
        <select name="term">
<?php
    for ($i = 0; $i <= $maxsize_term; $i++) {
      echo "<option value=\"$i\" ";
      selected($i,$user_row['term']);
      echo ">" . term_to_str($i) . "</option>";
    }
?>
        </select>
      </td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Instrument</th>
      <td>
        <select name="instrument">
<?php
    for ($i = 0; $i <= $maxsize_instrument; $i++) {
      echo "<option value=\"$i\" ";
      selected($i,$user_row['instrument']);
      echo ">" . instrument_to_str($i) . "</option>";
    }
?>
        </select>
      </td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Fun Fact</th>
      <td><textarea name="fun_fact" rows="4" cols="60" maxlength="255"><?php echo $user_row['fun_fact']; ?></textarea></td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>On Campus?</th>
      <td>
        <input type="radio" name="on_campus" value="1" <?php checked(1,$user_row['on_campus']); ?> /> Yes
        <input type="radio" name="on_campus" value="0" <?php checked(0,$user_row['on_campus']); ?> /> No
        <br />
        <span class="tip">
          (Pick "yes" if you'll be showing up to some practices/events this term. Otherwise, pick "no" and 
          you won't receive notication e-mails for events, surveys, etc)
        </span>
      </td>
    </tr>
<?php
  } else {
?>
    <tr <?php echo row_color(); ?> >
      <th>Program</th>
      <td><?php echo $user_row['program']; ?></td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Term</th>
      <td><?php echo term_to_str($user_row['term']); ?></td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Instrument</th>
      <td><?php echo instrument_to_str($user_row['instrument']); ?></td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>Fun Fact</th>
      <td><?php echo $user_row['fun_fact']; ?></td>
    </tr>
    <tr <?php echo row_color(); ?> >
      <th>On Campus?</th>
      <td><?php echo on_campus_to_str($user_row['on_campus']); ?></td>
    </tr>
<?php
  }
}

if (auth_view_user_type($user_id, $user_type)) {
?>
    <tr <?php echo row_color(); ?> >
      <th>User type</th>
<?php
  if (auth_edit_user_type($user_id, $user_type)) {
?>
      <td>
        <input type="radio" name="user_type" value="1" <?php checked(1,$user_type); ?> /> Member
        <input type="radio" name="user_type" value="2" <?php checked(2,$user_type); ?> /> Exec
        <br />
        <input type="radio" name="user_type" value="3" <?php checked(3,$user_type); ?> /> Admin Exec
        <input type="radio" name="user_type" value="4" <?php checked(4,$user_type); ?> /> Admin
      </td>
<?php
  } else {
?>
      <td><?php echo user_type_to_str($user_type); ?></td>
<?php
  }
?>
    </tr>
<?php
}

if (auth_edit_profile($user_id, $user_type)) {
?>
    <tr>
      <td style="text-align:center" colspan="2">
        <input type="submit" value="Update Profile" />
      </td>
    <tr>
  </form>
<?php
}
?>
</table>

<?php
if (auth_delete_account($user_id, $user_type)) {
?>
<br /><br />
<div class="center">
  <form action="/users/deleteuser-exec.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
<?php
  if ((isset($_GET['msg'])) && ($_GET['msg'] == "confirmdelete")) {
?>
    <input type="hidden" name="confirm" value="true" />
<?php
  }
?>
    <input style="width:150px" type="submit" value="Delete this account" />
  </form>
</div>
<?php
}
?>
