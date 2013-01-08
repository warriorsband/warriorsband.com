<?php

/*
 *  photos/upload-exec.php
 *
 *  Takes a ZIP file containing photos, and extracts them into a folder in the
 *  images directory to create an album viewable on the website. Also tries
 *  desperately to do as much validation as possible, since there is a ton
 *  that can go wrong trying to do something like this.
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

// sanitize_album_name(): Returns the given album name with all occurrences of
//   non-printable or non-ASCII characters removed
function sanitize_album_name($filename) {
  return preg_replace("/[^\x20-\x7E]+/", "", $filename);
}

// sanitize_filename(): Returns the given filename with "dangerous" characters
//   (non-alphanumeric) replaced by underscores.
function sanitize_filename($filename) {
  return preg_replace("/[^a-zA-Z0-9_]/", "_", $filename);
}

// rm_album_dir(): Removes all the files in a directory and then removes the
//   directory itself.
function rm_album_dir($dir) {
  foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
            unlink($dir . "/" . $item);
  }
  rmdir($dir);
}

// Make sure the user is logged in, has sufficient permissions, and that the
// submitted name/file are appropriate
if (!logged_in()) {
  error_and_exit("Not logged in");
}
if (!auth_upload_photos()) {
  error_and_exit("Insufficient permissions");
}
if ($_FILES['file']['error'] > 0) {
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
}
$file_extension = end(explode(".", $_FILES["file"]["name"]));
if ($_FILES['file']['type'] != "application/zip" || $file_extension != "zip") {
  header("Location: $domain?page=uploadphotos&msg=ziperror");
}
if ($_FILES['file']['size'] > 20000) {
  header("Location: $domain?page=uploadphotos&msg=albumsizeerror");
}

// Sanitize user inputs
$album_name = sanitize_album_name($_POST['album_name']);
$album_dir_name = sanitize_filename($album_name);
$album_dir = $photo_album_dir . '/' . $album_dir_name;

// Check if an album with the given name exists already
if (is_dir($album_dir) || file_exists($album_dir)) {
  header("Location: $domain?page=uploadphotos&msg=albumexistserror");
}

// The album does not already exist, so create a directory for it. Remember to
// delete this directory during cleanup if something fails later on!
if (!mkdir($album_dir)) {
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
}

// Unzip the contents of the zip file into the created directory
$zip = new ZipArchive;
if (!$zip->open($_FILES["file"]["tmp_name"])) {
  rmdir($album_dir);
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
}
if (!$zip->extractTo($album_dir)) {
  rm_album_dir($album_dir);
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
}
$zip->close();

// Redirect to the album page, indicating that the upload was successful
// TODO: make this redirect to the actual album page, not the upload page
header("Location: $domain?page=uploadphotos&msg=photouploadsuccess");
?>
