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
			$currentUsersList = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.users.ini', 'w+' );
			fwrite( $currentUsersList, '0' );
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
					
				case 'Register_Peer':
					if( $_SESSION[ 'Peer_Registered' ] == true ) print $_SESSION[ 'Peer_Registered_ID' ];
					else {
						$currentUserID = file_get_contents(  CB_ROOT_DIR . '/tempdata/currentsession/session.user.ini' );
						$_SESSION[ 'Peer_Registered' ] = true;
						$_SESSION[ 'Peer_Registered_ID' ] = $currentUserID + 1;
						print $currentUserID + 1;
						$currentUserIDList = fopen( CB_ROOT_DIR . '/tempdata/currentsession/session.settings.ini', 'w+' );
						fwrite( $currentUserIDList, $currentUserID + 1 );
						fclose( $currentUserIDList );
					}
					break;
					
				case 'CB_Compile':
					$currentCodeData = fopen( CB_ROOT_DIR . '/tempdata/currentsession/exec.code.php', 'w+' );
					$header = '<?php print "<div class=\"cbcompilcationresult\"><h5>CodeBroadcast Compilation Result</h5>\r\nCurrent compilation was built at " . date( \'d.m.Y H:i:s\') . ".</div>"; ?>';
					$footer = '<?php print "<div class=\"cbcompilcationfooter\">Code execution was successfull. No errors found during execution.</div>"; ?>';
					fwrite( $currentCodeData, $header . $_POST[ 'data' ] . ( strpos( $_POST[ 'data' ], '?>' ) ? null : " ?>\r\n" ) . $footer );
					fclose( $currentCodeData );
					break;
			}
		}
	}
	
?>