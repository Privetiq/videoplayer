<?php
		
		define('_PLAYER_ONLINE_ID_MEDIA_', 189931);
		
		function GetOnlineStream( $options = false ){

      $defaults = array(
        'string.var1'=>'Ct8v/qTbm/QMDQxbU6fn1d0ic2Cvy86jzympfUe+Czo=',
        'string.var2'=>'6E4zfFROW7RvqAdoIRNabnowo8TeeBjq6nYUXoRawGY=',
        'string.var3'=>'DC1wIBU6pnoMR+DPbhPzCKGFuQqkZk0eDPb09rKPeCY=',
        'string.var4'=>'CyEsGdCy6pzA7mIB0WoTjTz2ixKpxJ8YJtbPB5wGukY=',
        'string.var5'=>'cV4oYvjEu5kjaskYpLZ3p2DqVssutMCiL3LWlycPpm8=',
        'int.id_media'=>_PLAYER_ONLINE_ID_MEDIA_,
        'string.url'=>'https://grandcentral.1plus1.ua/lb/live',
        'string.user_agent'=>'',
        //'string.user_agent'=>getenv('HTTP_USER_AGENT')
      );

      $settings = array_merge($defaults,$options);
      $str = time().$settings['int.id_media'].$settings['string.user_agent'].$settings['string.var1'].$settings['string.var3'];
      $hash = md5($str);
      $url = $settings['string.url'].'/'.$settings['int.id_media'].'/'.$hash;

      $ch = curl_init();
      $ip = $_SERVER['REMOTE_ADDR'];

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt($ch, CURLOPT_USERAGENT, $settings['string.user_agent']);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('X_HTTP_RQ: '.$ip, 'REMOTE_ADDR: '.$ip, 'HTTP_X_FORWARDED_FOR: '.$ip));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      $response = curl_exec( $ch );
      curl_close($ch);

		  return explode('=',$response);
		}

		$_STREAM = GetOnlineStream( array('int.id_media'=>_PLAYER_ONLINE_ID_MEDIA_) );

		$_PLAYER_ONLINE_WIDTH = 720;
		$_PLAYER_ONLINE_HEIGHT = 568;
		$_PLAYER_ONLINE_IMG = 'online.png';
		$_PLAYER_ONLINE_ID_MEDIA = _PLAYER_ONLINE_ID_MEDIA_;
		
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

	
	<script src="http://www.1plus1.ua/static/js/jquery.min.js"></script>
	<script src="http://www.1plus1.ua/static/js/swfobject.js"></script>
	<script src="js/player.js"></script>

</head>

<body>
	
  <div class="video-holder" id="video-holder" style="width:720px; height:568px; cursor:pointer; background-image:url(online.png); background-repeat:no-repeat; background-position:center; background-size:cover;"></div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {

				player.online('video-holder', <?php echo $_PLAYER_ONLINE_ID_MEDIA; ?>,'1','<?php echo $_PLAYER_ONLINE_IMG; ?>', <?php echo $_PLAYER_ONLINE_WIDTH; ?>, <?php echo $_PLAYER_ONLINE_HEIGHT; ?>, <?=time();?>,'<?=$_STREAM[0]?>','<?=$_STREAM[1]?>');
		});
  </script>
<p><?=$_STREAM[1]?></p>
</body>
</html>