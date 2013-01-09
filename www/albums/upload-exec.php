<?php

/*
 *  albums/upload-exec.php
 *
 *  Takes a ZIP file containing photos, and extracts them into a folder in the
 *  images directory to create an album viewable on the website. Also tries
 *  desperately to do as much validation as possible, since there is a ton
 *  that can go wrong trying to do something like this.
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/albums/album-functions.php');

// Functions which undo various stages of the photo upload process. Used if
// we are partway through photo upload and something fails, requiring all 
// preceding steps to be undone.

function clear_temp_dir() {
  rm_all_files($photo_temp_dir);
}
function undo_db_entry($mysqli, $title) {
  $mysqli->query(
    "DELETE FROM `photo_albums` " .
    "WHERE `title`='$title'" );
  handle_sql_error($mysqli);
}

//


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
  exit();
}
$file_extension = end(explode(".", $_FILES["file"]["name"]));
if ($_FILES['file']['type'] != "application/zip" || $file_extension != "zip") {
  header("Location: $domain?page=uploadphotos&msg=ziperror");
  exit();
}
if ($_FILES['file']['size'] > 20000000) {
  header("Location: $domain?page=uploadphotos&msg=albumsizeerror");
  exit();
}

// Sanitize user inputs
$album_name = sanitize_album_name($_POST['album_name']);

// Exit if an album with the same name exists already
$album_row = $mysqli->query(
  "SELECT COUNT(*) " .
  "FROM `photo_albums` " .
  "WHERE `title`='$album_name'"
  )->fetch_assoc();
handle_sql_error($mysqli);
if ($album_row[0] > 0) {
  header("Location: $domain?page=uploadphotos&msg=albumexistserror");
  exit();
}

// Add the album name to to the albums table, so that we obtain an album ID
// (we will create a directory for the album using that ID as the directory
// name)
$mysqli->query(
  "INSERT INTO `photo_albums` " .
  "(`title`,`date_uploaded`) " .
  "VALUES ('$album_name', NOW())" );
handle_sql_error($mysqli);

// Get the album ID and create a directory for the album using the ID
$id_row = $mysqli->query(
  "SELECT `album_id` FROM `photo_albums` " .
  "WHERE `title`='$album_name'")->fetch_row();
handle_sql_error();
$album_id = $id_row[0];
$album_dir = $photo_album_dir . "/" . $album_id;
$album_images_dir = $album_dir . "/images";
$album_temp_dir = $album_dir . "/temp";
$album_thumbs_dir = $album_dir . "/thumbs";
$old = umask(0); //required to set directory permissions properly
if ( !mkdir($album_dir, 0775) ||
     !mkdir($album_images_dir, 0775) ||
     !mkdir($album_temp_dir, 0775) ||
     !mkdir($album_thumbs_dir, 0775) ) {
  undo_db_entry($mysqli, $album_name);
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
  exit();
}
umask($old); //reset umask

// Unzip the contents of the zip file into the temp directory
$zip = new ZipArchive;
if ( !$zip->open($_FILES["file"]["tmp_name"]) ||
     !$zip->extractTo($album_temp_dir) ) {
  rm_album_dir($album_dir);
  undo_db_entry($mysqli, $album_name);
  header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
  exit();
}
$zip->close();

// For each image in the temp directory, create resized copies in images/ and
// thumbs/. If a non-JPEG file is encountered, exit.
foreach (scandir($album_temp_dir) as $item) {
  if ($item == '.' || $item == '..') continue;
  list($width, $height, $image_type) = getimagesize($album_temp_dir . "/" . $item);
  if ($image_type != IMAGETYPE_JPEG) {
    rm_album_dir($album_dir);
    undo_db_entry($mysqli, $album_name);
    header("Location: $domain?page=uploadphotos&msg=unsupportedimageformaterror");
    exit();
  }
  list($bounded_width, $bounded_height) = bound_image_size(
    $width, $height, $photo_max_width, $photo_max_height);
  list($thumb_width, $thumb_height) = bound_image_size(
    $width, $height, $photo_thumb_width, $photo_thumb_height);
  $image_bounded = imagecreatetruecolor($bounded_width, $bounded_height);
  $image_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
  $image = imagecreatefromjpeg($album_temp_dir . "/" . $item);
  if ( !imagecopyresampled( $image_bounded, $image, 0, 0, 0, 0,
         $bounded_width, $bounded_height, $width, $height ) ||
       !imagecopyresampled( $image_thumb, $image, 0, 0, 0, 0,
         $thumb_width, $thumb_height, $width, $height ) ||
       !imagejpeg($image_bounded, $album_images_dir . "/" . $item, 100) ||
       !imagejpeg($image_thumb, $album_thumbs_dir . "/" . $item, 100) ) {
    rm_album_dir($album_dir);
    undo_db_entry($mysqli, $album_name);
    header("Location: $domain?page=uploadphotos&msg=fileuploaderror");
    exit();
  }
}
echo "DEBUG: image resize success.";

// Success! Remove the temp directory.
rm_all_dir($album_temp_dir);
echo "DEBUG: temp dir removed.";

// Redirect to the album page, indicating that the upload was successful
// TODO: make this redirect to the actual album page, not the upload page
header("Location: $domain?page=uploadphotos&msg=photouploadsuccess");
exit();

?>
