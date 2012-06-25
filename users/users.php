<?php

/*
 *  users.php
 *
 *  Lists all the users, with links to their profiles.
 */

$redirect_page = "users";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

row_color(TRUE);

$result = mysql_query("SELECT `first_name`,`last_name`,`email`,`user_type`, `user_id` from `users` ORDER BY `last_name`, `first_name`")
  or die(mysql_error());

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
<?php if ($print_email) { ?>
    <th>E-mail address</th>
<?php } ?>
    <th>User type</th>
    <th></th>
  </tr>
<?php
while ($row = mysql_fetch_row($result)) {
  echo '<tr '.row_color().'>';
  echo '<td>'.$row[0].'</td>';
  echo '<td>'.$row[1].'</td>';
  if ($print_email) {
    echo '<td>'.$row[2].'</td>';
  }
  echo '<td>'.user_type_to_str($row[3]).'</td>';
  echo '<td><a href="?page=profile&user_id=' . $row[4] . '">Profile</a></td>';
  echo '</tr>';
}
mysql_free_result($result); ?>
</table>
