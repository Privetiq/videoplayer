<?php

	session_start(); 

	// change the following paths if necessary 
	$config   = dirname(__FILE__) . '/inc/hybridauth/config.php';
	require_once( dirname(__FILE__) . '/inc/hybridauth/Hybrid/Auth.php' );
	require_once( dirname(__FILE__) . '/inc/hybridauth/Hybrid/Endpoint.php' );

	if (isset($_REQUEST['hauth_start'])) {
		Hybrid_Endpoint::process();
	}
	
//if (isset($_GET['login'])) {
	$hybridauth = new Hybrid_Auth( $config );
	$adapter = $hybridauth->authenticate( "Odnoklassniki" );  
	
	$profile = $adapter->getUserProfile();
	
	print_r($profile);

	die();
//}

?>