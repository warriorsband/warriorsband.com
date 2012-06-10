<?php

/*
 *  auth.php
 *
 *  Redirects to the login page if the user is not logged in if a redirect URL is provided
 *  in the $redirect_url variable to redirect to after login. If not, redirects to the 
 *  403 page.
 *
 *  TO USE THIS TO REQUIRE AUTHENTICATION ON A PAGE:
 *  Include the following at the beginning of the document:
 *
 *      <?php
 *      session_start();
 *      $redirect_url = $_SERVER['PHP_SELF'];
 *      require('auth.php');
 *      ?>
 *
 *  This opens the session and sets the page itself as the page which the login should 
 *  redirect to.
 */

require('config.php');

if ((!isset($_SESSION['logged_in'])) || ($_SESSION['logged_in'] == FALSE)) {
  if (isset($redirect_url)) {
    header(sprintf("Location: %s?redirect_url=%s", $loginpage_url, htmlspecialchars($redirect_url)));
  } else {
    header(sprintf("Location: %s", $forbidden_url));
  }
  exit();
}
?>
