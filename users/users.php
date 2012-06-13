<?php

/*
 *  users.php
 *
 *  Lists all the users, with links to their profiles.
 */

$redirect_url = $_SERVER['PHP_SELF'];
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/header.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

$result = mysql_query("SELECT `first_name`,`last_name`,`email`,`user_type`, `user_id` from `users` ORDER BY `last_name`, `first_name`")
  or die(mysql_error());

$print_email = FALSE;
if (user_type_greater_eq(2)) {
  $print_email = TRUE;
}
?>

<h2>Warriors Band Member List</h2>
<br />
<div class="pagedescription">
  This is a list of all the current members of the band. You can click on the profile link of any 
  member to view their profile page (though currently there is not much there!).
  <br /><br />
</div>
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
  echo '<td><a href="'.$domain.'/users/profile.php?user_id='.$row[4].'">Profile</a></td>';
  echo '</tr>';
}
mysql_free_result($result); ?>
</table>
<?php require($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?>
