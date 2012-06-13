<?php

/*
 *  logout.php
 *
 *  Ends a session if it exists.
 */


session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

$was_logged_in = FALSE;
//If the user is logged in, end the session and inform the user
if (logged_in()) {
  $_SESSION['logged_in'] = FALSE;
  session_destroy();   
  session_unset();
  $was_logged_in = TRUE;
}

require($_SERVER['DOCUMENT_ROOT'].'/header.php');

echo '<div class="center">';
if ($was_logged_in) {
  echo "Logged out successfully.";
} else {
  echo "You must be logged in before you can log out.";
}
echo '</div>';

require($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?>
