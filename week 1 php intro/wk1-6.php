<!DOCTYPE HTML>
<html><head><meta charset="utf-8">

<title>Demo while loop</title>
<style type="text/css">
div{
    margin: 7px;
    border: #600 6px ridge;
    border: 1px dotted;
}

div.m{
    margin: 8px;
    border: #677 12px ridge;
    border: 1px solid;
}
</style>

</head>

<body>

<?php
    $i = 1;//set counter value
    
    while($i <= 15)
    {
        if($i == 5 || $i == 7)
        {
            echo '<div class=m> <img src="http://passtharock.com/images/TR.png"></div>';

        }
        echo "<div>here's # $i<br><br><br><br><br><br></div><br>";
        $i++;
    }
    ?>
</body></html>
