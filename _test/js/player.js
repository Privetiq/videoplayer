//var stat = new TnsVideoStatistic();
//stat.init();

var player = {

  vod:function(id_target, id_media, id_section, path, poster, width, height, server, timestamp) {
    if (swfobject.getFlashPlayerVersion().major)
      this.onlineSWF(id_target, id_media, id_section, poster, width, height, timestamp);
    else
      this.vodHTML(id_target, id_media, id_section, path, poster, width, height, server);
  },

  online:function(id_target, id_media, id_section, poster, width, height, timestamp, error, stream) {
    if ( false )
//    if ( swfobject.getFlashPlayerVersion().major )
      this.onlineSWF(id_target, id_media, id_section, poster, width, height, timestamp);
    else
      this.onlineHTML(id_target, poster, width, height, error, stream);
  },

  onlineHTML:function(id_target, poster, width, height, error, stream) {
    var html = '';
    switch (error){
      case '302': html = '<video src="'+stream+'" controls poster="' + poster + '" width=' + width +' height=' + height + '></video>';
                  break;
      case '403': html = 'access denied';
                  break;
      case '509': html = 'limit connections';
                  break;
      default:    html = 'unknown error' + error;
    }
    $('#'+id_target).html(html);
  },

  onlineHTML_:function(id_target, id_media, id_section, poster, width, height, online_onair) {
		
		alert( online_onair );
		
    $.ajax({
      type: "GET",
      url: "http://pravonavladu.tsn.ua/_test/js/config.xml",
      dataType: "xml",
      success: function(xml){
        $(xml).find("flvserver").each(function(){
          if ($(this).attr("id") == "live"+online_onair) {
            var balancer = $(this).attr("balancer");
            var stream_name = $(this).attr("stream_name");
            var tmp_url = 'http://tsn.ua/svc/video/balancer/?balancer=' + encodeURIComponent(balancer);
            $.ajax({
              type: "GET",
              url: tmp_url,
              dataType: "html",
              success: function(data){
                var onlinesrc = 'http://' + data + ':1935/' + stream_name + '/' + id_media + '/' + 'playlist.m3u8';
                $('#'+id_target).html('<video src="'+onlinesrc+'" controls autoplay poster="' + poster + '" width=' + width +' height=' + height + '></video>');
              },
              error: function(){
                $('#'+id_target).html('player initilization error 8');
              }
            });
          }
        });
      },
      error: function(){
        $('#'+id_target).html('player initilization error 9');
      }
    });
  },

  vodHTML:function(id_target, id_media, id_section, path, poster, width, height, server) {
    var src = 'http://vid' + server + '.tsn.ua/' + path + '/' + id_media + '-2.mp4';
    $('#'+id_target).html('<video src="'+src+'" controls autoplay poster="' + poster + '" width=' + width +' height=' + height + '></video>');
  },

  onlineSWF:function(id_target, id_media, id_section, poster, width, height, ts) {
    var params = {
      quality:"high",
      wmode:"transparent",
      scale:"false",
      allowfullscreen:"true",
      allowScriptAccess:"always"
    };
    var vars = {
      media_id: id_media,
      section_id: id_section,
      resource_id: "20",
      color: "#000000",
      autostart: "true",
      config_file: "http://pravonavladu.tsn.ua/_test/js/config.xml",
      hide_title: "1",
      timestamp:ts
    };
    vars.media_id = id_media;
    vars.section_id = id_section;

    swfobject.embedSWF('http://www.1plus1.ua/static/app/swf/vplayer.swf',id_target,width,height,'10',false,vars,params);
  },

  volume:function(level){
    var cookie_name = 'volume';
    var domain = '.1plus1.ua';
    if(parseInt(level)<0) {
      var vol = player.get_cookie(cookie_name);
      if(!vol || isNaN(vol) || vol == null || parseInt(vol)<=0) vol = 0.5;
      return player.get_cookie(cookie_name);
    }
    else {
      var expire = new Date();
      expire.setTime(expire.getTime() + (365*24*60*60*1000));
      player.set_cookie(cookie_name,level,expire,'/',domain);
    }
  },

  rating:function(mode, id_media){
    var cookie_name = 'rating';
    var domain = '.1plus1.ua';
    var d = new Date();
    var expires = d.getTime() + 365 * 24 * 60 * 60 * 1000;

    switch(mode) {
      case('set'):
        var cookie_val = player.get_cookie(cookie_name);
        if(parseInt(cookie_val) != -1) {
          cookie_val += ','+id_media;
        }
        else {
          cookie_val = id_media;
        }
        player.set_cookie(cookie_name, cookie_val, expires, '/', domain);
      break;

      case('get'):
        var cookie_val = player.get_cookie(cookie_name);
        if(parseInt(cookie_val) != -1) {
          var cookie_arr = cookie_val.split(',');
          var result = 0;
          for(var i=0; i<cookie_arr.length;i++) {
            if(parseInt(cookie_arr[i]) == parseInt(id_media)) result = 1;
          }
          return result;
        }
        else return 0;
      break;
    }
  },

  set_cookie:function(name, value) {
    var argv = player.set_cookie.arguments;
    var argc = player.set_cookie.arguments.length;
    var expires = (argc > 2) ? argv[2] : null;
    var path = (argc > 3) ? argv[3] : null;
    var domain = (argc > 4) ? argv[4] : null;
    var secure = (argc > 5) ? argv[5] : false;
    document.cookie = name + "=" + escape(value) +
    ((expires == null) ? "" : ("; expires=" + expires)) +
    ((path == null) ? "" : ("; path=" + path)) +
    ((domain == null) ? "" : ("; domain=" + domain)) +
    ((secure == true) ? "; secure" : "");
  },

  get_cookie:function(name) {
    var pos = document.cookie.indexOf(name + "=");
    if (pos == -1){
      return -1;
    }
    else{
      var pos2 = document.cookie.indexOf(";", pos);
      if(pos2 == -1){
        return unescape(document.cookie.substring(pos + name.length + 1));
      }
      else{
        return unescape(document.cookie.substring(pos + name.length + 1, pos2));
      }
    }
  },

  newStream:function(type, file, stream, poster, width, height) {
    /*
    file   - ссылка на файл с основным контентом VideoOnDemand или название потока для liveTV
    poster - ссылка на постер, который показывается пользователю при старте плеера до нажатия Play. Может быть пустым.
    width  - ширина плеера
    height - высота плеера
    stream - объект инициализации потока:
            {stream: {url: "rtmpte://lb1.itcons.net.ua:80/inters-redir", media: [{url: "inter_3", bitrate: "", width: ""}] }
    type - тип контента: ondemand, stream, radio
    */
    switch(type) {
      case('ondemand'):
        stat.newStream({file:file, poster:poster, width:width, height:height});
      break;

      case('stream'):
        stat.newStream({stream:stream, poster:poster, width:width, height:height});
      break;

      case('radio'):
      break;
    }
  },

  onReady:function(){
    stat.sendStat(TnsStatuses.READY);
  },

  onPlay:function(){
    setTimeout("setPoints('video')",15000);
    stat.sendStat(TnsStatuses.PLAY);
  },

  onPause:function(){
    stat.sendStat(TnsStatuses.PAUSE);
  },

  onBuffer:function(){
    stat.sendStat(TnsStatuses.BUFFER);
  },

  onBufferFull:function(){
    stat.sendStat(TnsStatuses.BUFFERFULL);
  },

  onComplete:function(){
    stat.sendStat(TnsStatuses.COMPLETE);
  },

  onSeek:function(offset){
    stat.seek(offset);
  },

  onTime:function(position){
    stat.position(position);
  },

  onError:function(){
    stat.sendStat(TnsStatuses.ERROR);
  },

  onAdPlay:function(formatType, ads_link, ads_file, click_url){
    switch(formatType) {
      case("preroll"):
        stat.sendStat(TnsStatuses.ADS_PLAY, ads_link, ads_file, click_url);
      break;

      case("midroll"):
        stat.sendStat(TnsStatuses.ADM_PLAY, ads_link, ads_file, click_url);
      break;

      case("postroll"):
        stat.sendStat(TnsStatuses.ADF_PLAY, ads_link, ads_file, click_url);
      break;

      case("pauseroll"):
        stat.sendStat(TnsStatuses.ADI_PLAY, ads_link, ads_file, click_url);
      break;
    }
  },

  onAdComplete:function(formatType){
    switch(formatType) {
      case("preroll"):
        stat.sendStat(TnsStatuses.ADS_COMPLETE);
      break;

      case("midroll"):
        stat.sendStat(TnsStatuses.ADM_COMPLETE);
      break;

      case("postroll"):
        stat.sendStat(TnsStatuses.ADF_COMPLETE);
      break;

      case("pauseroll"):
        stat.sendStat(TnsStatuses.ADI_COMPLETE);
      break;
    }
  },

  onAdEvent:function(formatType, eventType){
    switch(formatType) {
      case("preroll"):
        switch(eventType) {
          case("delpas"):
            stat.sendStat(TnsStatuses.ADS_DELPAS);
          break;
          case("skip"):
            stat.sendStat(TnsStatuses.ADS_SKIP);
          break;
          case("passed"):
            stat.sendStat(TnsStatuses.ADS_PASSED);
          break;
          case("click"):
            stat.sendStat(TnsStatuses.ADS_CLICK);
          break;
        }
      break;

      case("midroll"):
        switch(eventType) {
          case("delpas"):
            stat.sendStat(TnsStatuses.ADM_DELPAS);
          break;
          case("skip"):
            stat.sendStat(TnsStatuses.ADM_SKIP);
          break;
          case("passed"):
            stat.sendStat(TnsStatuses.ADM_PASSED);
          break;
          case("click"):
            stat.sendStat(TnsStatuses.ADM_CLICK);
          break;
        }
      break;

      case("postroll"):
        switch(eventType) {
          case("delpas"):
            stat.sendStat(TnsStatuses.ADF_DELPAS);
          break;
          case("skip"):
            stat.sendStat(TnsStatuses.ADF_SKIP);
          break;
          case("passed"):
            stat.sendStat(TnsStatuses.ADF_PASSED);
          break;
          case("click"):
            stat.sendStat(TnsStatuses.ADF_CLICK);
          break;
        }
      break;

      case("pauseroll"):
        switch(eventType) {
          case("delpas"):
            stat.sendStat(TnsStatuses.ADI_DELPAS);
          break;
          case("skip"):
            stat.sendStat(TnsStatuses.ADI_SKIP);
          break;
          case("passed"):
            stat.sendStat(TnsStatuses.ADI_PASSED);
          break;
          case("click"):
            stat.sendStat(TnsStatuses.ADI_CLICK);
          break;
        }
      break;
    }
  }
};
