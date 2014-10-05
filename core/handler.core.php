<?php
	session_start();
	
	class Core {
		
		public static function start() {
			self::__requireHandler(
				array(
					'database',
					'render',
					'router',
					//'broadcast'
				)
			);
			
			Database::connect();
			Router::route();
		}
		
		private static function __requireHandler( $handlers ) {
			foreach( $handlers as $handlerName ) { 
				if( !file_exists( CB_ROOT_DIR . '/core/handler.' . $handlerName . '.php' ) ) print( 'Trace error: handler ' . $handlerName . ' does not exists' );
				if( ! @include( CB_ROOT_DIR . '/core/handler.' . $handlerName . '.php' ) ) print( 'Trace error: handler ' . $handlerName . ' is damaged' );
				if( !class_exists( $handlerName ) ) print( 'Trace error: handler ' . $handlerName . ' is seem to be incorrect' );
			}
			
			return true;
		}
	}
	
?>