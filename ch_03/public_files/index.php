<?php
include '../lib/config.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>Mailing List</title>
 </head>
 <body>
  <h1>Mailing List</h1>
  <p>You can subscribe to the mailing list by sending an email with 
<strong>SUBSCRIBE</strong> in the subject line to <?php echo MANAGE_EMAIL; ?>.
</p>
  <p>Send <strong>HELP</strong> to <?php echo MANAGE_EMAIL; ?> for assistance
in managing your subscription (including unsubscribing).</p>
  <p>List correspondance should be sent to <?php echo LIST_EMAIL; ?>.</p>
 </body>
</html>
