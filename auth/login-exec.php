<?php

/*
 *  login-exec.php
 *
 *  Receives a login form and attempts login validation.
 *  If successful, redirects to the requested URL, or to the homepage if none is provided. 
 *  If not successful, redirects back to the login form.
 */

session_start();

//require user configuration and database connection parameters
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

//Set default to not validated
if (!isset($_SESSION['logged_in'])) {
  $_SESSION['logged_in'] = FALSE;
}

//If the user has submitted the form and is not already logged in, attempt to validate them
if (($_SESSION['logged_in'] == FALSE) && (isset($_POST["password"])) && (isset($_POST["email"]))) {
  //Username and password has been submitted by the user
  //Receive and sanitize the submitted information
  $email=sanitize($_POST["email"]);
  $pass=sanitize($_POST["password"]);

  //Validate email and password
  if ($row = mysql_fetch_array( mysql_query("SELECT `email`,`user_id`,`first_name`,`password`,`last_login_attempt`,`login_attempts`,`user_type` FROM `users` WHERE `email`='$email'"))) {
    //Check if the user has attempted too many logins too recently
    $last_login_attempt = strtotime($row['last_login_attempt']);
    if (($row['login_attempts'] >= $max_login_attempts) && ((time() - $last_login_attempt) < $login_cooldown)) {
      $error='maxlogins';
    } else {
      //Update last login attempt time, and number of login attempts
      mysql_query("UPDATE `users` SET `last_login_attempt`=NOW(), `login_attempts`=`login_attempts`+1 WHERE `email`='$email'")
        or die(mysql_error());

      //If the user is registered and the password hash matches, validation is successful
      if (!valid_password($pass, $row['password'])) {
        $error='bademailpass';
      } else {
        //Update last login time and login attempts
        mysql_query("UPDATE `users` SET `last_login`=NOW(), `login_attempts`=0 WHERE `email`='$email'")
          or die(mysql_error());

        //Regenerate session id prior to setting any session variable
        //to mitigate session fixation attacks
        session_regenerate_id();

        //Set various session variables associated with a successful login
        $_SESSION['logged_in'] = TRUE;
        $_SESSION['LAST_ACTIVITY'] = time();
        $_SESSION['user_id'] = intval($row['user_id']);
        $_SESSION['user_type'] = intval($row['user_type']);
        $_SESSION['first_name'] = $row['first_name'];
      }
    }
  }
} 

//If the user is logged in successfully, redirect to the provided URL if it exists, or just 
//to the homepage otherwise
if ($_SESSION['logged_in']) {
  if (isset($_POST['redirect_url'])) {
    $redirect_url = $domain.htmlspecialchars($_POST['redirect_url']);
  } else {
    $redirect_url = $domain;
  }
}
//Otherwise, update login attempt information, and
//redirect back to the login page, passing along the redirect URL if it exists.
else {
  if (isset($_POST['redirect_url'])) {
    $redirect_url = $loginpage_url."?redirect_url=".htmlspecialchars($_POST['redirect_url']);
    if (isset($error)) {
      $redirect_url = $redirect_url."&msg=$error";
    }
  } else {
    $redirect_url = $loginpage_url;
    if (isset($error)) {
      $redirect_url = $redirect_url."?msg=$error";
    }
  }
}
header("Location: $redirect_url");
exit();
?>
