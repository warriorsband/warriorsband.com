<?php

/*
 *  photos/upload.php
 *  
 *  A form which posts to photos/upload-exec.php in order to upload a photo
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
  TODO: add photo upload instructions here
  <br /><br />
</div>
<form action="/photos/upload-exec.php" method="POST" enctype="multipart/form-data">
  <table>
    <tr>
      <th>Album name</th>
      <td><input type="text" name="album_name" maxlength="64" /></td>
    </tr>
    <tr class="alt" >
      <th>ZIP file</th>
      <td><input type="file" name="file" /></td>
    </tr>
    <tr>
      <th></th>
      <td style="text-align:center"><input style="width:150px" type="submit" value="Upload Photo Album" /></td>
    </tr>
  </table>
</form>
