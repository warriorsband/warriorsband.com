<?php

/*
 *  deleteevent-exec.php
 *
 *  Allows an authenticated user with sufficient permissions to delete an event.
 *  Accepts the following via POST:
 *
 *    event_id: ID of the user to delete
 *    confirm: whether to confirm the deletion
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['event_id'])) {
  error_and_exit();
}
$event_id = intval($_POST['event_id']);

//If the confirm flag is not set, refer back to the profile page with a confirm message
if ((!isset($_POST['confirm'])) || ($_POST['confirm'] != "true")) {
  header("Location: $domain?page=event&event_id=$event_id&msg=confirmdelete");
  exit();
}

//Check if the requester is authorized to delete events
if (!auth_delete_events()) {
  error_and_exit();
}

//First delete all the responses corresponding to this event
mysql_query("DELETE FROM `event_responses` WHERE `event_id`='$event_id'")
  or die(mysql_error());

//Then delete the event itself
mysql_query("DELETE FROM `events` WHERE `event_id`='$event_id'")
  or die(mysql_error());

//Success! Redirect to the settings page with the appropriate code.
header("Location: $domain?page=events&msg=eventdeletesuccess");
exit();
?>
