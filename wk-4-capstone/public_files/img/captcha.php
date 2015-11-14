<?php
include '../../lib/functions.php';

// must start or continue session and save CAPTCHA string in $_SESSION for it
// to be available to other requests
if (!isset($_SESSION))
{
    session_start();
    header('Cache-control: private');
}

// create a 65x20 pixel image
$width = 65;
$height = 20;
$image = imagecreate(65, 20);

// fill the image background color
$bg_color = imagecolorallocate($image, 0x33, 0x66, 0xFF);
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// fetch random text
$text = random_text(5);

// determine x and y coordinates for centering text
$font = 5;
$x = imagesx($image) / 2 - strlen($text) * imagefontwidth($font) / 2;
$y = imagesy($image) / 2 - imagefontheight($font) / 2;

// write text on image
$fg_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
imagestring($image, $font, $x, $y, $text, $fg_color);

// save the CAPTCHA string for later comparison
$_SESSION['captcha'] = $text;

// output the image
header('Content-type: image/png');
imagepng($image);

imagedestroy($image);
?>
