<?php

/*
 *  deleteuser-exec.php
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
  error_and_exit();
}
$user_id = intval($_POST['user_id']);

//If the confirm flag is not set, refer back to the profile page with a confirm message
if ((!isset($_POST['confirm'])) || ($_POST['confirm'] != "true")) {
  header("Location: $domain?page=profile&user_id=$user_id&msg=confirmdelete");
  exit();
}

//Get the target's user type
if (!($row = mysql_fetch_array( mysql_query("SELECT `user_type` FROM `users` WHERE `user_id`='$user_id'")))) {
  error_and_exit();
}
$user_type = intval($row['user_type']);

//Check if the requester is authorized to delete this account
if (!auth_delete_account($user_id, $user_type) {
  error_and_exit();
}

//Run the delete
mysql_query("DELETE FROM `users` WHERE `user_id`='$user_id'")
  or die(mysql_error());

//Success! Redirect to the settings page with the appropriate code.
header("Location: $domain?page=users&msg=userdeletesuccess");
exit();
?>
