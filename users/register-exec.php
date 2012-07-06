<?php

/*
 *  register-exec.php
 *
 *  Allows an authenticated user with high enough user_type to create new users. 
 *  Creates the new user and sends them an email with their password.
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once("Mail.php");

//pre-define success
$success = FALSE;

//Make sure the user is logged in, has sufficient permissions, and has 
//submitted all the required forms
if (!logged_in() || !auth_register_user() ||
  !isset($_POST['email']) || !isset($_POST['first_name']) ||
  !isset($_POST['last_name']) || !isset($_POST['comment'])) {
  error_and_exit("Not logged in, insufficient permissions, or not enough information provided.");
}

//Sanitize user inputs
$email = sanitize($_POST['email']);
$first_name = format_text(sanitize($_POST['first_name']));
$last_name = format_text(sanitize($_POST['last_name']));
$comment = sanitize($_POST['comment']);

//Validate e-mail address
if (!valid_email($email)) {
  header("Location: $domain?page=register&msg=bademail");
  exit();
}

//Ensure the email address is not already taken
$num_emails = $mysqli->query(
  "SELECT COUNT(*) " .
  "FROM `users` " .
  "WHERE `email`='$email'"
  )->fetch_row();
handle_sql_error($mysqli);
if ($num_emails[0] > 0) {
  header("Location: $domain?page=register&msg=duplicateemail");
  exit();
}

//Check if the names are too long
if (strlen($first_name) > 64 || strlen($last_name) > 64) {
  header("Location: $domain?page=register&msg=nametoolong");
  exit();
}
//If names are not letters and dashes only, exit
if (!ctype_alpha(str_replace('-','',$first_name)) ||
    !ctype_alpha(str_replace('-','',$last_name))) {
  header("Location: $domain?page=register&msg=nonalphaname");
  exit();
}

//Generate a random 8-char string to be the temporary password of the new account
$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$arr = str_split($charset);
shuffle($arr);
$arr = array_slice($arr, 0, 8);
$temp_password = implode('', $arr);
$hashedpassword= hash_password($temp_password);

//Insert username and the hashed password to MySQL database
$mysqli->query(
  "INSERT INTO `users` " .
  "(`status`,`last_name`, `first_name`, `email`, `password`, `last_login_attempt`) " .
  "VALUES (2, '$last_name', '$first_name', '$email', '$hashedpassword', NOW())");
handle_sql_error($mysqli);

//Send an email to the newly registered account
$from = "Warriors Band <$email_username>";
$to = "<$email>";
$subject = registration_email_subject();
$body = registration_email_message($temp_password, $_SESSION['first_name'], $comment);

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

//redirect back to registration page indicating success/failure as appropriate
if (!PEAR::isError($mail)) {
  header("Location: $domain?page=register&msg=registrationsuccess");
} else {
  header("Location: $domain?page=register&msg=registrationfail");
}

exit();
?>
