<?php 

/*
 *  display.php
 *
 *  A collection of functions/strings which print messages, help format displayed content,
 *  print errors, or define the printing/displaying of data types
 */

//Print a message if one is requested
function print_msg() {
  if (isset($_GET['msg'])) {
    echo '<div class="msg">';
    switch ($_GET['msg']) {
      //Form submission error messages
      case "bademail":
        echo "Error: Invalid e-mail address.";
        break;
      case "duplicateemail":
        echo "Error: The e-mail address provided is already in use.";
        break;
      case "bademailpass":
        echo "Invalid e-mail address or password.";
        break;
      case "badpass":
        echo "Error: Invalid password.";
        break;
      case "passmismatch":
        echo "Error: The new passwords provided do not match.";
        break;
      case "passconstraints":
        echo "Error: Password must be between 6 and 64 characters long";
        break;
      case "emptyname":
        echo "Error: First and last name cannot be empty.";
        break;
      case "nametoolong":
        echo "Error: First and last name must be less than 255 characters.";
        break;
      case "nonalphaname":
        echo "Error: First and last name must be letters and dashes only.";
        break;
      case "selfdowngrade":
        echo "Error: Cannot decrease your own user type. If this is really ";
        echo "your plan, delete the account and create a new one.";
        break;
      case "newuserupgrade":
        echo "Error: Cannot create a new user with equivalent or greater user type to your own.";
        break;
      case "maxlogins":
        echo "Maximum number of login attempts exceeded. Try again in 30 minutes.";
        break;
      case "logoutfail":
        echo "You must be logged in before you can log out.";
        break;
      case "badtitle":
        echo "Event titles must be between 4 and 255 characters long.";
        break;
      case "baddate":
        echo "The date provided must be valid.";
        break;
      case "badtime":
        echo "The time provided must be valid.";
        break;
      case "badlocation":
        echo "Event locations must be at most 255 characters long.";
        break;
      case "commentrequired":
        echo "If you answer \"Maybe\", you must provide a comment to elaborate (10 chars minimum).";
        break;
      case "bugreportfail":
        echo "There was a problem sending the bug report.";
        break;
      //Form submission success messages
      case "profileupdatesuccess":
        echo "Profile updated successfully.";
        break;
      case "registrationsuccess":
        echo "Registration e-mail sent. New user registered successfully!";
        break;
      case "registrationfail":
        echo "Error sending registration email.";
        break;
      case "userdeletesuccess":
        echo "Account deleted successfully.";
        break;
      case "eventdeletesuccess":
        echo "Event deleted successfully.";
        break;
      case "logoutsuccess":
        echo "Logged out successfully.";
        break;
      case "eventupdatesuccess":
        echo "Event updated successfully.";
        break;
      case "eventcreatesuccess":
        echo "Event created successfully.";
        break;
      case "responserecorded":
        echo "Attendance response recorded successfully.";
        break;
      case "bugreportsuccess":
        echo "Comment / Bug Report sent successfully.";
        break;
      //Confirmation messages
      case "confirmdelete":
        echo "If you are CERTAIN this should be deleted, click the delete ";
        echo "button again and it will be PERMANENTLY deleted.";
        break;
      default:
        echo "Error: Unknown msg code.";
        break;
    }
    echo '</div>';
  } else {
    echo '<br />';
  }
}

//Print the reminder for responding to events
function print_eventreminder() {
  if (isset($_SESSION['responses']) && $_SESSION['responses'] > 0) {
    echo '<div class="msg">';
    echo 'There are are ' . $_SESSION['responses'] . ' event(s) that you ';
    echo 'haven\'t responded to yet! Click ';
    echo '<a href="?page=events">here</a> to respond.';
    echo '</div>';
  }
}
//Converts user_type to a printable string
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
//Convert response code to a printable string
function response_to_str($response) {
  if ($response == 1) {
    return "Yes";
  } elseif ($response == 2) {
    return "No";
  } elseif ($response == 3) {
    return "Maybe";
  } else {
    return "";
  }
}

//Converts an event status code to a printable string
function event_status_to_str($status, $long = FALSE) {
  if ($status == 1) {
    $output = "Active";
    if ($long) {
      $output .= " (open to attendance responses from members)";
    }
  } elseif ($status == 2) {
    $output = "Inactive";
    if ($long) {
      $output .= " (not open to attendance responses from members)";
    }
  } else {
    $output = "";
  }
  return $output;
}

//Functions which print the content of the registration e-mail message
function registration_email_subject() {
  return "Warriors Band website registration";
}
function registration_email_message($temp_password, $submitter_name, $submitter_comment) {
  $message = "One of your band execs, $submitter_name, has registered your e-mail address\n" .
  "for a warriorsband.com account. To use your\n" .
  "account:\n\n" .
  "1. Visit http://warriorsband.dyndns.org/?page=profile\n" .
  "2. Log in with your e-mail address and the following temporary password:\n\n" .
  "    $temp_password\n\n" .
  "3. On the page that appears, change your password (6 characters minimum)\n\n" .
  "Welcome to the band!\n\n\n";
  $comment = "";
  if (!empty($submitter_comment)) {
    $comment = "--------------------\n" .
      "$submitter_name's comment:\n$submitter_comment\n" .
      "---------------------\n\n\n";
  }
  $signature = "This is an automated message - please do not reply.\n\nWarriors Band";
  return $message . $comment . $signature;
}
function registration_email_from() {
  return "registration_noreply@warriorsband.com";
}

//Outputs 'class="alt"' on even-numbered rows, so that it can be used to define the 
//class of rows and ".alt" can be given a different colour in the css style
function row_color($reset = FALSE) {
  static $row_count = 0;
  if ($reset == TRUE) {
    $row_count = 0;
    return;
  }
  if ($odd = ++$row_count % 2) return '';
  else return 'class="alt"';
}

function selected($item, $selecteditem) {
  if (isset($selecteditem) && ($item == $selecteditem)) {
    echo 'selected="selected"';
  }
}
function checked($item, $checkeditem) {
  if (isset($checkeditem) && ($item == $checkeditem)) {
    echo 'checked="checked"';
  }
}
