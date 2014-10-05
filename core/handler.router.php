<?php
	session_start();
	
	class Router {
		
		public static function route() {
			switch( $_POST[ 'action' ] ) {
				case null:
				default:
					Render::renderPage( 'broadcasting.broadcaster.main', array() );
					break;
			}
		}
		
	}
	
?>