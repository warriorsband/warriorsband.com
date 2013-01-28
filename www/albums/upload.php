<?php

/*
 *  albums/upload.php
 *  
 *  A form which posts to albums/upload-exec.php in order to upload a photo
 *  album (in a .zip archive) to the website.
 */

require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');

// Ensure that the user is authorized to upload photos
if (!auth_upload_photos()) {
  print_and_exit("You do not have permission to upload photos.");
}
?>

<h1>Upload Photo Album</h1>
<br />
<div class="ctext8">
  To upload photos:
  <ol>
    <li>
      Make sure that all the images you want to build an album out of are
      JPEG images; if not, convert them to JPEGs with an image editor first.
    </li>
    <li>
      The first image (alphabetically speaking) will be the preview image 
      which shows up in the album list, so choose which photo you would like 
      to use as the preview and name it e.g. 0.jpg to make it the first photo.
    </li>
    <li>
      Make a ZIP archive containing all the images. WinZip and WinRAR are 
      examples of programs you can get for free (for Windows) which will do 
      this for you.
    </li>
    <li>
      Ensure that the ZIP archive is less than 20MB in size. If it's over 
      20MB, consider either including less photos or resizing the photos to 
      reduce filesize. (Note that the site will automatically resize images to 
      be at most 1024x768 pixels anyway, so you might as well resize them to 
      this size to make the upload much faster)
    </li>
    <li>
      Select an album name and description in the form below (these will be 
      displayed on the album list), and select the ZIP archive you created.
    </li>
  </ol>
</div>
<br /><br />
<form action="/albums/upload-exec.php" method="POST" enctype="multipart/form-data">
  <table>
    <tr <?php echo row_color() ?> >
      <th>Album name</th>
      <td><input type="text" name="album_name" maxlength="64" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>Description</th>
      <td><textarea name="description" rows="6" cols="80" maxlength="10000"><?php echo $details; ?></textarea></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>ZIP file</th>
      <td><input type="file" name="file" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th></th>
      <td style="text-align:center"><input style="width:150px" type="submit" value="Upload Photo Album" /></td>
    </tr>
  </table>
</form>
