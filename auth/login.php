<?php

/*
 *  login.php
 *
 *  Form which posts a login request to login-exec.php for validation.
 *  Accepts the following via GET:
 *
 *    redirect_url: An optional string indicating the URL (relative to the domain and starting 
 *                  with a /) to redirect to if login is successful.
 *    error:        An optional string indicating what error occurred during the login 
 *                  validation process.
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');

if (isset($_GET['redirect_url'])) {
  $redirect_url = $_GET['redirect_url'];
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band Login</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
  </head>

  <body >
    <center>
    <h2>Warriors Band Login</h2>
    <br />

<?php if (!logged_in()) { ?>
    <?php if (isset($redirect_url)) { ?>
    You need to log in to access this page; please enter your e-mail address and password.
    <?php } else { ?>
    Please enter your e-mail address and password.
    <?php } ?>
    <br /><br />
<?php print_msg() ?>
    <!-- START OF LOGIN FORM -->
    <form action="/auth/login-exec.php" method="POST">
      <?php if (isset($redirect_url)) { ?>
      <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url) ?>">
      <?php } ?>
      <table>
        <tr>
          <th>E-mail:</th>
          <td><input type="text" name="email" tabindex="1"></td>
          <td rowspan="2"><input type="submit" value="Login" tabindex="3"></td>
        </tr>
        <tr>
          <th>Password:</th>
          <td><input type="password" name="password" tabindex="2"></td>
        </tr>
      </table>
    </form>
    <!-- END OF LOGIN FORM -->
<?php
} else { ?>
    You are already logged in.
<?php } ?>
    <br /><br />
    <a href="/index.php">Back to homepage</a>
    </center>
  </body>
</html>
