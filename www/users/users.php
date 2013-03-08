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

// If a filter for the users list is provided, use it, otherwise default to "all"
$users_filter = "all";
$users_filter_sql = "";
if (isset($_GET['filter']) && $_GET['filter'] != "all") {
  $users_filter = sanitize($_GET['filter']);
  switch ($users_filter) {
    case "exec":
      $users_filter_sql = "WHERE `user_type` BETWEEN 2 AND 3 ";
      break;
    case "unactivated":
      $users_filter_sql = "WHERE `last_login` IS NULL ";
      break;
    case "1monthold":
      $users_filter_sql = "WHERE `last_login` < DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
      break;
    case "4monthsold":
      $users_filter_sql = "WHERE `last_login` < DATE_SUB(NOW(), INTERVAL 4 MONTH) ";
      break;
    case "1yearold":
      $users_filter_sql = "WHERE `last_login` < DATE_SUB(NOW(), INTERVAL 1 YEAR) ";
      break;
    default:
      error_and_exit("Invalid filter provided.");
  }
}

$result = $mysqli->query(
  "SELECT `first_name`,`last_name`,`email`,`user_type`, `user_id` " .
  "FROM `users` " .
  $users_filter_sql .
  "ORDER BY `last_name`, `first_name`");
handle_sql_error($mysqli);

$print_email = FALSE;
if (auth_view_emails()) {
  $print_email = TRUE;
}
?>

<h3>Member List</h3>
<div class="ctext8">
  This is a list of all the current members of the band. You can click on the profile link of any 
  member to view their profile page.
</div>
<br />
<form action="<?php echo $domain ?>" method="GET">
  <input type="hidden" name="page" value="users" />
  Show: 
  <select name="filter">
    <option value="all" <?php selected("all",$users_filter) ?>>All members</option>
    <option value="exec" <?php selected("exec",$users_filter) ?>>Execs only</option>
<?php
if (user_type_greater_eq(2)) {
?>
    <option value="unactivated" <?php selected("unactivated",$users_filter) ?>>Unactivated members</option>
    <option value="1monthold" <?php selected("1monthold",$users_filter) ?>>Last login &gt;1 month ago</option>
    <option value="4monthsold" <?php selected("4monthsold",$users_filter) ?>>Last login &gt;4 months ago</option>
    <option value="1yearold" <?php selected("1yearold",$users_filter) ?>>Last login &gt;1 year ago</option>
<?php
}
?>
  </select>
  <input type="submit" value="Refresh" />
</form>
<br />
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
