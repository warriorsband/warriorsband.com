<?php

/*
 *  deleteuser-exec.php
 *
 *  Allows an authenticated user with sufficient permissions to delete a user.
 *  Accepts the following via POST:
 *
 *    confirm: "true" if the deletion has been confirmed and should be done
 *    user_id: ID of the user to delete
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id']) || $_POST['user_id'] < 0) {
  error_and_exit("No user ID provided, or invalid user ID");
}
$user_id = intval($_POST['user_id']);

//If the confirm flag is not set, refer back to the profile page with a confirm message
if (!isset($_POST['confirm']) || $_POST['confirm'] != "true") {
  header("Location: $domain?page=profile&user_id=$user_id&msg=confirmdelete");
  exit();
}

//Get the target's user type
$user_type_row = $mysqli->query(
  "SELECT `user_type` " .
  "FROM `users` " .
  "WHERE `user_id`='$user_id'"
  )->fetch_assoc();
handle_sql_error($mysqli);
$user_type = intval($user_type_row['user_type']);

//Check if the requester is authorized to delete this account
if (!auth_delete_account($user_id, $user_type)) {
  error_and_exit("Not authorised to delete this account");
}

//First delete all the event responses corresponding to this user.
//Then run the delete
$mysqli->multi_query(
  "DELETE FROM `event_responses` " .
  "WHERE `user_id`='$user_id'; " .
  "DELETE FROM `users` " .
  "WHERE `user_id`='$user_id';");
handle_sql_error($mysqli);

//Success! Redirect to the settings page with the appropriate code.
header("Location: $domain?page=users&msg=userdeletesuccess");
exit();
?>
