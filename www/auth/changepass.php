<?php

/*
 *  changepass.php
 *  
 *  A simple page which shows a change password prompt, and posts to profile-exec.php
 *  (similar to the profile page).
 */

$redirect_page = "changepass";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
?>

<h3>Change Password</h3>
<div>
  It looks like you've logged in for the first time. Welcome! We just need you to change your password 
  to something you'll remember. Do so here and then start exploring the site! (If you don't, this page
  will keep bugging you every time you log in)
</div>
<br /><br />
<table><tr><td class="contenttd">
  <form action="/users/profile-exec.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
    Current password: <input style="width: 100%" type="password" name="password" maxlength="64" /><br />
    New password: <input style="width: 100%" type="password" name="newpassword" maxlength="64" /><br />
    Retype password: <input style="width: 100%" type="password" name="newpassword1" maxlength="64" /><br />
    <br />
    <div class="center">
      <input type="submit" value="Change Password" />
    </div>
  </form>
</table>
