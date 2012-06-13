<?php

/*
 *  register.php
 *  
 *  A form which posts to register-exec.php with the details required for creating 
 *  a new user.
 */

$redirect_url = $_SERVER['PHP_SELF'];
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/header.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

//Ensure that the user has exec level or above
ensure_minimum_type(2);
?>

<h2>New Member Registration</h2>
<br />
<div class="pagedescription">
  To use this page to register a new user, enter their e-mail address, name, and optionally 
  a comment and click "Register New Member". An automated message will be sent to their e-mail, 
  containing a temporary password, instructions for completing registration, and the comment 
  (if one was provided).
  <br /><br />
</div>
<form action="/users/register-exec.php" method="POST">
  <table>
    <tr>
      <th>E-mail</th>
      <td><input type="text" name="email" maxlength="255" /></td>
    </tr>
    <tr class="alt" >
      <th>First name</th>
      <td><input type="text" name="first_name" maxlength="255" /></td>
    </tr>
    <tr>
      <th>Last name</th>
      <td><input type="text" name="last_name" maxlength="255" /></td>
    </tr>
    <tr class="alt" >
      <th>Custom message</th>
      <td><input type="text" name="comment" maxlength="255" /></td>
    </tr>
    <tr>
      <th></th>
      <td style="text-align:center"><input style="width:150px" type="submit" value="Register New Member" /></td>
    </tr>
  </table>
</form>

<?php require($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?>
