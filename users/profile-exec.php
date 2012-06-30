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
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');

//A user ID is required in order to edit a profile. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id'])) {
  error_and_exit("A used ID must be specified in order for a profile to be changed.");
}
$user_id = intval($_POST['user_id']);
$redirect_url = "$domain?page=profile&user_id=$user_id";

//Get all the user's details from the database; we'll need most of it anyway.
$user_row = $mysqli->query(
  "SELECT * " .
  "FROM `users` " .
  "WHERE `user_id`='$user_id'"
  )->fetch_assoc();
handle_sql_error($mysqli);
if (!$user_row) {
  error_and_exit("No such user in the database.");
}
$user_type = intval($user_row['user_type']);

if (!auth_edit_profile($user_id, $user_type)) {
  error_and_edit("Not authrorized to edit this profile.");
}

//The list of column names and values used in the SQL
//update (we build this up piece by piece so that there is flexibility in 
//form submission, i.e. a form updating only email address does not need to 
//post every field)
$updates = '';

//Update email
if (isset($_POST['email']) && auth_edit_email($user_id, $user_type)) {
  //Sanitize and validate the field
  $email = sanitize($_POST['email']);

  //Update if the email has changed
  if ($email != $user_row['email']) {
    if (!valid_email($email)) {
      header("Location: $redirect_url&msg=bademail");
      exit();
    }

    //Ensure the requested email address is not already in use
    $num_emails = $mysqli->query(
      "SELECT COUNT(*) " .
      "FROM `users` " .
      "WHERE `email`='$email'"
      )->fetch_row();
    handle_sql_error($mysqli);
    if ($num_emails[0] > 0) {
      header("Location: $redirect_url&msg=duplicateemail");
      exit();
    }

    $updates .= "`email`='$email',";
  }
}

//Update password
if (!empty($_POST['password']) && auth_edit_password($user_id, $user_type) && 
  !empty($_POST['newpassword']) && !empty($_POST['newpassword1'])) {
  //Sanitize and validate the fields
  $password = sanitize($_POST['password']);
  $newpassword = sanitize($_POST['newpassword']);
  $newpassword1 = sanitize($_POST['newpassword1']);

  //Validate current password
  if (!valid_password($password, $user_row['password'])) {
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

  $hash = hash_password($newpassword);
  $updates .= "`password`='$hash',";
}

//Update first name
if (isset($_POST['first_name']) && auth_edit_name($user_id, $user_type)) {
  //Check if the field is empty
  if (empty($_POST['first_name'])) {
    header("Location: $redirect_url&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $first_name = format_text(sanitize($_POST['first_name']));

  //Update only if the name has changed
  if ($first_name != $user_row['first_name']) {
    //Check if the field is too long
    if (strlen($first_name) > 64) {
      error_and_exit("Name value too long.");
    }

    //If name is not letters and dashes only, exit
    if (!ctype_alpha(str_replace('-','',$first_name))) {
      header("Location: $redirect_url&msg=nonalphaname");
      exit();
    }

    $updates .= "`first_name`='$first_name',";
  }
}

//Update last name
if (isset($_POST['last_name']) && auth_edit_name($user_id, $user_type)) {
  //Check if the field is empty
  if (empty($_POST['last_name'])) {
    header("Location: $redirect_url&msg=emptyname");
    exit();
  }

  //Sanitize the field
  $last_name = format_text(sanitize($_POST['last_name']));

  //Update only if the name has changed
  if ($last_name != $user_row['last_name']) {
    //Check if the field is too long
    if (strlen($last_name) > 64) {
      error_and_exit("Name value too long.");
    }

    //If name is not letters and dashes only, exit
    if (!ctype_alpha(str_replace('-','',$last_name))) {
      header("Location: $redirect_url&msg=nonalphaname");
      exit();
    }

    $updates .= "`last_name`='$last_name',";
  }
}

//Update user type
if (isset($_POST['user_type']) && auth_edit_user_type($user_id, $user_type)) {
  //Sanitize the field
  $new_user_type = intval($_POST['user_type']);

  //Only update if the user type has changed
  if ($new_user_type != $user_type) {
    //Make sure the value is in the correct range
    if ($new_user_type < 1 || $new_user_type > 4) {
      error_and_exit("Invalid user type");
    }

    //Prevent a user from lowering their own privileges
    //(The only exception is a drop from Admin to Admin Exec, since these have the same
    //privileges)
    if (is_same_user($user_id) && 
      ($user_type > $new_user_type && !($user_type == 4 && $new_user_type == 3))) {
      header("Location: $redirect_url&msg=selfdowngrade");
      exit();
    }

    $updates .= "`user_type`='$new_user_type',";
  }
}

//Update program
if (isset($_POST['program']) && auth_edit_misc_info($user_id, $user_type)) {
  //Sanitize the field
  $program = format_text(sanitize($_POST['program']));

  //Update only if the program has changed
  if ($program != $user_row['program']) {
    //Check if the field is too long
    if (strlen($program) > 64) {
      error_and_exit("Program value too long");
    }

    $updates .= "`program`='$program',";
  }
}

//Update term
if (isset($_POST['term']) && auth_edit_misc_info($user_id, $user_type)) {
  //Sanitize the field
  $term = intval($_POST['term']);

  //Only update if the term has changed
  if ($term != $user_row['term']) {
    //Make sure the value is in the correct range
    if ($term < 0 || $term > $maxsize_term) {
      error_and_exit("Invalid term value");
    }

    $updates .= "`term`='$term',";
  }
}

//Update instrument
if (isset($_POST['instrument']) && auth_edit_misc_info($user_id, $user_type)) {
  //Sanitize the field
  $instrument = intval($_POST['instrument']);

  //Only update if the instrument has changed
  if ($instrument != $user_row['instrument']) {
    //Make sure the value is in the correct range
    if ($instrument < 0 || $instrument > $maxsize_instrument) {
      error_and_exit("Invalid instrument value");
    }

    $updates .= "`instrument`='$instrument',";
  }
}

//Update fun fact
if (isset($_POST['fun_fact']) && auth_edit_misc_info($user_id, $user_type)) {
  //Sanitize the field
  $fun_fact = format_text(sanitize($_POST['fun_fact']));

  //Update only if the fun_fact has changed
  if ($fun_fact != $user_row['fun_fact']) {
    //Check if the field is too long
    if (strlen($fun_fact) > 255) {
      error_and_exit("Fun fact value too long");
    }

    $updates .= "`fun_fact`='$fun_fact',";
  }
}

//Run the update query if necessary
if (!empty($updates)) {
  //Remove the trailing comma from the updates
  $updates = substr($updates,0,-1);

  //Do the update
  $mysqli->query(
    "UPDATE `users` " .
    "SET $updates " .
    "WHERE `user_id`='$user_id'");
  handle_sql_error($mysqli);

  //Success! Redirect to the settings page with the appropriate code.
  header("Location: $redirect_url&msg=profileupdatesuccess");
  exit();
} else {
  error_and_exit("No profile field requested for update.");
}
?>
