<?php
require "less.php";
$inputFile = "main.less";
$outputFile = "main.css";
$less = new lessc;
$less->setFormatter("compressed");
// create a new cache object, and compile
$cache = $less->cachedCompile($inputFile);
file_put_contents($outputFile, $cache["compiled"]);
// the next time we run, write only if it has updated
$last_updated = $cache["updated"];
$cache = $less->cachedCompile($cache);
if ($cache["updated"] > $last_updated) {
    file_put_contents($outputFile, $cache["compiled"]);
}
//Test data
$arParams = array('WIDTH' => '1280', 'HEIGHT' => '720' );
$arResult = array('STATUS' => array('MP4' => '3.mp4'));

$poster = "1.jpg";
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>test</title>
    <link href="style.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">

    <!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<!--    <script src="http://vjs.zencdn.net/ie8/ie8-version/videojs-ie8.min.js"></script>-->
</head>
<body>
<video id="my-video" class="video-js" controls preload="auto" width="<?=$arParams['WIDTH']?>" height="<?=$arParams['HEIGHT']?>"  data-setup="{}">
    <source src="<?=$arResult['STATUS']['MP4'] ?>" type='video/mp4'>
</video>
<script src="video.js"></script>
<script src="main.js"></script>
</body>
</html>