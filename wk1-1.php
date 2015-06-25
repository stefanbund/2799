<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Embedding PHP inside your HTML</title>
<style type="text/css">
    body {
        background-color:#fff;
    color:#000;
        font-family:"Lucida Sans Unicode", "Lucida Grande", sans-serif;
    }

    h1{
        font-family:Tahoma, Geneva, sans-serif;
    }

    #footer{
    font-size:85%;
    }
</style></head>

<body>
<h1>Embedded PHP code</h1>
<p> Let's research what happens when you embed PHP directly into a page</p>

<div id="footer">
<p>&copy;

<?php
    $startYear = 2012;
    
    $thisYear = date('Y');
    
    if($startYear == $thisYear)
    {
        echo $startYear;
    }
    else
    {
        echo "{$startYear} - {$thisYear}";
    }
    ?> Capstone, LLC</p>
</div>
</body></html>







