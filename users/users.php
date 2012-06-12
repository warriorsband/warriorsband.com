<?php

/*
 *  users.php
 *
 *  Lists all the users, with links to their profiles.
 */

session_start(); 
$redirect_url = $_SERVER['PHP_SELF'];
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');

function user_type_to_str($user_type) {
  if ($user_type == 1) {
    return "Member";
  } elseif ($user_type == 2) {
    return "Exec";
  } elseif ($user_type == 3) {
    return "Admin Exec";
  } elseif ($user_type == 4) {
    return "Admin";
  } else {
    return "";
  }
}

//Outputs 'class="alt"' on even-numbered rows, so that it can be used to define the 
//class of rows and :alt" can be given a different colour in the css style
function row_color() {
  static $row_count = 0;
  if ($odd = ++$row_count % 2) {
    return '';
  } else {
    return 'class="alt"';
  }
}

$result = mysql_query("SELECT `first_name`,`last_name`,`email`,`user_type`, `user_id` from `users` ORDER BY `last_name`, `first_name`")
  or die(mysql_error());
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band Member List</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
    <h2>Warriors Band Member List</h2>
    <br />
    <table>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>E-mail address</th>
        <th>User type</th>
        <th></th>
      </tr>
<?php
while ($row = mysql_fetch_row($result)) {
  echo '<tr '.row_color().'>';
  echo '<td>'.$row[0].'</td>';
  echo '<td>'.$row[1].'</td>';
  echo '<td>'.$row[2].'</td>';
  echo '<td>'.user_type_to_str($row[3]).'</td>';
  echo '<td><a href="'.$domain.'/users/profile.php?user_id='.$row[4].'">Profile</a></td>';
  echo '</tr>';
}
mysql_free_result($result); ?>
    </table>
    <br /><br />
    <a href="/index.php">Back to homepage</a>
    </center>
  </body>
</html>
