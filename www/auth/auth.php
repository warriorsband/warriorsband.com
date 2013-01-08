<?php

/*
 *  auth.php
 *
 *  Redirects to the login page if the user is not logged in if a redirect URL is provided
 *  in the $redirect_url variable to redirect to after login. If not, redirects to the 
 *  403 page.
 *
 */

if (!isset($_SESSION)) session_start(); 

if ((!isset($_SESSION['logged_in'])) || ($_SESSION['logged_in'] == FALSE)) {
  if (isset($redirect_page)) {
    header(sprintf("Location: $domain?page=login&redirect_page=" . htmlspecialchars($redirect_page)));
  } else {
    header(sprintf("Location: $domain?page=403"));
  }
  exit();
}
?>
