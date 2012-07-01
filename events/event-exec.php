<?php

/*
 *  event-exec.php
 *
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Ensure that the user is allowed to edit events
if (!auth_edit_events()) error_and_exit();

$redirect_url = "$domain?page=event";

//TRUE if we are creating a new event, FALSE if we are updating an existing one
$new_event = TRUE;

//If an event ID is provided, sanitize it and fetch the existing event info
if (isset($_POST['event_id'])) {
  $event_id = intval($_POST['event_id']);
  $new_event = FALSE;
  $redirect_url .= "&event_id=$event_id";

  //Make sure the event exists
  $num_events = $mysqli->query(
    "SELECT COUNT(*) FROM `events` WHERE `event_id`='$event_id'")->fetch_row();
  handle_sql_error($mysqli);
  if ($num_events[0] == 0) {
    error_and_exit("No event with that event ID.");
  }
}

//Validate status
$status = intval($_POST['status']);
if (($status < 1) || ($status > 9)) {
  error_and_exit("Invalid status");
}

//Validate title
$title = format_text(sanitize($_POST['title']));
if (empty($title) || strlen($title) == 0 || strlen($title) > 255) {
    header("Location: $redirect_url&msg=badtitle");
    exit();
}

//Construct and validate date
if (isset($_POST['no_date'])) {
  $date = "NULL";
} else {
  $date_day = intval($_POST['date_day']);
  $date_month = intval($_POST['date_month']);
  $date_year = intval($_POST['date_year']);
  $date = "'$date_year-$date_month-$date_day'";
  if (!checkdate($date_month, $date_day, $date_year)) {
    header("Location: $redirect_url&msg=baddate");
    exit();
  }
  if (mktime(0,0,0,$date_month,$date_day,$date_year) < time()) {
    header("Location: $redirect_url&msg=pastdate");
    exit();
  }
}

//Construct and validate time
if (isset($_POST['no_time'])) {
  $time = "NULL";
} else {
  $time_hour = intval($_POST['time_hour']);
  $time_minute = intval($_POST['time_minute']);
  $time_ampm = sanitize($_POST['time_ampm']);
  if (($time_hour < 1) || ($time_hour > 12) ||
      ($time_minute < 0) || ($time_minute > 59) ||
      (($time_ampm != "AM") && ($time_ampm != "PM"))) {
    header("Location: $redirect_url&msg=badtime");
    exit();
  }
  $time = "'" . date("H:i", strtotime("$time_hour:$time_minute $time_ampm")) . "'";
}

//Validate location
$location = format_text(sanitize($_POST['location']));
if (strlen($location) > 255) {
    header("Location: $redirect_url&msg=badlocation");
    exit();
}

//Validate description
$description = sanitize($_POST['description']);
if (strlen($location) > 10000) {
  error_and_exit("Description must be less than 10000 characters.");
}

//If this is a new event, do an insertion and update the reminder counter, otherwise do an update
if ($new_event) {
  $mysqli->query(
    "INSERT INTO `events` " .
    "(`status`,`creator_id`,`title`,`date`,`start_time`,`location`,`description`)" . 
    "VALUES ('$status','" . $_SESSION['user_id'] . "','$title',$date,$time,'$location','$description')");
  handle_sql_error($mysqli);
  //Regenerate session id prior to setting any session variable
  //to mitigate session fixation attacks
  session_regenerate_id();
  $_SESSION['responses'] -= 1;
  header("Location: $redirect_url&msg=eventcreatesuccess");
} else {
  $mysqli->query(
    "UPDATE `events` SET `status`='$status',`creator_id`='" . $_SESSION['user_id'] . 
    "',`title`='$title',`date`=$date,`start_time`=$time,`location`='$location'," . 
    "`description`='$description' WHERE `event_id`='$event_id'");
  handle_sql_error($mysqli);
  header("Location: $redirect_url&msg=eventupdatesuccess");
}
exit();
?>
