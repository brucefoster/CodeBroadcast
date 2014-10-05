<?php
	
	header( 'Content-type: text/html; charset=utf-8' );
	define( 'CB_CORE_DIR', 	dirname( __FILE__ ) );
	define( 'CB_ROOT_DIR', 	dirname( __FILE__ ) );
	
	define( 'DEBUG_START_TIME', 		microtime( true ) );
	define( 'DEBUG_PROFILING_ENABLED', 	false );
	
	include( 'core/config/config.php' );
	include( 'core/handler.core.php' );
	Core::start();
	
?>