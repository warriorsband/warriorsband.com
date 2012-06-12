<?php

/*
 *  settings-exec.php
 *
 *  Receives a settings form and attempts login validation.
 *  If successful, redirects to the requested URL, or to the homepage if none is provided. 
 *  If not successful, redirects back to the login form.
 */

session_start();
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

function sanitize($data){
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

function validateEmail($email)
{
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex)
  {
    $isValid = false;
  }
  else
  {
    $domain = substr($email, $atIndex+1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64)
    {
      // local part length exceeded
      $isValid = false;
    }
    else if ($domainLen < 1 || $domainLen > 255)
    {
      // domain part length exceeded
      $isValid = false;
    }
    else if ($local[0] == '.' || $local[$localLen-1] == '.')
    {
      // local part starts or ends with '.'
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $local))
    {
      // local part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
    {
      // character not valid in domain part
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $domain))
    {
      // domain part has two consecutive dots
      $isValid = false;
    }
    else if
      (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
      str_replace("\\\\","",$local)))
    {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/',
        str_replace("\\\\","",$local)))
      {
        $isValid = false;
      }
    }
    if ($isValid && !(checkdnsrr($domain,"MX") || 
      checkdnsrr($domain,"A")))
    {
      // domain not found in DNS
      $isValid = false;
    }
  }
  return $isValid;
}

function ensure_minimum_type($type) {
  if ($_SESSION['user_type'] < $type) {
    echo "Your user type does not allow you to change that setting.";
    exit();
  }
}

function ensure_same_user() {
  global $user_id;
  if ($_SESSION['user_id'] != $user_id) {
    echo "You cannot make that modification to another user's page.";
    exit();
  }
}

//Variable to indicate whether a post has been validated and requires an update.
//If this is TRUE, it is assumed that $col_name and $value contain the column name 
//and value of a field to update, and $successcode contains a valid code to send 
//back via GET to profile.php.
$success = FALSE;

//A user ID is required in order to change settings. If none is provided, 
//show an error and exit.
if (!isset($_POST['user_id'])) {
  echo "A used ID must be specified in order for settings to be changed.";
  exit();
}

$user_id = intval(sanitize($_POST['user_id']));

//Get all the user's details from the database; we'll need most of it anyway.
if (!($row = mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `user_id`='".$user_id."'")))) {
  echo "No such user with that user_id.";
  exit();
}

$user_type = intval($row['user_type']);

//If the user is attempting to modify a different user's information who has a 
//higher user type, forbid them and display an error
if (($_SESSION['user_id'] != $user_id) && ($_SESSION['user_type'] < $user_type)) {
  echo "You do not have permission to edit this user's information.";
  exit();
}

//Update email
if (isset($_POST['email'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Sanitize and validate the field
  $email = sanitize($_POST['email']);
  if (!validateEmail($email)) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=bademail");
    exit();
  }

  //Ensure the requested email address is not already in use
  if (!($fetch = mysql_fetch_array( mysql_query("SELECT `email` FROM `users` WHERE `email`='$email'")))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=duplicateemail");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'email';
  $value = $email;
  $successcode = 'email';
  $success = TRUE;
}

//Update password
elseif ((isset($_POST['password'])) && (isset($_POST['newpassword'])) && (isset($_POST['newpassword1']))) {
  //Ensure that the user is allowed to modify this setting
  ensure_same_user();

  //Sanitize and validate the fields
  $password = sanitize($_POST['password']);
  $newpassword = sanitize($_POST['newpassword']);
  $newpassword1 = sanitize($_POST['newpassword1']);

  //Validate current password
  $correctpassword = $row['password'];
  $salt = substr($correctpassword, 0, 64);
  $correcthash = substr($correctpassword, 64, 64);
  $userhash = hash("sha256", $salt . $password);
  if ($userhash != $correcthash) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=badpass");
    exit();
  }

  //Validate new password
  if ((empty($newpassword)) || (strlen($newpassword) < 6) || (strlen($newpassword) > 64)) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=passconstraints");
    exit();
  }
  if ($newpassword != $newpassword1) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=passmismatch");
    exit();
  }

  //Hash the password
  function HashPassword($input)
  {
    $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
    $hash = hash("sha256", $salt . $input); 
    $final = $salt . $hash; 
    return $final;
  }

  //Set the variables used in the update code below
  $col_name = 'password';
  $value = HashPassword($newpassword);
  $successcode = 'password';
  $success = TRUE;
}

//Update first name
elseif (isset($_POST['first_name'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Check if the field is empty
  if (empty($_POST['first_name'])) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=emptyname");
    exit();
  }

  //Sanitize the field
  $first_name = sanitize($_POST['first_name']);
  
  //Check if the field is too long
  if (strlen($first_name) > 255) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=nametoolong");
    exit();
  }

  //If name is not letters and dashes only, exit
  if (!ctype_alpha(str_replace('-','',$first_name))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=nonalphaname");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'first_name';
  $value = $first_name;
  $successcode = 'firstname';
  $success = TRUE;
}

//Update last name
elseif (isset($_POST['last_name'])) {
  //Ensure that the user is allowed to modify this setting
  ensure_minimum_type(2);

  //Check if the field is empty
  if (empty($_POST['last_name'])) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=emptyname");
    exit();
  }

  //Sanitize the field
  $last_name = sanitize($_POST['last_name']);

  //Check if the field is too long
  if (strlen($first_name) > 255) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=nametoolong");
    exit();
  }

  //If name is not letters and dashes only, exit
  if (!ctype_alpha(str_replace('-','',$last_name))) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=nonalphaname");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'last_name';
  $value = $last_name;
  $successcode = 'lastname';
  $success = TRUE;
}

//Update user type. Note that since this is posted via field, we can just
//throw an error and exit if the input is unexpected, since it likely means 
//someone is screwing around. Still have to do the sanitation/validation though.
elseif (isset($_POST['user_type'])) {
  //Only admins can modify user_type
  ensure_minimum_type(3);

  //Sanitize the field
  $new_user_type = intval(sanitize($_POST['user_type']));

  //Make sure the value is in the correct range
  if (($new_user_type < 1) || ($new_user_type > 4)) {
    echo "Invalid user_type.";
    exit();
  }

  //To prevent, say, the last admin from deprivileging themself, 
  //admins cannot be de-adminned. Verify that this is not happening.
  if (($user_type >= 3) && ($new_user_type <= 2)) {
    header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&error=admindowngrade");
    exit();
  }

  //Set the variables used in the update code below
  $col_name = 'user_type';
  $value = $new_user_type;
  $successcode = 'usertype';
  $success = TRUE;
}

//Unrecognized settings field, or no field provided. Show an error.
else {
  echo "Unrecognized setting, or no setting provided.";
  exit();
}

//If successful, do the update
if ($success == TRUE) {
  //Run the update query
  mysql_query("UPDATE `users` SET `".$col_name."`='".$value."' WHERE `user_id`=".$user_id)
    or die(mysql_error());

  //Success! Redirect to the settings page with the appropriate code.
  header("Location: ".$domain."/users/profile.php?user_id=".$user_id."&success=".$successcode);
  exit();
}
//Otherwise an unknown error occurred. Show an error I guess.
else {
  echo "Unknown error occurred.";
  exit();
}
?>
