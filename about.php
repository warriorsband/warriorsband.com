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

<h2>About the Band</h2>

<p>The Warriors Band is a student-run pep band for the University of Waterloo. Main events where the band 
creates lots of noise include football, basketball, hockey, volleyball, soccer and even the odd rugby 
games. The band has been known to attend other functions such as opening ceremonies, city parades, and 
even weddings.<p>

<p>Join the fun! There are no auditions, and instruments are provided (as long as we've got one). No 
practice or event is mandatory, so you can go home or get that hugely important assignment done. Of 
course the more stuff you come to the more fun you'll have. For practice and every other event, we meet 
first in the Band Office (PAC 1001). After that, we'll be at the event, or just playing outside, near 
the SLC. For more information contact 
<a href="mailto:warriorsband@gmail.com">The Band</a> or just show up at a practice on Thursday's at 
5:30 pm (PAC 1001).</p>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/footer.php');
?>
