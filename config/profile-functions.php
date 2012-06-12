<?php

/*
 *  profile-functions.php
 *
 *  Functions used in profile.php
 */

//Functions which determine whether the requesting user can view
//or can view/edit a given profile element.
//These must all be called after the row query has succeeded and 
//$user_id and $user_type have been defined.
function profile_viewable() {
  return TRUE;
}
function profile_editable() {
  global $user_id;
  if (($_SESSION['user_id'] == $user_id) || ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function email_viewable() {
  global $user_id;
  if (($_SESSION['user_id'] == $user_id) || ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function email_editable() {
  if ($_SESSION['user_type'] >= 2) {
    return TRUE;
  } else { return FALSE; }
}
function password_editable() {
  global $user_id;
  if ($_SESSION['user_id'] == $user_id) {
    return TRUE;
  } else { return FALSE; }
}
function first_name_viewable() {
  return TRUE;
}
function first_name_editable() {
  if ($_SESSION['user_type'] >= 2) {
    return TRUE;
  } else { return FALSE; }
}
function last_name_viewable() {
  return TRUE;
}
function last_name_editable() {
  if ($_SESSION['user_type'] >= 2) {
    return TRUE;
  } else { return FALSE; }
}
function user_type_viewable() {
  if ($_SESSION['user_type'] >= 2) {
    return TRUE;
  } else { return FALSE; }
}
function user_type_editable() {
  if ($_SESSION['user_type'] >= 3) {
    return TRUE;
  } else { return FALSE; }
}

//Takes in an error code and outputs the relevant message
function print_errmsg($error) {
  if ($error == "bademail") {
    echo "Invalid e-mail address.";
  } elseif ($error == "duplicateemail") {
    echo "The provided e-mail address is already in use.";
  } elseif ($error == "badpass") {
    echo "Invalid current password.";
  } elseif ($error == "passmismatch") {
    echo "Retyped new password did not match.";
  } elseif ($error == "passconstraints") {
    echo "Password must be between 6 and 64 characters long";
  } elseif ($error == "emptyname") {
    echo "First and last name cannot be empty.";
  } elseif ($error == "nametoolong") {
    echo "First and last name must be less than 255 characters.";
  } elseif ($error == "nonalphaname") {
    echo "First and last name must be letters and dashes only.";
  } elseif ($error == "admindowngrade") {
    echo "Cannot downgrade the user type of an admin account.<br />";
    echo "If this was really your plan, delete the account and re-make it.";
  } else {
    echo "Unknown error code.";
  }
  echo "<br /><br />";
}

//Takes in a success code and prints the relevant message
function print_successmsg($success) {
  if ($success == "email") {
    echo "E-mail address updated successfully.";
  } elseif ($success == "password") {
    echo "Password updated successfully.";
  } elseif ($success == "firstname") {
    echo "First name updated successfully.";
  } elseif ($success == "lastname") {
    echo "Last name updated successfully.";
  } elseif ($success == "usertype") {
    echo "User type updated successfully.";
  } else {
    echo "Unknown success code.";
  }
  echo "<br /><br />";
}
