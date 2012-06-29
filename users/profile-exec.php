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

//A user ID is required in order to edit a profile. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id'])) {
  echo "A used ID must be specified in order for a profile to be changed.";
  exit();
}

$user_id = intval($_POST['user_id']);
$redirect_url = "$domain?page=profile&user_id=$user_id";

//Get all the user's details from the database; we'll need most of it anyway.
if (!($row = mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `user_id`='".$user_id."'")))) {
  echo "No such user with that user_id.";
  exit();
}

$user_type = intval($row['user_type']);

if (!auth_edit_profile($user_id, $user_type)) {
  error_and_edit();
}

//Update email
if (isset($_POST['email']) && auth_edit_email($user_id, $user_type)) {
  //Sanitize and validate the field
  $email = sanitize($_POST['email']);
  if ($email != $row['email']) {
    if (!valid_email($email)) {
      header("Location: $redirect_url&msg=bademail");
      exit();
    }

    //Ensure the requested email address is not already in use
    if ($fetch = mysql_fetch_array( mysql_query("SELECT `email` FROM `users` WHERE `email`='$email'"))) {
      header("Location: $redirect_url&msg=duplicateemail");
      exit();
    }

    //Run the update query
    mysql_query("UPDATE `users` SET `email`='$email' WHERE `user_id`='$user_id'")
      or die(mysql_error());
  }
}

//Update password
if ((!empty($_POST['password'])) && auth_edit_password($user_id, $user_type) && 
  (!empty($_POST['newpassword'])) && (!empty($_POST['newpassword1']))) {
  //Sanitize and validate the fields
  $password = sanitize($_POST['password']);
  $newpassword = sanitize($_POST['newpassword']);
  $newpassword1 = sanitize($_POST['newpassword1']);

  //Validate current password
  if (!valid_password($password, $row['password'])) {
    header("Location: $redirect_url&msg=badpass");
    exit();
  }

  //Validate new password
  if ((empty($newpassword)) || (strlen($newpassword) < 6) || (strlen($newpassword) > 64)) {
    header("Location: $redirect_url&msg=passconstraints");
    exit();
  }
  if ($newpassword != $newpassword1) {
    header("Location: $redirect_url&msg=passmismatch");
    exit();
  }

  //Run the update query
  $hash = hash_password($newpassword);
  mysql_query("UPDATE `users` SET `password`='$hash' WHERE `user_id`='$user_id'")
    or die(mysql_error());
}

//Update first name
if (isset($_POST['first_name']) && auth_edit_first_name($user_id, $user_type)) {
  //Check if the field is empty
  if (empty($_POST['first_name'])) {
    header("Location: $redirect_url&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $first_name = sanitize($_POST['first_name']);

  if ($first_name != $row['first_name']) {
    //Check if the field is too long
    if (strlen($first_name) > 255) {
      header("Location: $redirect_url&msg=nametoolong");
      exit();
    }

    //If name is not letters and dashes only, exit
    if (!ctype_alpha(str_replace('-','',$first_name))) {
      header("Location: $redirect_url&msg=nonalphaname");
      exit();
    }

    //Run the update query
    mysql_query("UPDATE `users` SET `first_name`='$first_name' WHERE `user_id`='$user_id'")
      or die(mysql_error());
  }
}

//Update last name
if (isset($_POST['last_name']) && auth_edit_last_name($user_id, $user_type)) {
  //Check if the field is empty
  if (empty($_POST['last_name'])) {
    header("Location: $redirect_url&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $last_name = sanitize($_POST['last_name']);
  
  if ($last_name != $row['last_name']) {
    //Check if the field is too long
    if (strlen($last_name) > 255) {
      header("Location: $redirect_url&msg=nametoolong");
      exit();
    }

    //If name is not letters and dashes only, exit
    if (!ctype_alpha(str_replace('-','',$last_name))) {
      header("Location: $redirect_url&msg=nonalphaname");
      exit();
    }

    //Run the update query
    mysql_query("UPDATE `users` SET `last_name`='$last_name' WHERE `user_id`='$user_id'")
      or die(mysql_error());
  }
}

//Update user type
if (isset($_POST['user_type']) && auth_edit_user_type($user_id, $user_type)) {
  //Sanitize the field
  $new_user_type = intval($_POST['user_type']);

  if ($new_user_type != $user_type) {
    //Make sure the value is in the correct range
    if (($new_user_type < 1) || ($new_user_type > 4)) {
      echo "Invalid user type.";
      exit();
    }

    //Prevent a user from lowering their own privileges
    //(The only exception is a drop from Admin to Admin Exec, since these have the same
    //privileges)
    if (is_same_user($user_id) && 
      (($user_type > $new_user_type) && !(($user_type == 4) && ($new_user_type == 3)))) {
      header("Location: $redirect_url&msg=selfdowngrade");
      exit();
    }

    //Run the update query
    mysql_query("UPDATE `users` SET `user_type`='$new_user_type' WHERE `user_id`='$user_id'")
      or die(mysql_error());
  }
}

//Success! Redirect to the settings page with the appropriate code.
header("Location: $redirect_url&msg=profileupdatesuccess");
exit();
?>
