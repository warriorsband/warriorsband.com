<?php

/*
 *  template.php
 *
 *  This is a template for creating a new page.
 *  The header.php and footer.php contain the logo, banner, navigation menu,
 *  pretty much everything. You can just stick whatever content you like 
 *  in between.
 *
 *  Important note: This file is not readable on the website!
 *  If you're copying it for a new page, be sure to do a `chmod o+r` on it 
 *  so that it can be viewed.
 */

require($_SERVER['DOCUMENT_ROOT'].'/header.php');
?>

<h2>Insert Title</h2>
Insert content

<?php
require($_SERVER['DOCUMENT_ROOT'].'/footer.php');
?>
