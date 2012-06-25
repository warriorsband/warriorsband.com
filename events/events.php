<?php

/*
 *  events.php
 *
 *  Lists band events and provides buttons for execs to create new events
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

function printcell_maybe($item) {
  echo "<td>";
  if (isset($item)) {
    echo $item;
  } else {
    echo "TBA";
  }
  echo "</td>";
}

$no_events = FALSE;

$result = mysql_query("SELECT `event_id`,`creator_id`,`title`,DATE_FORMAT(`date`,'%b %e %Y')," .
  "TIME_FORMAT(`start_time`,'%H:%i %p'),`location` from `events` ORDER BY " .
  "IF(ISNULL(`date`),1,0),`date`,IF(ISNULL(`start_time`),1,0),`start_time`");
if (mysql_num_rows($result) == 0) {
  $no_events = TRUE;
}
?>

<h3>Events</h3>
<div class="center">
  Here's a list of our upcoming events!
</div>
<br />
<?php if(auth_view_events()) { 
  if (auth_edit_events()) { ?>
<div class="center">
  <a href="?page=event">Click here to create a new event</a>
</div>
<?php } ?>
<br />
<?php if ($no_events == TRUE) { ?>
<div class="center">
  There are no currently scheduled events.
</div>
<?php } else { ?>
<table>
  <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Event</th>
    <th>Location</th>
    <th></th>
  </tr>
<?php
while ($row = mysql_fetch_row($result)) {
  printcell_maybe($row[3]);
  printcell_maybe($row[4]);
  echo '<td>'.$row[2].'</td>';
  printcell_maybe($row[5]);
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
