<?php

/*
 *  eventresponse-exec.php
 *
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Ensure that the user is logged in
if (!logged_in()) {
  echo "Not logged in.";
  //error_and_exit();
  exit();
}

//Ensure that the required fields were provided
if (!isset($_POST['event_id']) || !isset($_POST['response'])) {
  echo "No event ID provided or no response provided.";
  //error_and_exit();
  exit();
}

//Sanitize and validate inputs
$event_id = intval($_POST['event_id']);
$response = intval($_POST['response']);
if ($response < 1 || $response > 3) {
  echo "bad response value sent.";
  //error_and_exit();
  exit();
}
if (isset($_POST['comment'])) {
  $comment = sanitize($_POST['comment']);
} else {
  $comment = "";
}
$valid_comment = FALSE;
if (strlen($comment) >= 10) {
  $valid_comment = TRUE;
}

//Ensure that if "maybe" was selected, a comment was provided
if ($response == 3 && $valid_comment == FALSE) {
  header("Location: $domain?page=event&event_id=$event_id&msg=commentrequired");
  exit();
}

//Check if the user has already responded to this event
$row = mysql_fetch_array( mysql_query(
  "SELECT COUNT(*) FROM `event_responses` WHERE `event_id`='$event_id' AND `user_id`='" .
  $_SESSION['user_id'] . "'"));
if ($row[0] == 0) {
  //Do an insert and update the reminder counter
  mysql_query("INSERT INTO `event_responses` (`user_id`,`event_id`,`response`,`comment`) " .
    "VALUES ('" . $_SESSION['user_id'] . "','$event_id','$response','$comment')")
    or die(mysql_error());
  $_SESSION['responses'] -= 1;
} else {
  //Do the update
  mysql_query("UPDATE `event_responses` SET `response`='$response',`comment`='$comment' " .
    "WHERE `event_id`='$event_id' AND `user_id`='" . $_SESSION['user_id'] . "'")
    or die(mysql_error());
}
header("Location: $domain?page=event&event_id=$event_id&msg=responserecorded");
exit();
