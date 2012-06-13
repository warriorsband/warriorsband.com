<?php

/*
 *  profile-exec.php
 *
 *  Receives a profile form and attempts to update the requested profile element.
 *  If successful, redirects to the requested URL, or to the homepage if none is provided. 
 *  If not successful, redirects back to the profile form.
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Variable to indicate whether a post has been validated and requires an update.
//If this is TRUE, it is assumed that $col_name and $value contain the column name 
//and value of a field to update, and $successcode contains a valid code to send 
//back via GET to profile.php.
$success = FALSE;

//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id'])) {
  echo "A used ID must be specified in order for settings to be changed.";
  exit();
}

$user_id = intval(sanitize($_POST['user_id']));

//Get all the user's details from the database; we'll need most of it anyway.
if (!($row = mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `user_id`='".$user_id."'")))) {
  echo "No such user with that user_id.";
  exit();
}

$user_type = intval($row['user_type']);

//If the user is attempting to modify a different user's information who has a 
//higher user type, forbid them and display an error
if (!is_same_user($user_id) && !user_type_greater_eq($user_type)) {
  echo "You do not have permission to edit this user's information.";
  exit();
}

//Update email
if (isset($_POST['email'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Sanitize and validate the field
  $email = sanitize($_POST['email']);
  if (!valid_email($email)) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=bademail");
    exit();
  }

  //Ensure the requested email address is not already in use
  if ($fetch = mysql_fetch_array( mysql_query("SELECT `email` FROM `users` WHERE `email`='$email'"))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=duplicateemail");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'email';
  $value = $email;
  $successcode = 'emailsuccess';
  $success = TRUE;
}

//Update password
elseif ((isset($_POST['password'])) && (isset($_POST['newpassword'])) && (isset($_POST['newpassword1']))) {
  //Ensure that the user is allowed to modify this setting
  ensure_same_user($user_id);

  //Sanitize and validate the fields
  $password = sanitize($_POST['password']);
  $newpassword = sanitize($_POST['newpassword']);
  $newpassword1 = sanitize($_POST['newpassword1']);

  //Validate current password
  if (!valid_password($password, $row['password'])) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=badpass");
    exit();
  }

  //Validate new password
  if ((empty($newpassword)) || (strlen($newpassword) < 6) || (strlen($newpassword) > 64)) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=passconstraints");
    exit();
  }
  if ($newpassword != $newpassword1) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=passmismatch");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'password';
  $value = hash_password($newpassword);
  $successcode = 'passwordsuccess';
  $success = TRUE;
}

//Update first name
elseif (isset($_POST['first_name'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Check if the field is empty
  if (empty($_POST['first_name'])) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $first_name = sanitize($_POST['first_name']);
  
  //Check if the field is too long
  if (strlen($first_name) > 255) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=nametoolong");
    exit();
  }

  //If name is not letters and dashes only, exit
  if (!ctype_alpha(str_replace('-','',$first_name))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=nonalphaname");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'first_name';
  $value = $first_name;
  $successcode = 'firstnamesuccess';
  $success = TRUE;
}

//Update last name
elseif (isset($_POST['last_name'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Check if the field is empty
  if (empty($_POST['last_name'])) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $last_name = sanitize($_POST['last_name']);

  //Check if the field is too long
  if (strlen($first_name) > 255) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=nametoolong");
    exit();
  }

  //If name is not letters and dashes only, exit
  if (!ctype_alpha(str_replace('-','',$last_name))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=nonalphaname");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'last_name';
  $value = $last_name;
  $successcode = 'lastnamesuccess';
  $success = TRUE;
}

//Update user type. Note that since this is posted via field, we can just
//throw an error and exit if the input is unexpected, since it likely means 
//someone is screwing around. Still have to do the sanitation/validation though.
elseif (isset($_POST['user_type'])) {
  //Only admins can modify user_type
  ensure_minimum_type(3);

  //Sanitize the field
  $new_user_type = intval(sanitize($_POST['user_type']));

  //Make sure the value is in the correct range
  if (($new_user_type < 1) || ($new_user_type > 4)) {
    echo "Invalid user_type.";
    exit();
  }

  //Prevent a user from lowering their own privileges
  //(The only exception is a drop from Admin to Admin Exec, since these have the same
  //privileges)
  if (is_same_user($user_id) && 
    (($user_type > $new_user_type) && !(($user_type == 4) && ($new_user_type == 3)))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=selfdowngrade");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'user_type';
  $value = $new_user_type;
  $successcode = 'usertypesuccess';
  $success = TRUE;
}

//Unrecognized settings field, or no field provided. Show an error.
else {
  echo "Unrecognized setting, or no setting provided.";
  exit();
}

//If successful, do the update
if ($success == TRUE) {
  //Run the update query
  mysql_query("UPDATE `users` SET `$col_name`='$value' WHERE `user_id`='$user_id'")
    or die(mysql_error());

  //Success! Redirect to the settings page with the appropriate code.
  header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=".$successcode);
  exit();
}
//Otherwise an unknown error occurred. Show an error I guess.
else {
  echo "Unknown error occurred.";
  exit();
}
?>
