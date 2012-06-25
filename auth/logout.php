<?php

/*
 *  logout.php
 *
 *  Ends a session if it exists.
 */

session_start(); 
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

$was_logged_in = FALSE;
//If the user is logged in, end the session and inform the user
if (logged_in()) {
  $_SESSION['logged_in'] = FALSE;
  session_destroy();   
  session_unset();
  $was_logged_in = TRUE;
}

if ($was_logged_in) {
  header("Location: $domain?msg=logoutsuccess");
} else {
  header("Location: $domain?msg=logoutfail");
}
exit();
