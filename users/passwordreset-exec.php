<?php

/*
 *  passwordreset-exec.php
 *
 *  Allows an authenticated user with sufficient permissions to reset a 
 *  user's password.
 *  Accepts the following via POST:
 *
 *    confirm: "true" if the reset has been confirmed and should be done
 *    user_id: ID of the user to reset
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
set_include_path(get_include_path().'/Sites/warriorsband.com/pear'.PATH_SEPARATOR);
require_once("Mail.php");


//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id']) || $_POST['user_id'] < 0) {
  error_and_exit("No user ID provided, or invalid user ID");
}
$user_id = intval($_POST['user_id']);

//If the confirm flag is not set, refer back to the profile page with a confirm message
if (!isset($_POST['confirm']) || $_POST['confirm'] != "true") {
  header("Location: $domain?page=profile&user_id=$user_id&msg=confirmpasswordreset");
  exit();
}

//Get the target's user type and e-mail address
$row = $mysqli->query(
  "SELECT `user_type`,`email` " .
  "FROM `users` " .
  "WHERE `user_id`='$user_id'"
  )->fetch_assoc();
handle_sql_error($mysqli);
$user_type = intval($row['user_type']);
$email = $row['email'];

//Check if the requester is authorized to reset this password
if (!auth_password_reset($user_id, $user_type)) {
  error_and_exit("Not authorised to reset this account's password");
}

//Generate a random 8-char string to be the new temporary password
$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$arr = str_split($charset);
shuffle($arr);
$arr = array_slice($arr, 0, 8);
$temp_password = implode('', $arr);
$hashedpassword= hash_password($temp_password);

//Update user's password in the database, and set their status to 3,
//indicating that a password change is required
$mysqli->query(
  "UPDATE `users` " .
  "SET `password`='$hashedpassword', `status`=3 " .
  "WHERE `user_id`='$user_id'");
handle_sql_error($mysqli);

//Send an email to the newly reset account
$from = "Warriors Band <$email_username>";
$to = "<$email>";
$subject = passwordreset_email_subject();
$body = passwordreset_email_message($temp_password, $_SESSION['first_name']);

$headers = array ('From' => $from, 
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $email_host,
  'port' => $email_port,
  'auth' => true,
  'username' => $email_username,
  'password' => $email_password));

$mail = $smtp->send($to, $headers, $body);

//Success! Redirect to the profile with the appropriate code.
header("Location: $domain?page=profile&user_id=$user_id&msg=passwordresetsuccess");
exit();
?>
