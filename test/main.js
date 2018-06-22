document.addEventListener('DOMContentLoaded', function(){
    var player = document.querySelector('#my-video');
    var myPlayer = videojs(player);
    myPlayer.options.autoplay = false;
});