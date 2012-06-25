<?php

/*
 *  index.php
 *
 *  The main Warriors Band page!
 */

session_start(); 
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>UW Warriors Band</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
  </head>

  <body>
    <table id="header" class="contenttable">
      <tr>
        <td class="contenttd logotd">
          <a href="/"><img id=logo src="/images/logo_small.png" alt="Warriors Band" /></a>
        </td>
        <td class="contenttd headertd">
          <div id="header_text" class="center">
            <img style="vertical-align:middle" src="/images/uw_crest.png" />
            <span class="header1">University of Waterloo</span><br />
            <span class="header2">WARRIORS BAND</span><br />
            <span class="header3">"One of the Bands in Canada"</span>
          </div>
        </td>
      </tr>
    </table>
    <hr />
    <?php print_msg(); ?>
    <table class="contenttable">
      <tr>
        <td id="navigation" class="contenttd navigation">
          <table class="navigation">
            <tr><th class="center">Navigation</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/">Home</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=about">About</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=events">Events</a>
            </td></tr>
<?php if (logged_in()) {
  row_color(TRUE); ?>
            <tr><th class="center">Member links</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=users">Member list</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=profile">View your profile</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/auth/logout.php">Logout</a>
            </td></tr>
<?php if (user_type_greater_eq(2)) {
  row_color(TRUE); ?>
            <tr><th class="center">Exec links</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=register">Register new user</a>
            </td></tr>
<?php } } else { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=login">Login</a>
            </td></tr>
<?php } row_color(TRUE); ?>
          </table>
        </td>
        <td id="content" class="contenttd">
<?php
if (isset($_GET['page'])) {
  switch ($_GET['page']) {
    case "403":
      require($_SERVER['DOCUMENT_ROOT'].'/403.php');
      break;
    case "about":
      require($_SERVER['DOCUMENT_ROOT'].'/about.php');
      break;
    case "event":
      require($_SERVER['DOCUMENT_ROOT'].'/events/event.php');
      break;
    case "events":
      require($_SERVER['DOCUMENT_ROOT'].'/events/events.php');
      break;
    case "login":
      require($_SERVER['DOCUMENT_ROOT'].'/auth/login.php');
      break;
    case "newevent":
      require($_SERVER['DOCUMENT_ROOT'].'/events/newevent.php');
      break;
    case "profile":
      require($_SERVER['DOCUMENT_ROOT'].'/users/profile.php');
      break;
    case "register":
      require($_SERVER['DOCUMENT_ROOT'].'/users/register.php');
      break;
    case "users":
      require($_SERVER['DOCUMENT_ROOT'].'/users/users.php');
      break;
    default:
      require($_SERVER['DOCUMENT_ROOT'].'/404.php');
  }
} else { ?>
          <div class="center">
            Woo, new site! Still heavily under construction.<br /><br />
          </div>
          <div class="center">
            <img src="/images/band_photo.jpg" />
          </div>
<?php } ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="contenttd center">
          <br /><br />
          University of Waterloo | 200 University Ave. W. | Waterloo, Ontario, Canada | N2L 
          3G1 | 519.888.4567 | <a href="http://www.uwaterloo.ca">www.uwaterloo.ca</a>
        </td>
      </tr>
    </table>
  </body>
</html>
