<?php
	
	define( 'CB_DATABASE_DBNAME',	 	'studcenter' );
	define( 'CB_DATABASE_USERNAME', 	'root' );
	define( 'CB_DATABASE_PASSWORD', 	'test' );
	define( 'CB_DATABASE_SERVER',	 	'127.0.0.1' );
	
	
	# Flag CB_SESSION_USERS_EMPTY:
	#	true:	empty last session's user list (recommended in case you're going to broadcast to another group of people);
	#	false:	preserver last session's user list (if you're going to broadcast to the same group of people).
	define( 'CB_SESSION_USERS_EMPTY',	 true );
	
	# Flag CB_SESSION_DEFAULT_SETTINGS:
	#	CB_Session_Broadcast:	is broadcast enabled when you're entering to the broadcasting page;
	#				 DEFAULT:	'0'
	#				  CAN BE:	'0' or '1'
	#
	#		 CB_Session_Name:	heading title to show on the viewer's page;
	#				 DEFAULT:	'Code Broadcast'
	#				  CAN BE:	any string value
	#
	# CB_Session_Description:	heading subtitle to show on the viewer's page;
	#				 DEFAULT:	'Open Source tool for real-time code broadcasting.'
	#				  CAN BE:	any string value
	#
	#	   CB_Current_Window:	default second window;
	#				 DEFAULT:	'WINDOW_COMPILE'
	#				  CAN BE:	'WINDOW_COMPILE' or 'WINDOW_FEEDBACK'
	#
	#	CB_Register_Required:	is user registration required;
	#				 DEFAULT:	'1'
	#				  CAN BE:	'0' or '1'
	
	define( 'CB_SESSION_TITLE',		'Code Broadcast' );
	define( 'CB_SESSION_SUBTITLE',	'Open Source tool for real-time code broadcasting.' );
	
	define( 'CB_SESSION_DEFAULT_SETTINGS',	 	'CB_Session_Broadcast=0;CB_Compilation_Enabled=0;CB_Session_Name=' . CB_SESSION_TITLE . ';CB_Session_Description=' . CB_SESSION_SUBTITLE . ';CB_Current_Window=WINDOW_COMPILE;CB_Register_Required=1;' );