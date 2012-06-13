<?php

/*
 *  header.php
 *
 *  The standard start of a page: Contains the banner at the top of the page
 *  and the navigation bar. It's a separate file since it's the same across all pages, 
 *  so if, say, a link needs to be added to the sidebar, it only has to be done once.
 *
 *  Note that if you include header.php in a page, you need to include footer.php 
 *  sometime later so things work correctly.
 */

if (!isset($_SESSION)) session_start(); 
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Warriors Band</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
  </head>

  <body>
    <div id="header">
      <div id="header_text" class="center">
        <img style="vertical-align:middle" src="/images/uw_crest.png" />
        <span id="header1">University of Waterloo</span><br />
        <span id="header2">WARRIORS BAND</span><br />
        <span id="header3">"One of the Bands in Canada"</span>
      </div>
      <a href="/index.php"><img id=logo src="/images/logo_small.png" alt="Warriors Band" /></a>
    </div>
    <hr /><br />
    <table class="contenttable">
      <tr class="contenttr">
        <td id="navigation" class="contenttd">
          <table class="navigation">
            <tr><th class="side">Navigation</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/index.php">Home</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/about.php">About</a>
            </td></tr>
<?php if (logged_in()) { ?>
            <tr><th class="side">Member links</th></tr>
            <tr <?php echo row_color(TRUE) ?> ><td>
              <a href="/users/users.php">Member list</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/users/profile.php">View your profile</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/auth/logout.php">Logout</a>
            </td></tr>
<?php if (user_type_greater_eq(2)) { ?>
            <tr><th class="side">Admin links</th></tr>
            <tr <?php echo row_color(TRUE) ?> ><td>
              <a href="/users/register.php">Register new user</a>
            </td></tr>
<?php } } else { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/auth/login.php">Login</a>
            </td></tr>
<?php } ?>
          </table>
        </td>
        <td id="content" class="contenttd">
          <div class="center"><?php print_msg(); ?></div>
