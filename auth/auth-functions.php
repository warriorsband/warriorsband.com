<?php

/*
 *  auth-functions.php
 *
 *  Contains functions used to authenticate users and make decisions based on 
 *  their credentials.
 */

function hash_password($input)
{
  $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
  $hash = hash("sha256", $salt . $input); 
  $final = $salt . $hash; 
  return $final;
}

function sanitize($data){
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

function logged_in() {
  if (isset($_SESSION['logged_in'])) {
    return $_SESSION['logged_in'];
  } else { return FALSE; }
}

//Functions for authentication
function valid_password($password, $correctpassword) {
  $salt = substr($correctpassword, 0, 64);
  $correcthash = substr($correctpassword, 64, 64);
  $userhash = hash("sha256", $salt . $password);
  return ($userhash == $correcthash);
}
function valid_email($email)
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
function ensure_same_user($user_id) {
  if ((isset($_SESSION['user_id'])) && ($_SESSION['user_id'] != $user_id)) {
    echo "Cannot edit this field for another user.";
    exit();
  }
}
function ensure_minimum_type($user_type) {
  if ((isset($_SESSION['user_type'])) && ($_SESSION['user_type'] < $user_type)) {
    echo "Insufficient privileges to view this page / perform this action.";
    exit();
  }
}

//Functions which compare the requester against another user
function is_same_user($user_id) {
  if (isset($_SESSION['user_id'])) {
    return ($_SESSION['user_id'] == $user_id);
  } else { return FALSE; }
}
function user_type_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] == $user_type);
  } else { return FALSE; }
}
function user_type_greater_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] >= $user_type);
  } else { return FALSE; }
}
function user_type_less_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] <= $user_type);
  } else { return FALSE; }
}

//Functions which determine whether the requesting user can view
//or can view/edit a given profile element.
function profile_viewable($user_id, $user_type) {
  return TRUE;
}
function profile_editable($user_id, $user_type) {
  if ((isset($_SESSION['user_id'])) && (isset($_SESSION['user_type'])) &&
  (($_SESSION['user_id'] == $user_id) || ($_SESSION['user_type'] >= 2))) {
    return TRUE;
  } else { return FALSE; }
}
function email_viewable($user_id, $user_type) {
  if ((isset($_SESSION['user_id'])) && (isset($_SESSION['user_type'])) &&
    (($_SESSION['user_id'] == $user_id) || ($_SESSION['user_type'] >= 2))) {
    return TRUE;
  } else { return FALSE; }
}
function email_editable($user_id, $user_type) {
  if (isset($_SESSION['user_type']) &&
    ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function password_editable($user_id, $user_type) {
  if ((isset($_SESSION['user_id'])) &&
    ($_SESSION['user_id'] == $user_id)) {
    return TRUE;
  } else { return FALSE; }
}
function first_name_viewable($user_id, $user_type) {
  return TRUE;
}
function first_name_editable($user_id, $user_type) {
  if ((isset($_SESSION['user_type'])) &&
    ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function last_name_viewable($user_id, $user_type) {
  return TRUE;
}
function last_name_editable($user_id, $user_type) {
  if ((isset($_SESSION['user_type'])) &&
    ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function user_type_viewable($user_id, $user_type) {
  if ((isset($_SESSION['user_type'])) &&
    ($_SESSION['user_type'] >= 2)) {
    return TRUE;
  } else { return FALSE; }
}
function user_type_editable($user_id, $user_type) {
  if ((isset($_SESSION['user_type'])) &&
    ($_SESSION['user_type'] >= 3)) {
    return TRUE;
  } else { return FALSE; }
}
function account_deletable($user_id, $user_type) {
  if ((isset($_SESSION['user_id'])) && (isset($_SESSION['user_type'])) &&
    ($_SESSION['user_id'] != $user_id) && ($_SESSION['user_type'] >= 2)
    && ($_SESSION['user_type'] > $user_type)) {
    return TRUE;
  } else { return FALSE; }
}
?>
