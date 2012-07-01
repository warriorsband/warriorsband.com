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
        echo "Error: Maximum number of login attempts exceeded. Try again in 30 minutes.";
        break;
      case "logoutfail":
        echo "Error: You must be logged in before you can log out.";
        break;
      case "badtitle":
        echo "Error: Event titles must be between 4 and 255 characters long.";
        break;
      case "baddate":
        echo "Error: The date provided must be valid.";
        break;
      case "pastdate":
        echo "Error: Can't create an event in the past.";
        break;
      case "badtime":
        echo "Error: The time provided must be valid.";
        break;
      case "badlocation":
        echo "Error: Event locations must be at most 255 characters long.";
        break;
      case "commentrequired":
        echo "Error: If you answer \"Maybe\", you must provide a comment to elaborate (10 chars minimum).";
        break;
      case "bugreportfail":
        echo "Error: There was a problem sending the bug report.";
        break;
      case "jointhebandfail":
        echo "Error: There was a problem sending the form.";
        break;
      case "emptymessage":
        echo "Error: Message cannot be empty.";
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
      case "jointhebandsuccess":
        echo "Form sent successfully.";
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


//Functions for converting database codes to printable strings

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
//Convert a term code to a printable string
function term_to_str($term) {
  if ($term == 1) {
    return "1A";
  } elseif ($term == 2) {
    return "1A Co-op";
  } elseif ($term == 3) {
    return "1B";
  } elseif ($term == 4) {
    return "1B Co-op";
  } elseif ($term == 5) {
    return "2A";
  } elseif ($term == 6) {
    return "2A Co-op";
  } elseif ($term == 7) {
    return "2B";
  } elseif ($term == 8) {
    return "2B Co-op";
  } elseif ($term == 9) {
    return "3A";
  } elseif ($term == 10) {
    return "3A Co-op";
  } elseif ($term == 11) {
    return "3B";
  } elseif ($term == 12) {
    return "3B Co-op";
  } elseif ($term == 13) {
    return "4A";
  } elseif ($term == 14) {
    return "4A Co-op";
  } elseif ($term == 15) {
    return "4B";
  } elseif ($term == 16) {
    return "4B Co-op";
  } elseif ($term == 17) {
    return "Grad";
  } else {
    return "";
  }
}
//Convert an instrument code to a printable string
function instrument_to_str($instrument) {
  if ($instrument == 1) {
    return "Piccolo";
  } elseif ($instrument == 2) {
    return "Flute";
  } elseif ($instrument == 3) {
    return "Clarinet";
  } elseif ($instrument == 4) {
    return "Alto Saxophone";
  } elseif ($instrument == 5) {
    return "Tenor Saxophone";
  } elseif ($instrument == 6) {
    return "Baritone Saxophone";
  } elseif ($instrument == 7) {
    return "French Horn";
  } elseif ($instrument == 8) {
    return "Trumpet";
  } elseif ($instrument == 9) {
    return "Baritone";
  } elseif ($instrument == 10) {
    return "Trombone";
  } elseif ($instrument == 11) {
    return "Soussaphone";
  } elseif ($instrument == 12) {
    return "Bass Drum";
  } elseif ($instrument == 13) {
    return "Snare Drum";
  } elseif ($instrument == 14) {
    return "Auxiliary Percussion";
  } elseif ($instrument == 15) {
    return "Other";
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

//Functions which print the content of email messages
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
function jointheband_email_subject($name) {
  return "\"Join the Band\" website message from $name";
}
function jointheband_email_message($name, $email, $msg) {
  $body = "$name ($email) has filled out the \"Join the Band\" comment form on the Warriors Band " .
    "website. Here's the content of their message:\n\n" .
    "--------------------\n$msg\n--------------------\n\n\n" .
    "This is an automated message.\n\nWarriors Band";
  return $body;
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
