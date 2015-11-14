<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>
<?php
if (!empty($GLOBALS['TEMPLATE']['title']))
{
    echo $GLOBALS['TEMPLATE']['title'];
}
?>
</title>
  <link rel="stylesheet" type="text/css" href="css/styles.css"/>
<?php
if (!empty($GLOBALS['TEMPLATE']['extra_head']))
{
    echo $GLOBALS['TEMPLATE']['extra_head'];
}
?>
 </head>
 <body>
  <div id="header">
<?php
if (!empty($GLOBALS['TEMPLATE']['title']))
{
    echo $GLOBALS['TEMPLATE']['title'];
}
?>
  </div>
  <div id="content">
<?php
if (!empty($GLOBALS['TEMPLATE']['content']))
{
    echo $GLOBALS['TEMPLATE']['content'];
}
?>
  </div>
  <div id="footer">Copyright &copy;<?php echo date('Y'); ?></div>
  </div>
 </body>
</html>
