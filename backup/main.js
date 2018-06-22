$(document).ready(function(){
    var controls = {
        video: $("#myvideo"),
        allcontrols: $(".playpausevideo"),
        playpause: $("#playpause"),
        total: $("#total"),
        buffered: $("#buffered"),
        progress: $("#current"),
        duration: $("#duration"),
        currentTime: $("#currenttime"),
        dynamic: $("#volume_high"),
        miniplay: $("#miniplay"),
        videovolume: $(".video_volume"),
        fullscreenButton: $("#fullscreen"),
        videoframe: $(".video-frame"),
        hasHours: false
    };
    var video = controls.video[0];

    video.addEventListener("canplay", function() {
        controls.hasHours = (video.duration / 3600) >= 1.0;
        controls.duration.text(formatTime(video.duration, controls.hasHours));
        controls.currentTime.text(formatTime(0),controls.hasHours);
    }, false);

    controls.fullscreenButton.click(function () {
        var fullscr = controls.videoframe[0];
        if (fullscr.requestFullscreen) {
            video.style.height = '100%';
            video.style.width = '100%';
            fullscr.requestFullscreen();
        } else if (fullscr.mozRequestFullScreen) {
            video.style.height = '100%';
            video.style.width = '100%';
            fullscr.mozRequestFullScreen();
        } else if (fullscr.webkitRequestFullscreen) {
            video.style.height = '100%';
            video.style.width = '100%';
            fullscr.webkitRequestFullscreen();
        }
    });

    function playpausevideo() {
        if (video.paused) {
            video.play();
            controls.playpause.addClass("play");
            controls.miniplay.addClass("ministop");
        } else {
            video.pause();
            controls.playpause.removeClass("play");
            controls.miniplay.removeClass("ministop");
        }

        controls.playpause.toggleClass("paused");
    }

    controls.allcontrols.click(function () {
        playpausevideo();
    });

    controls.playpause.click(function () {
        playpausevideo();
    });

    controls.miniplay.click(function () {
        playpausevideo();
    });


    function formatTime(time, hours) {
        if (hours) {
            var h = Math.floor(time / 3600);
            time = time - h * 3600;

            var m = Math.floor(time / 60);
            var s = Math.floor(time % 60);

            return h.lead0(2)  + ":" + m.lead0(2) + ":" + s.lead0(2);
        } else {
            var m = Math.floor(time / 60);
            var s = Math.floor(time % 60);

            return m.lead0(2) + ":" + s.lead0(2);
        }
    }

    Number.prototype.lead0 = function(n) {
        var nz = "" + this;
        while (nz.length < n) {
            nz = "0" + nz;
        }
        return nz;
    };

    video.addEventListener("timeupdate", function() {
        controls.currentTime.text(formatTime(video.currentTime, controls.hasHours));

        var progress = Math.floor(video.currentTime) / Math.floor(video.duration);
        controls.progress[0].style.width = Math.floor(progress * controls.total.width()) + "px";
    }, false);

    controls.total.click(function(e) {
        var x = (e.pageX - this.offsetLeft)/$(this).width();
        video.currentTime = x * video.duration;
    });

    video.addEventListener("progress", function() {
        var buffered = Math.floor(video.buffered.end(0)) / Math.floor(video.duration);
        controls.buffered[0].style.width =  Math.floor(buffered * controls.total.width()) + "px";
    }, false);

    controls.dynamic.click(function() {
        var cachedval = 0;
        if (video.muted) {
            controls.dynamic.addClass("muted");
            cachedval = controls.videovolume.val();
            controls.videovolume.val(0);
        } else {
            controls.dynamic.removeClass("muted");
            controls.videovolume.val(cachedval);
        }

        video.muted = !video.muted;
    });
    controls.videovolume.on('input', function(e){
        var min = e.target.min,
            max = e.target.max,
            val = e.target.value;

        video.volume = controls.videovolume.val();

        $(e.target).css({
            'backgroundSize': (val - min) * 100 / (max - min) + '% 3px'
        });
    }).trigger('input');
});