<?php

/*
 *  events.php
 *
 *  Lists band events and provides buttons for execs to create new events
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

function printcell_maybe($item) {
  echo "<td>";
  if (empty($item)) {
    echo "TBA";
  } else {
    echo $item;
  }
  echo "</td>";
}

$no_events = FALSE;

$result = $mysqli->query(
  "SELECT `event_id`,`status`,`creator_id`,`title`,DATE_FORMAT(`date`,'%b %e %Y') AS `fdate`," .
  "TIME_FORMAT(`start_time`,'%l:%i %p') AS `ftime`,`location` " .
  "FROM `events` " .
  "WHERE (`date` = NULL OR `date` >= NOW()) " .
  "ORDER BY IF(ISNULL(`date`),1,0),`date`,IF(ISNULL(`start_time`),1,0),`start_time`");
handle_sql_error($mysqli);
if ($result->num_rows == 0) {
  $no_events = TRUE;
}
?>

<h3>Events</h3>
<div class="ctext8">
  Here's a list of our upcoming events! Click "Event Details" to view full event info or to 
  respond to an event. If you haven't yet responded to an active event, a "Respond" link will 
  also appear beside it.
</div>
<br />
<?php
if(auth_view_events()) {
?>
<br />
<?php
  if ($no_events == TRUE) {
?>
<div class="center">
  There are currently no scheduled events.
</div>
<?php
  } else {
?>
<table>
  <tr>
<?php
    if (isset($_SESSION['responses']) && $_SESSION['responses'] > 0) {
?>
    <th></th>
<?php
    }
?>
    <th>Date</th>
    <th>Time</th>
    <th>Event</th>
    <th>Location</th>
    <th>Attendees</th>
    <th></th>
  </tr>
<?php
    while ($event_row = $result->fetch_assoc()) {
      $responses_row = $mysqli->query(
        "SELECT COUNT(*)" .
        "FROM `event_responses`" .
        "WHERE `event_id`='" . $event_row['event_id'] . "' AND `response`='1'")->fetch_row();
      handle_sql_error($mysqli);
      if (logged_in()) {
        $userresponse_row = $mysqli->query(
          "SELECT COUNT(*) " .
          "FROM `event_responses` " .
          "WHERE `event_id`='" . $event_row['event_id'] . "' AND `user_id`='" . $_SESSION['user_id'] . "'")->fetch_row();
        handle_sql_error($mysqli);
        if (isset($_SESSION['responses']) && $_SESSION['responses'] > 0) {
          if ($event_row['status'] == 1 && $userresponse_row[0] == 0) {
            echo '<td><a href="' . $domain . '?page=event&event_id=' . $event_row['event_id'] . '#respond">Respond</a></td>';
          } else {
            echo '<td></td>';
          }
        }
      }
      printcell_maybe($event_row['fdate']);
      printcell_maybe($event_row['ftime']);
      echo '<td>'.$event_row['title'].'</td>';
      printcell_maybe($event_row['location']);
      if (intval($event_row['status']) == 1) {
        echo '<td>'.$responses_row[0].'</td>';
      } else {
        echo '<td></td>';
      }
      echo '<td><a href="' . $domain . '?page=event&event_id=' . $event_row['event_id'] . '">Event Details</a></td>';
      echo '</tr>';
    }
    $result->free();
?>
</table>
<?php
  }
} else {
?>
<div class="center">
  You are not authorized to view events.
</div>
<?php
}
?>
