<?php
header("Content-type: image/png");
error_reporting("0");

// Create the image
$height = $_GET['height'];
$width = $_GET['width'];

//Create image with bg
$top = '692108';
$bot = '381007';
if ($_GET["top"]) {
  $top = $_GET["top"];
}
if ($_GET["bot"]) {
  $bot = $_GET["bot"];
}
 
$top_r = hexdec(substr($top, 0, 2));
$top_g = hexdec(substr($top, 2, 2));
$top_b = hexdec(substr($top, 4, 2));
$bot_r = hexdec(substr($bot, 0, 2));
$bot_g = hexdec(substr($bot, 2, 2));
$bot_b = hexdec(substr($bot, 4, 2));
$img = @imagecreate($width, $height);
 
for($y=0;$y<$height;$y++) {
  for($x=0;$x<$width;$x++) {
    if ($top_r == $bot_r) {
      $new_r = $top_r;
    }
    $difference = $top_r - $bot_r;
    $new_r = $top_r - intval(($difference / $height) * $y); 
    if ($top_g == $bot_g) {
      $new_g = $top_g;
    }
    $difference = $top_g - $bot_g;
    $new_g = $top_g - intval(($difference / $height) * $y);         
    if ($top_b == $bot_b) {
      $new_b = $top_b;
    }
    $difference = $top_b - $bot_b;
    $new_b = $top_b - intval(($difference / $height) * $y);
    $row_color = imagecolorresolve($img, $new_r, $new_g, $new_b);
    imagesetpixel($img, $x, $y, $row_color);
  }
}


imagepng($img);
imagedestroy($img);

?>