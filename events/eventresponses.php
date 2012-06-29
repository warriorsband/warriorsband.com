<?php

/*
 *  eventresponses.php
 *
 *  List all the responses to a given event
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if (!auth_view_responses()) {
  echo 'You are not authorized to view event responses.';
  exit();
}

row_color(TRUE);

//Make sure an event ID was provided
$event_id = intval($_GET['event_id']);
if ($event_id <= 0) {
  echo 'No event ID provided.';
  exit();
}

//Get the list of event responses
$result = mysql_query(
  "SELECT `first_name`,`last_name`,`response`,`comment` " .
  "FROM `event_responses` INNER JOIN `users` ON `event_responses`.`user_id` = `users`.`user_id` " .
  "WHERE `event_id`='$event_id' " .
  "ORDER BY `last_name`,`first_name`");
?>

<h3>Event Responses</h3>
<div class="center">
  Here is the list of attendance responses for 
  <a href="<?php echo "$domain?page=event&amp;event_id=$event_id"; ?>">this</a> event.
</div>
<br />
<table>
  <tr>
    <th>Name</th>
    <th>Response</th>
    <th>Comment</th>
  </tr>
<?php
while ($row = mysql_fetch_row($result)) {
  echo '<tr ' . row_color() . ' >';
  echo '<td>' . $row[0] . ' ' . $row[1] . '</td>';
  echo '<td>' . response_to_str($row[2]) . '</td>';
  echo '<td>' . $row[3] . '</td>';
  echo '</tr>';
}
mysql_free_result($result); ?>
</table>
