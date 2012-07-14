<?php

/*
 *  timeout.php
 *
 *  Ends a session if it has been inactive for $sessiontimeout or more seconds.
 *  If not, updates the LAST_ACTIVITY time of the session.
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Session Lifetime control for inactivity
if ((isset($_SESSION['logged_in'])) && (($_SESSION['logged_in'])==TRUE)) {
  if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $sessiontimeout) {
      session_destroy();   
      session_unset();  
      header("Location: $domain?msg=sessiontimeout");
      exit();
    }
    else {
      session_regenerate_id();
      $_SESSION['LAST_ACTIVITY'] = time(); 
    }
  }
}
?>
