<?php

/*
 *  index.php
 *
 *  This page contains the banner at the top of the page, and the navigation sidebar.
 *  It accepts `page` via GET, and the value of `page` defines what content will be 
 *  displayed.
 */

session_start(); 

// If the request URL doesn't start with "www.", redirect with "www." added.
// This is necessary because "warriorsband.com" and "www.warriorsband.com" 
// count as separate domains, and thus have separate PHP sessions. Without 
// this, users who log in on "warriorsband.com" will redirect to 
// "www.warriorsband.com" and have to log in again.
if (substr($_SERVER['HTTP_HOST'],0,4) !== 'www.') {
  header('Location: http://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

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
          <a href="/"><img id=logo src="/images/logos/logo_small.png" alt="Warriors Band" /></a>
        </td>
        <td class="contenttd headertd">
          <div id="header_text" class="center">
            <img style="vertical-align:middle" src="/images/logos/uw_crest.png" />
            <span class="header1">University of Waterloo</span><br />
            <span class="header2">WARRIORS BAND</span><br />
            <span class="header3">"One of the Bands in Canada"</span>
          </div>
        </td>
      </tr>
    </table>
    <hr />
    <?php print_eventreminder(); ?>
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
              <a href="/?page=history">History</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=events">Events</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=jointheband">Join the Band</a>
            </td></tr>
<?php if (logged_in()) {
  row_color(TRUE); ?>
            <tr><th class="center">Member links</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=users">Member list</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=profile">View/Edit your profile</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=bugreport">Comments / Bug Reports</a>
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
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=event">Create new event</a>
            </td></tr>
            <!--
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=photoupload">Upload photos</a>
            </td></tr>
            -->
<?php } } else { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=login">Login</a>
            </td></tr>
<?php } row_color(TRUE); ?>
          </table>
        </td>
        <td id="content" class="contenttd" valign="top">
<?php
if (isset($_GET['page'])) {
  switch ($_GET['page']) {
    case "403":
      require($_SERVER['DOCUMENT_ROOT'].'/403.php');
      break;
    case "about":
      require($_SERVER['DOCUMENT_ROOT'].'/about.php');
      break;
    case "bugreport":
      require($_SERVER['DOCUMENT_ROOT'].'/bugreport.php');
      break;
    case "changepass":
      require($_SERVER['DOCUMENT_ROOT'].'/auth/changepass.php');
      break;
    case "event":
      require($_SERVER['DOCUMENT_ROOT'].'/events/event.php');
      break;
    case "eventresponses":
      require($_SERVER['DOCUMENT_ROOT'].'/events/eventresponses.php');
      break;
    case "events":
      require($_SERVER['DOCUMENT_ROOT'].'/events/events.php');
      break;
    case "history":
      require($_SERVER['DOCUMENT_ROOT'].'/history/history.php');
      break;
    case "history1":
      require($_SERVER['DOCUMENT_ROOT'].'/history/annotated.php');
      break;
    case "history2":
      require($_SERVER['DOCUMENT_ROOT'].'/history/playboy.php');
      break;
    case "history3":
      require($_SERVER['DOCUMENT_ROOT'].'/history/queen.php');
      break;
    case "history4":
      require($_SERVER['DOCUMENT_ROOT'].'/history/olympics.php');
      break;
    case "history5":
      require($_SERVER['DOCUMENT_ROOT'].'/history/halifax.php');
      break;
    case "jointheband":
      require($_SERVER['DOCUMENT_ROOT'].'/jointheband.php');
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
    case "uploadphotos":
      require($_SERVER['DOCUMENT_ROOT'].'/photos/upload.php');
      break;
    case "users":
      require($_SERVER['DOCUMENT_ROOT'].'/users/users.php');
      break;
    default:
      require($_SERVER['DOCUMENT_ROOT'].'/404.php');
  }
} else { ?>
          <div class="ctext8">
            Woo, new site!<br />
            If you're lost, try the links on the left, or scroll down to view
            a list of site features. If something isn't displaying correctly on your browser, or 
            you find any other problems with the site, please let Paul know with the Bug Report link 
            on the left. Thanks!
          </div>
          <br /><br />
          <div class="center">
            <img src="/images/band_photo.jpg" />
          </div>
          <br />
          <div style="width:600px; margin:0 auto">
            Current main features of the site:
            <ul>
              <li>
                <span class="emph">Accounts/profiles:</span>
                You can be registered by e-mail, log in, and edit your profile
              </li>
              <li>
                <span class="emph">Events:</span>
                Execs can create events for all to see
              </li>
              <li>
                <span class="emph">Event Responses:</span>
                You can let us know which events you can attend, and see who's attending each event
              </li>
              <li>
                <span class="emph">Member List:</span>
                See who's in the band and find out a bit about them
              </li>
              <li>
                <span class="emph">E-mail Notifications:</span>
                New members get a registration e-mail, and all members get a notification e-mail when an 
                event needs responses
              </li>
            </ul>
            <br />
            Next things on Paul's list of stuff to do
            <ul>
              <li>Photos page (admin can upload .zip file of appropriately-sized .jpg's and a new album 
              gets created. Admins can delete albums also.)</li>
              <li>Limited surveys (execs can create small surveys, members get an e-mail reminder and 
              can log in to fill them out similarly to event responses)</li>
              <li>Stats page (view number of on-campus band members and whatever other band stats
              I can come up with)</li>
              <li>Admin control panel (buttons to delete old users, delete all events, etc)</li>
            </ul>
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
