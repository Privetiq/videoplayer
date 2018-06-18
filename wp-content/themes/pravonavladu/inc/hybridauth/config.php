<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => get_bloginfo('url') . '/',

		"providers" => array ( 
			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "702913139784232", "secret" => "a4b33201fb67b7364874705f94cc20b0" ),
				"scope" => "email"
			),
			"Vkontakte" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "4545751", "secret" => "Zsolc7hpmcIVzMgPzfaG" ),
				"scope" => "email"
			)
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => $_SERVER['DOCUMENT_ROOT'] . "/log.txt",
	);
