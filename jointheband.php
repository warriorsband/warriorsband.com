<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once("Mail.php");

row_color(TRUE);

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
  //Sanitize and validate input
  $name=htmlspecialchars(trim($_POST["name"]));
  $email=sanitize($_POST["email"]);
  $message=htmlspecialchars(trim($_POST["message"]));
  if (strlen($name) > 64) {
    error_and_exit("Name must be at most 64 characters.");
  }
  if (!valid_email($email)) {
    header("Location: $domain?page=jointheband&msg=bademail");
    exit();
  }
  if (empty($message)) {
  }
  if (strlen($message) > 10000) {
    error_and_exit("Message must be at most 10000 characters.");
  }

  $from = "$name <$email>";
  $to = "Warriors Band <$email_username>";
  $subject = jointheband_email_subject($name);

  $headers = array ('From' => $from, 
    'To' => $to,
    'Subject' => $subject);
  $smtp = Mail::factory('smtp',
    array ('host' => $email_host,
    'port' => $email_port,
    'auth' => true,
    'username' => $email_username,
    'password' => $email_password));
  $body = jointheband_email_message($name, $email, $message);

  $mail = $smtp->send($to, $headers, $body);

  if (!PEAR::isError($mail)) {
    header("Location: $domain?page=jointheband&msg=jointhebandsuccess");
  } else {
    header("Location: $domain?page=jointheband&msg=jointhebandfail");
  }
  exit();
}
?>
<h3>Join the Band / Ask a Question</h3>
  <p>Interesting in joining the band? Want to find out more about us? Leave your name, e-mail and 
  message here and we'll get back to you with whatever it is you'd like to know.</p>

  <p>You can also just show up at a practice (Thursdays at 5:30 PM in PAC 1001) and grab/bring an 
  instrument, no registration required!</p>
<br /><br />
<table>
  <form action="/jointheband.php" method="POST">
    <tr <?php echo row_color() ?> >
      <th>Name</th>
      <td><input type="text" name="name" maxlength="64" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>E-mail</th>
      <td><input type="text" name="email" maxlength="255" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>Message</th>
      <td><textarea name="message" rows="8" cols="80" maxlength="10000"></textarea></td>
    </tr>
    <tr><td class="center" colspan=2><input type="submit" value="Submit" /></td></tr>
  </form>
</table>
