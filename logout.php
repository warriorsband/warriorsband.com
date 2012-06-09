<?php
session_start();
require('config.php');

if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in'] == TRUE)) {
  //valid logout request
  $_SESSION['logged_in'] = False;
  session_destroy();   
  session_unset();     

  header(sprintf("Location: %s", $domain));	
}
?>
