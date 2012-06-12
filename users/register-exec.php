<?php

/*
 *  register-exec.php
 *
 *  Allows an authenticated user with high enough user_type to create new users. 
 *  Creates the new user and sends them an email with their password.
 */

require($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

/**
  Validate an email address.
  Provide email address (raw input)
  Returns true if the email address has the email 
  address format and the domain exists.
 */
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

//pre-define validation parameters

$emailvalidate=FALSE;
$emailnotduplicate=FALSE;
$passwordmatch=FALSE;
$passwordvalidate=FALSE;

//Check if user submitted the desired password and username
if ((isset($_POST["desired_email"]))
  && (isset($_POST["desired_password"]))
  && (isset($_POST["desired_password1"])))  {

  //Username and Password has been submitted by the user
  //Receive and validate the submitted information

  //sanitize user inputs

  function sanitize($data){
    $data=trim($data);
    $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
  }

  $desired_email=sanitize($_POST["desired_email"]);
  $desired_password=sanitize($_POST["desired_password"]);
  $desired_password1=sanitize($_POST["desired_password1"]);

  //validate e-mail address
  if ((!empty($desired_email)) && (validateEmail($desired_email))) {
    $emailvalidate=TRUE;
  }

  if (!($fetch = mysql_fetch_array( mysql_query("SELECT `email` FROM `users` WHERE `email`='$desired_email'")))) {
    //A record exists in the database
    $emailnotduplicate=TRUE;
  }

  //validate password
  if ((!empty($desired_password)) && (strlen($desired_password) >= 6)) {
      $passwordvalidate=TRUE;
    if ($desired_password == $desired_password1) {
      $passwordmatch=TRUE;
    }
  }

  if (($emailvalidate==TRUE)
    && ($emailnotduplicate==TRUE)
    && ($passwordvalidate==TRUE)
    && ($passwordmatch==TRUE)) {
      //The email and password validation succeeds.

      //Hash the password
      //This is very important for security reasons (so that if someone bad gets a hold of the database,
      //they can't read users' passwords)
      function HashPassword($input)
      {
        //Credits: http://crackstation.net/hashing-security.html
        //This is secure hashing the consist of strong hash algorithm sha 256 and using highly random salt
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
        $hash = hash("sha256", $salt . $input); 
        $final = $salt . $hash; 
        return $final;
      }

      $hashedpassword= HashPassword($desired_password);

      //Insert username and the hashed password to MySQL database
      mysql_query("INSERT INTO `users` (`last_name`, `first_name`, `email`, `password`, `user_type`) VALUES ('Last', 'First', '$desired_email', '$hashedpassword', 3)")
        or die(mysql_error());

      //redirect to main page
      header(sprintf("Location: %s", $domain));	
      exit();
    }
}
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Register as a Valid User</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
      .invalid {
        border: 1px solid #000000;
        background: #FF00FF;
      }
    </style>
  </head>
  <body >
    <h2>User registration Form</h2>
    <br />
    Hi! This private website is restricted to public access. If you want to see the content, please register below. You will be redirected to a login page after successful registration.
    <br /><br />
    <!-- Start of registration form -->
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
      Email: <input type="text" class="<?php if (($emailvalidate==FALSE) || ($emailnotduplicate==FALSE))  echo "invalid"; ?>" id="desired_email" name="desired_email"><br /><br />
      Password: (<i>at least 6 characters</i>) <input name="desired_password" type="password" class="<?php if (($passwordmatch==FALSE) || ($passwordvalidate==FALSE)) echo "invalid"; ?>" id="desired_password" ><br /><br />
      Type the password again: <input name="desired_password1" type="password" class="<?php if (($passwordmatch==FALSE) || ($passwordvalidate==FALSE)) echo "invalid"; ?>" id="desired_password1" ><br />
      <br /><br />
      <input type="submit" value="Register">
      <br /><br />
      <a href="index.php">Back to Homepage</a><br />
      <!-- Display validation errors -->
      <?php if ($emailvalidate==FALSE) echo '<font color="red">Your email should be alphanumeric and less than 12 characters.</font>'; ?><br />
      <?php if ($emailnotduplicate==FALSE) echo '<font color="red">Please choose another email, your username is already used.</font>'; ?><br />
      <?php if ($passwordmatch==FALSE) echo '<font color="red">Your password does not match.</font>'; ?><br />
      <?php if ($passwordvalidate==FALSE) echo '<font color="red">Your password should be alphanumeric and greater 8 characters.</font>'; ?><br />
    </form>
    <!-- End of registration form -->
  </body>
</html>
