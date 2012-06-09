<?php
session_start(); 

//require user configuration and database connection parameters
require('config.php');

//Pre-define validation
if (!isset($_SESSION['logged_in'])) {
  $_SESSION['logged_in'] = FALSE;
}
$validationresults=FALSE;
$registered=FALSE;

//Check if the form is submitted
if ((isset($_POST["password"])) && (isset($_POST["username"]))) {

  //Username and password has been submitted by the user
  //Receive and sanitize the submitted information
  function sanitize($data){
    $data=trim($data);
    $data=htmlspecialchars($data);
    $data=mysql_real_escape_string($data);
    return $data;
  }
  $user=sanitize($_POST["username"]);
  $pass=sanitize($_POST["password"]);

  //validate username
  if ($fetch = mysql_fetch_array( mysql_query("SELECT `username` FROM `authentication` WHERE `username`='$user'"))) {

    //Username is in database; user is registered
    $registered=TRUE;

    //Get correct hashed password based on given username stored in MySQL database
    $result = mysql_query("SELECT `password` FROM `authentication` WHERE `username`='$user'");
    $row = mysql_fetch_array($result);
    $correctpassword = $row['password'];
    $salt = substr($correctpassword, 0, 64);
    $correcthash = substr($correctpassword, 64, 64);
    $userhash = hash("sha256", $salt . $pass);

    if (($userhash == $correcthash) && ($registered==TRUE)) {
      //user login validation succeeds
      $validationresults=TRUE;

      //Regenerate session id prior to setting any session variable
      //to mitigate session fixation attacks
      session_regenerate_id();

      //Set logged_in to TRUE as well as start activity time
      $_SESSION['logged_in'] = TRUE;
      $_SESSION['LAST_ACTIVITY'] = time(); 
    }
  }

} 

if (!$_SESSION['logged_in']): 

?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
      .invalid {
        border: 1px solid #000000;
        background: #FF00FF;
      }
    </style>
  </head>
  <body >
    <h2>Restricted Access</h2>
    <br />
    Hi! This private website is restricted to public access. Please enter username and password to proceed.
    <br /><br />
    <!-- START OF LOGIN FORM -->
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">

      Username:  <input type="text" class="<?php if ($validationresults==FALSE) echo "invalid"; ?>" id="username" name="username">
      Password: <input name="password" type="password" class="<?php if ($validationresults==FALSE) echo "invalid"; ?>" id="password" >
      <br /><br />
      <?php if ($validationresults==FALSE) echo '<font color="red">Please enter valid username and password.</font>'; ?><br />
      <input type="submit" value="Login">
    </form>
    <!-- END OF LOGIN FORM -->
    <br />
    <br />
    If you are not registered. You can register by clicking <a href="register.php">here</a>.
  </body>
</html>
<?php
  exit();
endif;
?>
