<?php

/*
 *  event.php
 *  
 *  A form which view or edits an existing event or creates a new event by posting to 
 *  event-exec.php
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Ensure that the user is allowed to view events
if (!auth_view_events()) {
  print_and_exit("You are not authorized to view events.");
}

row_color(TRUE);

//Action being returned to the user: either "view", "edit", or "create".
$action = "view";

//If the requester can edit events, set variables to define the values of the 
//form elements, and if an event ID is provided, fetch that event's info.
if (auth_edit_events()) {
  // Default selections for the form
  $status = 2;
  $title = "";
  $no_date = FALSE;
  $date_year = 2012;
  $date_month = 1;
  $date_day = 1;
  $no_time = FALSE;
  $time_hour = 5;
  $time_minute = 30;
  $time_ampm = "PM";
  $location = "";
  $details = "";

  //If an event ID was specified, try to load that event
  if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $action = "edit";

    //Get the event details from the database
    //If no row is found, print an error and exit.
    $event_row = $mysqli->query(
      "SELECT `status`,`title`,`date`,TIME_FORMAT(`start_time`, '%h%i%p') AS `time`," .
      "`location`,`details` " .
      "FROM `events` WHERE `event_id`='$event_id'")->fetch_assoc();
    handle_sql_error($mysqli);
    if (!$event_row) {
      print_and_exit("No such event with that event ID.");
    }
    $status = intval($event_row['status']);
    $title = $event_row['title'];
    if (is_null($event_row['date'])) {
      $no_date = TRUE;
      $date_year = 2012;
      $date_month = 1;
      $date_day = 1;
    } else {
      $no_date = FALSE;
      $date = explode("-", $event_row['date']);
      $date_year = intval($date[0]);
      $date_month = intval($date[1]);
      $date_day = intval($date[2]);
    }
    if (is_null($event_row['time'])) {
      $no_time = TRUE;
      $time_hour = 5;
      $time_minute = 30;
      $time_ampm = "PM";
    } else {
      $no_time = FALSE;
      $time_hour = intval(substr($event_row['time'],0,2));
      $time_minute = intval(substr($event_row['time'],2,2));
      $time_ampm = substr($event_row['time'],4,2);
    }
    $location = $event_row['location'];
    $details = $event_row['details'];
  } else {
    $action = "create";
  }
}
//Otherwise the requester can view, but not edit, events
else {
  //If an event ID was specified, try to load that event
  if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $action = "view";

    //Get the event details from the database
    //If no row is found, print an error and exit.
    $event_row = $mysqli->query(
      "SELECT `status`,`title`,DATE_FORMAT(`date`,'%b %e %Y') AS `date`," .
      "TIME_FORMAT(`start_time`, '%l:%i %p') AS `time`,`location`,`details` " .
      "FROM `events` " .
      "WHERE `event_id`='$event_id'")->fetch_assoc();
    handle_sql_error($mysqli);
    if (!$event_row) {
      print_and_exit("No such event with that event ID.");
    }
  } else {
    print_and_exit("No event ID specified.");
  }
}

//Start displaying page
if ($action == "create") {
?>
<h1>Create Event</h1>
<div class="ctext8">
  Just fill in the required fields and click "Create Event". Note that all of this can be 
  updated later, and only the title is required right now; so you can fill out as much as you know 
  about the event at this time, and update it later.
</div>
<?php
} elseif ($action == "edit") {
?>
<h1>View/Edit Event</h1>
<div class="center">
  <a href="<?php echo "$domain?page=events"?>">Back to list of events</a>
</div>
<?php
} else {
?>
<h1>View Event</h1>
<div class="center">
  <a href="<?php echo "$domain?page=events"?>">Back to list of events</a>
</div>
<?php
}
?>

<br /><br />
<table>
<?php
if ($action == "view") {
?>
  <tr <?php echo row_color() ?> >
    <th>Title</th>
    <td><?php echo $event_row['title']; ?></td>
  </tr>
<?php
} else {
?>
  <form action="/events/event-exec.php" method="POST">
<?php
  if (isset($event_id)) {
?>
    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
<?php
  }
?>
    <tr <?php echo row_color() ?> >
      <th>Title<br /><span class="tiph">(required)</span></th>
      <td><input type="text" name="title" maxlength="255" value="<?php echo $title; ?>" /></td>
    </tr>
<?php
}

//Display date
if ($action == "view") {
?>
  <tr <?php echo row_color() ?> >
    <th>Date</th>
    <td><?php echo $event_row['date']; ?></td>
  </tr>
<?php
} else {
?>
    <tr <?php echo row_color() ?> >
      <th>Date</th>
      <td>
        <select name="date_day">
<?php
    for ($i = 1; $i <= 31; $i++) {
      echo "<option value=\"$i\" ";
      selected($i,$date_day);
      echo ">$i</option>";
    }
?>
        </select> / 
        <select name="date_month">
          <option value="1" <?php selected(1,$date_month); ?>>January</option>
          <option value="2" <?php selected(2,$date_month); ?>>February</option>
          <option value="3" <?php selected(3,$date_month); ?>>March</option>
          <option value="4" <?php selected(4,$date_month); ?>>April</option>
          <option value="5" <?php selected(5,$date_month); ?>>May</option>
          <option value="6" <?php selected(6,$date_month); ?>>June</option>
          <option value="7" <?php selected(7,$date_month); ?>>July</option>
          <option value="8" <?php selected(8,$date_month); ?>>August</option>
          <option value="9" <?php selected(9,$date_month); ?>>September</option>
          <option value="10" <?php selected(10,$date_month); ?>>October</option>
          <option value="11" <?php selected(11,$date_month); ?>>November</option>
          <option value="12" <?php selected(12,$date_month); ?>>December</option>
        </select> / 
        <select name="date_year">
<?php
    for ($i = 2012; $i <= 2030; $i++) {
      echo "<option value=\"$i\" ";
      selected($i,$date_year);
      echo ">$i</option>";
    }
?>
        </select>
        &nbsp &nbsp
        <input type="checkbox" name="no_date" value="true" <?php checked(TRUE,$no_date); ?>/> Leave date blank
      </td>
    </tr>
<?php
}

//Display time
if ($action == "view") {
?>
  <tr <?php echo row_color() ?> >
    <th>Start Time</th>
    <td><?php echo $event_row['time']; ?></td>
  </tr>
<?php
} else {
?>
    <tr <?php echo row_color() ?> >
      <th>Start Time</th>
      <td>
        <select name="time_hour">
          <option value="1" <?php selected(1,$time_hour); ?>>1</option>
          <option value="2" <?php selected(2,$time_hour); ?>>2</option>
          <option value="3" <?php selected(3,$time_hour); ?>>3</option>
          <option value="4" <?php selected(4,$time_hour); ?>>4</option>
          <option value="5" <?php selected(5,$time_hour); ?>>5</option>
          <option value="6" <?php selected(6,$time_hour); ?>>6</option>
          <option value="7" <?php selected(7,$time_hour); ?>>7</option>
          <option value="8" <?php selected(8,$time_hour); ?>>8</option>
          <option value="9" <?php selected(9,$time_hour); ?>>9</option>
          <option value="10" <?php selected(10,$time_hour); ?>>10</option>
          <option value="11" <?php selected(11,$time_hour); ?>>11</option>
          <option value="12" <?php selected(12,$time_hour); ?>>12</option>
        </select> : 
        <select name="time_minute">
          <option value="0" <?php selected(0,$time_minute); ?>>00</option>
          <option value="5" <?php selected(5,$time_minute); ?>>05</option>
          <option value="10" <?php selected(10,$time_minute); ?>>10</option>
          <option value="15" <?php selected(15,$time_minute); ?>>15</option>
          <option value="20" <?php selected(20,$time_minute); ?>>20</option>
          <option value="25" <?php selected(25,$time_minute); ?>>25</option>
          <option value="30" <?php selected(30,$time_minute); ?>>30</option>
          <option value="35" <?php selected(35,$time_minute); ?>>35</option>
          <option value="40" <?php selected(40,$time_minute); ?>>40</option>
          <option value="45" <?php selected(45,$time_minute); ?>>45</option>
          <option value="50" <?php selected(50,$time_minute); ?>>50</option>
          <option value="55" <?php selected(55,$time_minute); ?>>55</option>
        </select>
        <select name="time_ampm">
          <option value="AM" <?php selected("AM",$time_ampm); ?>>AM</option>
          <option value="PM" <?php selected("PM",$time_ampm); ?>>PM</option>
        </select>
        &nbsp &nbsp
        <input type="checkbox" name="no_time" value="true" <?php checked(TRUE,$no_time) ?> /> Leave time blank
      </td>
    </tr>
<?php
}

//Display location
if ($action == "view") {
?>
  <tr <?php echo row_color() ?> >
    <th>Location</th>
    <td><?php echo $event_row['location']; ?></td>
  </tr>
<?php
} else {
?>
    <tr <?php echo row_color() ?> >
      <th>Location</th>
      <td><input type="text" name="location" maxlength="255" value="<?php echo $location; ?>"/></td>
    </tr>
<?php
}

//Display details
if ($action == "view") {
?>
  <tr <?php echo row_color() ?> >
    <th>Details</th>
    <td><?php echo nl2br($event_row['details']); ?></td>
  </tr>
<?php
} else {
?>
    <tr <?php echo row_color() ?> >
      <th>Details</th>
      <td><textarea name="details" rows="6" cols="80" maxlength="10000"><?php echo $details; ?></textarea></td>
    </tr>
<?php
}

if ($action != "create") {
?>
  <tr <?php echo row_color() ?> >
    <th>Who's Going</th>
    <td>
<?php
//If appropriate, display message saying the user doesn't have permission to view attendees
//(note since "create" can only occur for logged-in users, we can skip checking it here)
  if (!logged_in() && $event_row['status'] == 1) {
?>
      Only logged-in members can view event attendees.
<?php
  }
//If appropriate, display "event isn't open yet" message
  elseif ($action == "view" && $event_row['status'] == 2) {
?>
      This event has not yet been made open to responses.
<?php
  }
//If appropriate, display the list of attendees
  elseif (logged_in() && $action != "create" && $event_row['status'] == 1) {
?>
      Definitely attending: 
<?php
    $yess = $mysqli->query(
      "SELECT `first_name` " .
      "FROM `users` " .
      "INNER JOIN `event_responses` " .
      "ON `users`.`user_id`=`event_responses`.`user_id` " .
      "WHERE `event_id`='$event_id' AND `response`='1'");
      handle_sql_error($mysqli);
    while($yesrow = $yess->fetch_assoc()) {
      echo $yesrow['first_name'] . ", ";
    }
    $yess->free();
?>
      <br />Maybe attending: 
<?php
    $maybes = $mysqli->query(
      "SELECT `first_name` " .
      "FROM `users` " .
      "INNER JOIN `event_responses` " .
      "ON `users`.`user_id`=`event_responses`.`user_id` " .
      "WHERE `event_id`='$event_id' AND `response`='3'");
      handle_sql_error($mysqli);
    while($mayberow = $maybes->fetch_assoc()) {
      echo $mayberow['first_name'] . ", ";
    }
    $maybes->free();
?>
      <br />Not attending: 
<?php
    $nos = $mysqli->query(
      "SELECT `first_name` " .
      "FROM `users` " .
      "INNER JOIN `event_responses` " .
      "ON `users`.`user_id`=`event_responses`.`user_id` " .
      "WHERE `event_id`='$event_id' AND `response`='2'");
      handle_sql_error($mysqli);
    while($norow = $nos->fetch_assoc()) {
      echo $norow['first_name'] . ", ";
    }
    $nos->free();
    if ($action == "edit") {
      echo "<br /><a href=\"$domain?page=eventresponses&event_id=$event_id\">View full response list</a>";
    }
  }
//If appropriate, display a checkbox to mark event as upcoming
  elseif ($action == "edit" && $event_row['status'] == 2) {
?>
      This event has not yet been made open to responses. <br />
      <input type="checkbox" name="mark_upcoming" value="" />Open this event to responses<br />
<?php
    if (user_type_greater_eq(3)) {
?>
      <input type="checkbox" name="send_email" value="" />Also send notification e-mail<br />
<?php
    }
?>
      <span class="tip">
        (Note: Opening an event to responses can't be undone; neither can sending the notification 
        e-mail to all on-campus members. Only do either of these if you're sure the event details 
        are filled in and it's time to find out who can attend.)
      </span>
<?php
  }
//This should never happen, but just in case, if none of the above apply, show error
  else {
?>
      Error
<?php
  }
}
?>
    </td>
  </tr>

<?php
//Display appropriate submit button (if any)
if ($action == "create") {
?>
    <tr <?php echo row_color() ?> >
      <th></th>
      <td style="text-align:center"><input type="submit" value="Create Event" /></td>
    </tr>
  </form>
<?php
} elseif ($action == "edit") {
?>
    <tr <?php echo row_color() ?> >
      <th></th>
      <td style="text-align:center"><input type="submit" value="Update Event" /></td>
    </tr>
  </form>
<?php
}
?>
</table>

<?php
//If the requester can edit events, display a delete form/button
if ($action == "edit") { ?>
<br /><br />
<div class="center">
  <form action="/events/deleteevent-exec.php" method="POST">
    <input type="hidden" name="event_id" value="<?php echo $event_id ?>" />
<?php
  if (isset($_GET['msg']) && ($_GET['msg'] == "confirmdelete")) {
?>
    <input type="hidden" name="confirm" value="true" />
<?php
  }
?>
    <input type="submit" value="Delete This Event" />
  </form>
</div>
<?php
}
?>

<?php
//If the user is logged in, and the event is existing and active, show a response form
if (logged_in()) {
  if (isset($event_id) && ($event_row['status'] == 1)) {
    //Get existing response if there is one
    $response_row = $mysqli->query(
      "SELECT `response`,`comment` " .
      "FROM `event_responses` " .
      "WHERE `event_id`='$event_id' AND `user_id`='" . $_SESSION['user_id'] . "'")->fetch_assoc();
    handle_sql_error($mysqli);
    if ($response_row) {
      $response = $response_row['response'];
      $comment = $response_row['comment'];
    } else {
      $response = 1;
      $comment = "";
    }
?>
<br /><br />
<table><tr><td class="contenttd">
  <a name="respond">Will you be able to attend this event?</a><br />
  (If you answer "maybe", you must enter an explanation in the box below)
  <br />
  <form action="/events/eventresponse-exec.php" method="POST">
    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
    <input type="radio" name="response" value="1" <?php checked(1,$response); ?> /> Yes &nbsp
    <input type="radio" name="response" value="2" <?php checked(2,$response); ?> /> No &nbsp
    <input type="radio" name="response" value="3" <?php checked(3,$response) ?> /> Maybe
    <br />
    <textarea name="comment" rows="5" cols="80"><?php echo $comment; ?></textarea>
    <br /><br />
    <div class="center"><input type="submit" value="Submit Response" /></div>
  </form>
</td></tr></table>
<?php
  } elseif ($action != "create") {
?>
<br /><br />
<div class="center">This event is not open to attendance responses at this time.</div>
<?php
  }
}
?>
