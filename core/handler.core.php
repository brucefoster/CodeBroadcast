<?php
	session_start();
	
	class Core {
		
		private static function __requireHandler( $handlers ) {
			foreach( $handlers as $handlerName ) { 
				if( !file_exists( CB_ROOT_DIR . '/core/handler.' . $handlerName . '.php' ) ) print( 'Trace error: handler ' . $handlerName . ' does not exists' );
				if( ! @include( CB_ROOT_DIR . '/core/handler.' . $handlerName . '.php' ) ) print( 'Trace error: handler ' . $handlerName . ' is damaged' );
				if( !class_exists( $handlerName ) ) print( 'Trace error: handler ' . $handlerName . ' is seem to be incorrect' );
			}
			
			return true;
		}
		
		public static function view() {
			self::__requireHandler(
				array(
					'database',
					'render'
					//'broadcast'
				)
			);
			
			//Database::connect();
			Render::renderPage( 'broadcasting.viewer.main', array() );
		}
		
	   /*
		*	CodeBroadcast
		*
		*	@function	void	broadcast
		*	@return		—		—
		*	@author				Bruce Foster
		*	@desc				0.1.01
		*
		*/
	
		public static function broadcast() {
			// Initializing new broadcast.
			// Setting default broadcast settings.
			$currentSessionSettings = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini', 'w+' );
			fwrite( $currentSessionSettings, CB_SESSION_DEFAULT_SETTINGS );
			fclose( $currentSessionSettings );
			
			// Empty last session user list.
			if( CB_SESSION_USERS_EMPTY == true ) $currentUsersList = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.users.ini', 'w+' );
			fclose( $currentUsersList );
			
			self::__requireHandler(
				array(
					'database',
					'render'
				)
			);
			
			//Database::connect();
			Render::renderPage( 'broadcasting.broadcaster.main', array() );
		}
		
		public static function initServer() {
			self::__requireHandler(
				array(
					'database',
					'render'
				)
			);
			
			//Database::connect();
			switch( $_POST[ '_codebroadcast_action' ] ? $_POST[ '_codebroadcast_action' ] : $_GET[ 'codebroadcast_action' ] ) {
				case 'Presenter_CB_Action_UpdateCode':
					break;
					
				case 'Presenter_CB_Action_InitBroadcast':
					$currentSettings = file_get_contents(  CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini' );
					$currentSessionSettings = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini', 'w+' );
					fwrite( $currentSessionSettings, str_replace( 'CB_Session_Broadcast=0', 'CB_Session_Broadcast=1', $currentSettings ) );
					fclose( $currentSessionSettings );
					break;
					
				case 'Presenter_CB_Action_PauseBroadcast':
					$currentSettings = file_get_contents(  CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini' );
					$currentSessionSettings = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini', 'w+' );
					fwrite( $currentSessionSettings, str_replace( 'CB_Session_Broadcast=1', 'CB_Session_Broadcast=0', $currentSettings ) );
					fclose( $currentSessionSettings );
					break;
					
				case 'Viewer_Action_GetSetting_And_Code':
					print file_get_contents(  CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini' );
					print '##CB_CODE_START';
					print file_get_contents(  CB_ROOT_DIR . '/tempdata/currentsession/session.code.ini' );
					break;
					
				case 'Broadcast_Save_Code':
					$currentCodeData = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.code.ini', 'w+' );
					fwrite( $currentCodeData, $_POST[ 'data' ] );
					fclose( $currentCodeData );
					break;
			}
		}
	}
	
?>