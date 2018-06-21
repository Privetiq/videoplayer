<?php
//Less
require "less.php";
$inputFile = "style.less";
$outputFile = "style.css";
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
//Test
$arParams = array('WIDTH' => '1280', 'HEIGHT' => '720' );
$arResult = array('STATUS' => array('MP4' => '3.mp4'));
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VideoPlayer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="video-frame" style="width:<?=$arParams['WIDTH']?>px; height:<?=$arParams['HEIGHT']?>px">

        <video id='myvideo' preload='metadata' style="width:<?=$arParams['WIDTH']?>px; height:<?=$arParams['HEIGHT']?>px" loop='loop' class='videoplayer'>
            <source src="<?=$arResult['STATUS']['MP4'] ?>" type="video/mp4" />
        </video>

        <div id="controls">
            <span id="playpause" class="paused"></span>
            <span class="bottom_videoplayer_controls">
                <span id="progress">
                    <span id="total">
                        <span id="buffered">
                            <span id="current">​</span>
                        </span>
                    </span>
                </span>
                <span id="volume_high" class="volume_dynamic"></span>
                <input class="video_volume" name="volume" type="range" min="0" max="1" value="0.9" step="0.01"/>
                <span id="time">
                    <span id="currenttime">00:00</span> /
                    <span id="duration">00:00</span>
                </span>
                <span id="miniplay"></span>
                <span id="fullscreen"></span>
            </span>
        </div>
    </div>
</div>
<script src="http://my.1plus1.tv/bitrix/templates/light_red_copy/scripts/jquery-1.9.1.js"></script>
<script src="main.js"></script>
</body>
</html>