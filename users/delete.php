<?php

/*
 *  delete.php
 *
 *  Allows an authenticated user with sufficient permissions to delete a user.
 *  Accepts the following via GET:
 *
 *    user_id: ID of the user to delete
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id'])) {
  echo "A used ID must be specified in order for settings to be changed.";
  exit();
}

$user_id = intval(sanitize($_POST['user_id']));

//If the confirm flag is not set, refer back to the profile page with a confirm message
if ((!isset($_POST['confirm'])) || ($_POST['confirm'] != "true")) {
  header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&msg=confirmdelete");
  exit();
}

//Ensure that the requester is allowed to delete accounts
if ($_SESSION['user_type'] < 2) {
  echo "Your user type does not allow you to delete accounts.";
  exit();
}

//If the user ID to delete matches the current user, display an error
if ($_SESSION['user_id'] == $user_id) {
  echo "You cannot delete your own account.";
  exit();
}

//Exit if the target user does not exist
if (!($row = mysql_fetch_array( mysql_query("SELECT `user_type` FROM `users` WHERE `user_id`='".$user_id."'")))) {
  echo "No such user with that user_id.";
  exit();
}

//Get the user_type of the the delete target, and stop if the requester has lower user type
$user_type = intval($row['user_type']);
if ($_SESSION['user_type'] < $user_type) {
  echo "Cannot delete a user with higher user type than yourself.";
  exit();
}

//Run the delete
mysql_query("DELETE FROM `users` WHERE `user_id`='$user_id'")
  or die(mysql_error());

//Success! Redirect to the settings page with the appropriate code.
header("Location: $domain/users/users.php?msg=deletesuccess");
exit();
?>
