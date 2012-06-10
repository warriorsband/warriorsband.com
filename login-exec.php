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
require_once('config.php');

//Set default to not validated
if (!isset($_SESSION['logged_in'])) {
  $_SESSION['logged_in'] = FALSE;
}

//If the user has submitted the form and is not already logged in, attempt to validate them
if (($_SESSION['logged_in'] == FALSE) && (isset($_POST["password"])) && (isset($_POST["email"]))) {
  //Username and password has been submitted by the user
  //Receive and sanitize the submitted information
  function sanitize($data){
    $data=trim($data);
    $data=htmlspecialchars($data);
    $data=mysql_real_escape_string($data);
    return $data;
  }
  $email=sanitize($_POST["email"]);
  $pass=sanitize($_POST["password"]);

  //Validate Username
  if ($fetch = mysql_fetch_array( mysql_query("SELECT `email` FROM `users` WHERE `email`='$email'"))) {
    //Get correct hashed password from the database, and also the user_type and first name 
    //because we will save these in the session if validation is successful
    $row = mysql_fetch_array(mysql_query("SELECT `user_id`,`first_name`,`password`,`user_type` FROM `users` WHERE `email`='$email'"));
    $correctpassword = $row['password'];
    $salt = substr($correctpassword, 0, 64);
    $correcthash = substr($correctpassword, 64, 64);
    $userhash = hash("sha256", $salt . $pass);

    //If the user is registered and the password hash matches, validation is successful
    if ($userhash == $correcthash) {
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

//If the user is logged in successfully, redirect to the provided URL if it exists, or just 
//to the homepage otherwise
if ($_SESSION['logged_in']) {
  if (isset($_POST['redirect_url'])) {
    header(sprintf("Location: %s", $domain.htmlspecialchars($_POST['redirect_url'])));	
  } else {
    header(sprintf("Location: %s", $domain));	
  }
}
//Otherwise, redirect back to the login page, passing along the redirect URL if it exists
else {
  if (isset($_POST['redirect_url'])) {
    header(sprintf("Location: %s?redirect_url=%s&err_code=1", $loginpage_url, htmlspecialchars($_POST['redirect_url'])));	
  } else {
    header(sprintf("Location: %s", $loginpage_url));	
  }
}
exit();
?>
