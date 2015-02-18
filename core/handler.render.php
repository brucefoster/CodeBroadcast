<?php
	session_start();
	
	class Render {
		
		public static function renderPage( $template, $tags ) {
			ob_start();
				include( CB_ROOT_DIR . '/templates/' . $template . '.php' );
			$contents = ob_get_contents();
			ob_end_clean();
			
			include_once( CB_ROOT_DIR . '/templates/_system.headers.php' );
			print $contents;
		}

	}
	
?>