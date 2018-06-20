<?php
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
                <input id="volume_custom_roller" type="range" min="0" max="10" value="5" step="0.1" >
                <span id="volume_roller"></span>
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