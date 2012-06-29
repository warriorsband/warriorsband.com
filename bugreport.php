<?php

require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once("Mail.php");

if (isset($_POST['comment'])) {
  $from = "Warriors Band <" . registration_email_from() . ">";
  $to = "<ironmaiden1158@gmail.com>";
  $subject = "Warriors Band Comment / Bug Report";

  $headers = array ('From' => $from, 
    'To' => $to,
    'Subject' => $subject);
  $smtp = Mail::factory('smtp',
    array ('host' => $email_host,
    'port' => $email_port,
    'auth' => true,
    'username' => $email_username,
    'password' => $email_password));
  $body = "User id: " . $_SESSION['user_id'] . "\n\nComment:\n" . $_POST['comment'];

  $mail = $smtp->send($to, $headers, $body);

  if (!PEAR::isError($mail)) {
    header("Location: $domain?page=bugreport&msg=bugreportsuccess");
  } else {
    header("Location: $domain?page=bugreport&msg=bugreportfail");
  }

  exit();
}
?>
<h3>Post Comment / Report Bug</h3>
  If you run into any problems with the site, or if you have comments/suggestions regarding 
  site features, design, etc, write it down here. Just, like, let it all out man. Good Guy Paul 
  will get be sent an email with your message.
<br /><br />
<div class="center">
  <form action="/bugreport.php" method="POST">
    <textarea name="comment" rows="8" cols="80"></textarea>
    <br /><br />
    <input type="submit" value="Submit" />
  </form>
</div>
