<?php
	error_reporting( 0 ); 
	header( 'Content-type: text/html; charset=utf-8' );
	define( 'CB_CORE_DIR', 	dirname( __FILE__ ) );
	define( 'CB_ROOT_DIR', 	dirname( __FILE__ ) );
	
	define( 'DEBUG_START_TIME', 		microtime( true ) );
	define( 'DEBUG_PROFILING_ENABLED', 	false );
	define( 'CB_Broadcaster_Mode', 		true );
	
	include( 'core/config/server.config.php' );
	include( 'core/handler.core.php' );
	Core::broadcast();
	
?>