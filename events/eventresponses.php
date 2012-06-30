<?php

/*
 *  eventresponses.php
 *
 *  List all the responses to a given event
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if (!auth_view_responses()) {
  error_and_exit("You are not authorized to view event responses.");
}

row_color(TRUE);

//Make sure an event ID was provided
$event_id = intval($_GET['event_id']);
if ($event_id <= 0) {
  error_and_exit("No event ID provided.");
}

$no_responses = FALSE;

//Get the list of event responses
$result = $mysqli->query(
  "SELECT `first_name`,`last_name`,`response`,`comment` " .
  "FROM `event_responses` INNER JOIN `users` ON `event_responses`.`user_id` = `users`.`user_id` " .
  "WHERE `event_id`='$event_id' " .
  "ORDER BY `last_name`,`first_name`");
handle_sql_error($mysqli);

if ($result->num_rows == 0) {
  $no_responses = TRUE;
}
?>

<h3>Event Responses</h3>
<?php
if ($no_responses) {
?>
<div class="center">
  There are no attendance responses for this event.
</div>
<?php
} else {
?>
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
  while ($response_row = $result->fetch_assoc()) {
    echo '<tr ' . row_color() . ' >';
    echo '<td>' . $response_row['first_name'] . ' ' . $response_row['last_name'] . '</td>';
    echo '<td>' . response_to_str($response_row['response']) . '</td>';
    echo '<td>' . $response_row['comment'] . '</td>';
    echo '</tr>';
  }
  $result->free();
}
?>
</table>
