<?php
require('timeout.php');
?>
This is the Warriors Band homepage.
<br />
<br />
===============<br />
Navigation Menu<br />
===============<br />
<a href="index.php">Homepage</a><br />
<a href="secured.php">Super secret secure page</a><br />
<a href="login.php">Login page</a><br />
<?php if (isset($_SESSION['logged_in'])) { ?><a href="logout.php">Logout</a><br /><?php } ?>
