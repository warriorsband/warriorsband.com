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

require($_SERVER['DOCUMENT_ROOT'].'/header.php');

if (isset($_GET['redirect_url'])) {
  $redirect_url = $_GET['redirect_url'];
}
?>
<?php if (!logged_in()) { ?>
<div class="pagedescription">
<?php if (isset($redirect_url)) { ?>
  You need to log in to access this page; please enter your e-mail address and password.
<?php } else { ?>
  Please enter your e-mail address and password.
<?php } ?>
  <br /><br />
</div>
  <!-- START OF LOGIN FORM -->
<?php if (isset($redirect_url)) { ?>
<?php } ?>
  <table>
    <form action="/auth/login-exec.php" method="POST">
      <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url) ?>">
      <tr>
        <th class="side">E-mail:</th>
        <td><input type="text" name="email" tabindex="1"></td>
        <td rowspan="2"><input type="submit" value="Login" tabindex="3"></td>
      </tr>
      <tr>
        <th class="side">Password:</th>
        <td><input type="password" name="password" tabindex="2"></td>
      </tr>
    </form>
  </table>
  <!-- END OF LOGIN FORM -->
<?php
} else { ?>
<div class="pagedescription">
  You are already logged in.
</div>
<?php }

require($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?>
