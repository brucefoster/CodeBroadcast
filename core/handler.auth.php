<?php
	
	class Accounting {
		private static $userData = array();
	
		public static function neutralize( $string ) {
			return preg_replace( '/[^A-Za-z0-9\-_ ]/', '', $string );
		}
		
		public static function querySafe( $string ) {
			if( is_array( $string ) ) {
				$outputArray = array();
				foreach( $string as $str ) {
					$outputArray[] = strtr( $str, array( '\'' => '\\\'' ) );
				}
				
				return $outputArray;
			}
			return strtr( $string, array( '\'' => '\\\'' ) );
		} 
		
		private static function verifySignin() {
			if( $_POST ) {
				$studentBookID = self::querySafe( $_POST[ 'studentBookID' ] );
				$request = mysql_fetch_array( mysql_query( "SELECT * FROM testing_students WHERE sStudentBook='$studentBookID'" ) );
				if( $request[ 'sFullName' ] == null ) {
					define( 'SIGNIN_ERROR', 'Данный номер зачетной книжки не зарегистрирован.' );
					return false;
				}
				
				self::$userData[ 'Name' ] = $request[ 'sName' ];
				$currentTest = mysql_fetch_array( mysql_query( "SELECT * FROM testing_schedule LEFT JOIN testing_tests ON testing_schedule.sTestID=testing_tests.tID  WHERE sGroupID='{$request[ 'sGroupID' ]}'" ) );
				if( $currentTest[ 'sTestID' ] == null ) { 
					define( 'SIGNIN_WARNING', 'На текущий день вам не назначено тестирование.' );
					return false;
				}
				
				$_SESSION[ 'TS_TestData' ][ 'Subj' ] = $currentTest[ 'tName' ];
				return true;
			}
			
			return false;
		}
		
		public static function analyze() {
			if( $_SESSION[ 'TS_UserData' ][ 'State' ] != true ) {
				if( self::verifySignin() ) {
					$_SESSION[ 'TS_UserData' ][ 'State' ] = true;
					$_SESSION[ 'TS_UserData' ][ 'Name' ] = self::$userData[ 'Name' ];
					header( 'Location: ?' );
					exit();
				}
				include( TS_ROOT_DIR . '/pages/accounting.signin.php' );
			} else {
				if( $_GET[ 'action' ] == 'start' ) {
					$_SESSION[ 'TS_TestData' ][ 'Running' ] = true;
				}
				
				if( $_SESSION[ 'TS_TestData' ][ 'Running' ] == true ) {
					$question = mysql_fetch_array( mysql_query( "SELECT * FROM testing_questions WHERE qTestID='1' ORDER BY RAND() LIMIT 1" ) );
					include( TS_ROOT_DIR . '/pages/testing.question.php' );
					exit();					
				}
				define( 'USERNAME', $_SESSION[ 'TS_UserData' ][ 'Name' ] );
				define( 'SUBJECT', $_SESSION[ 'TS_TestData' ][ 'Subj' ] );
				include( TS_ROOT_DIR . '/pages/testing.welcome.php' );
			}
		}
	}