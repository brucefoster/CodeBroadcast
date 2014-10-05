<?php
	
	header( 'Content-type: text/html; charset=utf-8' );
	define( 'STUDCENTER_CORE_DIR', 	dirname( __FILE__ ) );
	define( 'STUDCENTER_ROOT_DIR', 	dirname( __FILE__ ) );
	
	define( 'DEBUG_START_TIME', 		microtime( true ) );
	define( 'DEBUG_PROFILING_ENABLED', 	false );
	
	include( 'settings/config.php' );
	include( 'handlers/handler.engine.php' );
	Engine::start();
	
?>