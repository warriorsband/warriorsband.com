<?php

/*
 *  config.php
 *
 *  General settings for the website
 */

//Site domain
$domain= "http://warriorsband.dyndns.org/";

//Define length of salt,minimum=10, maximum=35
$length_salt=15;

//Define session timeout in seconds
//minimum 60 (for one minute)
$sessiontimeout=1800;

//Maximum number of login attempts before being locked out
$max_login_attempts=3;

//Cooldown time (in seconds) after being locked out before a correct
//login will be accepted
$login_cooldown=1800;

//If set to true, error messages for unexpected errors (like SQL errors, or improper
//values posted by a form) will be more verbose and descriptive. Useful for debugging,
//but for security reasons it might be good to disable this if things are running smoothly.
$verbose_errors=TRUE;

//E-mail configuration settings
$email_host = "ssl://smtp.gmail.com";
$email_port = "465";
$email_username = "ironmaiden1158@gmail.com";
$email_password = "gma1337acusphail";

//Maximum sizes of various user profile fields and database types
$maxsize_term = 17;
$maxsize_instrument = 15;
?>
