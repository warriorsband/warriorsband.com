<?php

/*
 *  albums/deletealbum-exec.php
 *
 *  Allows an authenticated user with sufficient permissions to delete a photo
 *  album.
 *  Accepts the following via POST:
 *
 *    confirm: "true" if the deletion has been confirmed and should be done
 *    album_id: ID of the album to delete
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/albums/album-functions.php');

// An album ID is required in order to change settings. If none is provided, 
// show an error and exit.
if (!isset($_POST['album_id']) || $_POST['album_id'] < 0) {
  error_and_exit("No album ID provided, or invalid album ID");
}
$album_id = intval($_POST['album_id']);

//If the confirm flag is not set, refer back to the album list with a confirm message
if (!isset($_POST['confirm']) || $_POST['confirm'] != "true") {
  header("Location: $domain?page=albumlist&msg=confirmdelete");
  exit();
}

// Delete the album info row in the database
$mysqli->query(
  "DELETE FROM `photo_albums` " .
  "WHERE `album_id`='$album_id'");
handle_sql_error($mysqli);

// Delete the album from disk
echo "starting delete"; //DEBUG
rm_recursive($photo_album_abs_path . "/" . $album_id, TRUE);
echo "ending delete"; //DEBUG

// Success! Redirect to the settings page with the appropriate code.
header("Location: $domain?page=albumlist&msg=albumdeletesuccess");
exit();

?>
