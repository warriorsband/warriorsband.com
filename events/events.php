<?php

/*
 *  events.php
 *
 *  Lists band events and provides buttons for execs to create new events
 */

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

$result = mysql_query(
  "SELECT `event_id`,`status`,`creator_id`,`title`,DATE_FORMAT(`date`,'%b %e %Y')," .
  "TIME_FORMAT(`start_time`,'%l:%i %p'),`location` " .
  "FROM `events` " .
  "WHERE `status` = 1 AND (`date` = NULL OR `date` >= NOW()) " .
  "ORDER BY IF(ISNULL(`date`),1,0),`date`,IF(ISNULL(`start_time`),1,0),`start_time`");
if (mysql_num_rows($result) == 0) {
  $no_events = TRUE;
}
?>

<h3>Events</h3>
<div class="center">
  Here's a list of our upcoming events!
</div>
<br />
<?php if(auth_view_events()) { ?>
<br />
<?php if ($no_events == TRUE) { ?>
<div class="center">
  There are no currently scheduled events.
</div>
<?php } else { ?>
<table>
  <tr>
<?php if ($_SESSION['responses'] > 0) { ?>
    <th></th>
<?php } ?>
    <th>Date</th>
    <th>Time</th>
    <th>Event</th>
    <th>Location</th>
    <th>Attendees</th>
    <th></th>
  </tr>
<?php
while ($row = mysql_fetch_row($result)) {
  $row2 = mysql_fetch_array( mysql_query(
    "SELECT COUNT(*)" .
    "FROM `event_responses`" .
    "WHERE `event_id`='" . $row[0] . "' AND `response`='1'"));
  if (logged_in() && $_SESSION['responses'] > 0) {
    $row3 = mysql_fetch_array( mysql_query(
      "SELECT COUNT(*) FROM `event_responses` WHERE `event_id`='" . $row[0] . "' AND `user_id`='" .
      $_SESSION['user_id'] . "'"));
    if ($row3[0] == 0) {
      echo '<td><a href="' . $domain . '?page=event&event_id=' . $row[0] . '#respond">Respond</a></td>';
    } else {
      echo '<td></td>';
    }
  }
  printcell_maybe($row[4]);
  printcell_maybe($row[5]);
  echo '<td>'.$row[3].'</td>';
  printcell_maybe($row[6]);
  if (intval($row[1]) == 1) {
    echo '<td>'.$row2[0].'</td>';
  } else {
    echo '<td></td>';
  }
  echo '<td><a href="' . $domain . '?page=event&event_id=' . $row[0] . '">Event Details</a></td>';
  echo '</tr>';
}
mysql_free_result($result); ?>
</table>
<?php } } else { ?>
<div class="center">
  You are not authorized to view events.
</div>
<?php } ?>
