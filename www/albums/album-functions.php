<?php

/*
 *  albums/albums-functions.php
 *
 *  Functions for dealing with photo album directories and images.
 *
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

// rm_all_files(): Removes all the files in a directory (doesn't handle 
//   directories)
function rm_all_files($dir) {
  foreach (scandir($dir) as $item) {
    if ($item == '.' || $item == '..') continue;
    unlink($dir . "/" . $item);
  }
}

// rm_all_dir(): Removes all the files in a directory and then removes the
//   directory itself.
function rm_all_dir($dir) {
  rm_all_files($dir);
  rmdir($dir);
}

// rm_album_dir(): Removes an album directory
function rm_album_dir($dir) {
  rm_all_dir($dir . "/images");
  rm_all_dir($dir . "/temp");
  rm_all_dir($dir . "/thumbs");
  rmdir($dir);
}

// bound_image_size(): Computes the desired image dimensions to resize an
//   image, maintaining aspect ratio, to be at most a given width/height.
function bound_image_size($width_orig, $height_orig, $max_width, $max_height) {
  $width = $max_width;
  $height = $max_height;
  $ratio_orig = $width_orig / $height_orig;
  if ($width/$height > $ratio_orig) {
    $width = $height*$ratio_orig;
  } else {
    $height = $width/$ratio_orig;
  }
  return array($width, $height);
}

?>
