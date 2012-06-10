<?php
require_once('config.php');

if ((isset($_SESSION['logged_in'])) && (($_SESSION['logged_in'])==TRUE)) {
  //valid user has logged-in to the website

  //Session Lifetime control for inactivity
  if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $sessiontimeout) {
      session_destroy();   
      session_unset();  
    }
    else {
      $_SESSION['LAST_ACTIVITY'] = time(); 
    }
  }
}
?>
