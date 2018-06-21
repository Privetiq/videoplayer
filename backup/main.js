$(document).ready(function(){
    var controls = {
        video: $("#myvideo"),
        allcontrols: $("#controls"),
        playpause: $("#playpause"),
        total: $("#total"),
        buffered: $("#buffered"),
        progress: $("#current"),
        duration: $("#duration"),
        currentTime: $("#currenttime"),
        dynamic: $("#volume_high"),
        hasHours: false
    };
    var video = controls.video[0];

    video.addEventListener("canplay", function() {
        controls.hasHours = (video.duration / 3600) >= 1.0;
        controls.duration.text(formatTime(video.duration, controls.hasHours));
        controls.currentTime.text(formatTime(0),controls.hasHours);
    }, false);

    controls.allcontrols.click(function(){
        if (video.paused) {
            video.play();
            controls.playpause.addClass("play");
        } else {
            video.pause();
            controls.playpause.removeClass("play");
        }

        controls.playpause.toggleClass("paused");
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

        if (video.muted) {
            controls.dynamic.addClass("muted");
        } else {
            controls.dynamic.removeClass("muted");
        }

        video.muted = !video.muted;

    });

});