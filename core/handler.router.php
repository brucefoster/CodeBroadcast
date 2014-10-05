<?php
	session_start();
	
	class Router {
		public static function route() {
		
			switch( OPERATING_MODE ) {
				case 'BROADCAST':
					Render::renderPage( 'broadcasting.broadcaster.main', array() );
					break;
			}
		}
		
	}
	
?>