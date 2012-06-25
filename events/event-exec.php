<?php

/*
 *  event-exec.php
 *
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
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

  //Fetch the event's info
  if (!($row = mysql_fetch_array( mysql_query("SELECT * FROM `events` WHERE `event_id`='$event_id'")))) {
    echo "No such event with that event ID.";
    exit();
  }
}

//Validate title
$title = sanitize($_POST['title']);
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
  if (!valid_date($date)) {
      header("Location: $redirect_url&msg=baddate");
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
  $time = "'$time_hour:$time_minute $time_ampm'";
}

//Validate location
$location = sanitize($_POST['location']);
if (empty($location)) {
  $location = "NULL";
} else {
  if (strlen($location) > 255) {
      header("Location: $redirect_url&msg=badlocation");
      exit();
  }
  $location = "'$location'";
}

//Validate description
$description = sanitize($_POST['description']);
if (empty($description)) {
  $description = "NULL";
} else {
  $description = "'$description'";
}

//If this is a new event, do an insertion, otherwise do an update
if ($new_event) {
  mysql_query("INSERT INTO `events` (`creator_id`,`title`,`date`,`start_time`,`location`,`description`)" . 
    "VALUES ('" . $_SESSION['user_id'] . "','$title',$date,$time,$location,$description)")
    or die(mysql_error());
  header("Location: $redirect_url&msg=eventcreatesuccess");
} else {
  mysql_query("UPDATE `events` SET `creator_id`='" . $_SESSION['user_id'] . 
    "`title`='$title',`date`=$date,`time`=$time,`location`=$location," . 
    "`description`=$description WHERE `event_id`='$event_id'")
    or die(mysql_error());
  header("Location: $redirect_url&msg=eventupdatesuccess");
}
?>
