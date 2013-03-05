<?php

/*
 *  albums/albums-functions.php
 *
 *  Functions for dealing with photo album directories and images.
 *
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

// rm_recursive(): Recursively deletes everything within a directory,
//   optionally deleting the directory itself if deldir is TRUE.
function rm_recursive($directory, $deldir=FALSE) {
  // if the path has a slash at the end we remove it here
  if(substr($directory,-1) == '/') {
    $directory = substr($directory,0,-1);
  }

  // if the path is not valid or is not a directory ...
  if(!file_exists($directory) || !is_dir($directory)) {
    // ... we return false and exit the function
    return FALSE;

  // ... if the path is not readable
  } elseif(!is_readable($directory)) {
    // ... we return false and exit the function
    return FALSE;

  // ... else if the path is readable
  } else {
    // we open the directory
    $handle = opendir($directory);

    // and scan through the items inside
    while (FALSE !== ($item = readdir($handle))) {
      // if the filepointer is not the current directory
      // or the parent directory
      if($item != '.' && $item != '..') {
        // we build the new path to delete
        $path = $directory.'/'.$item;

        // if the new path is a directory
        if(is_dir($path)) {
          // we call this function with the new path
          rm_recursive($path);

        // if the new path is a file
        } else {
          // we remove the file
          unlink($path);
        }
      }
    }
    // close the directory
    closedir($handle);

    if($deldir == TRUE) {
      // try to delete the now empty directory
      if(!rmdir($directory)) {
        // return false if not possible
        return FALSE;
      }
    }

    // return success
    return TRUE;
  }
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
