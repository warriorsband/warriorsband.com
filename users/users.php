<?php

/*
 *  users.php
 *
 *  Lists all the users, with links to their profiles.
 */

$redirect_page = "users";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');

row_color(TRUE);

$result = $mysqli->query(
  "SELECT `first_name`,`last_name`,`email`,`user_type`, `user_id` " .
  "FROM `users` " .
  "ORDER BY `last_name`, `first_name`");
handle_sql_error($mysqli);

$print_email = FALSE;
if (auth_view_emails()) {
  $print_email = TRUE;
}
?>

<h3>Member List</h3>
<div>
  This is a list of all the current members of the band. You can click on the profile link of any 
  member to view their profile page (though currently there is not much there!).
</div>
<br /><br />
<table>
  <tr>
    <th>First Name</th>
    <th>Last Name</th>
<?php
if ($print_email) {
?>
    <th>E-mail address</th>
<?php
}
?>
    <th>User type</th>
    <th></th>
  </tr>
<?php
while ($row = $result->fetch_assoc()) {
  echo '<tr ' . row_color() . '>';
  echo '<td>' . $row['first_name'] . '</td>';
  echo '<td>' . $row['last_name'] . '</td>';
  if ($print_email) {
    echo '<td>' . $row['email'] . '</td>';
  }
  echo '<td>' . user_type_to_str($row['user_type']) . '</td>';
  echo '<td><a href="?page=profile&user_id=' . $row['user_id'] . '">Profile</a></td>';
  echo '</tr>';
}
$result->free();
?>
</table>
