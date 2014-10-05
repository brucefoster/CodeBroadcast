indicatorState = 0;
indicatorStop  = 2;
function startindicator() {
	if( indicatorStop == 1 ) return false;
	
	if( indicatorState == 0 ) {
		$( '#state' ).css({ color: 'red', opacity: 1 });
		indicatorState = 1;
	} else {
		$( '#state' ).css({ opacity: 0 });
		indicatorState = 0;
	}
	
	setTimeout( 'startindicator();', 750 );
}

function GoLive() {
	if( indicatorStop == 0 ) return false;
	$.post( "server.php", { '_codebroadcast_action': 'Presenter_CB_Action_InitBroadcast' }, function( data ) {
		
	});
	$( '#state-text' ).html( '<i class="fa fa-circle"></i> broadcast' );
	$( '#state-text' ).css( { color: 'red' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	indicatorStop = 0;
	startindicator();
	
	SaveCurrentCode();
}

function Pause() {
	if( indicatorStop == 1 ) return false;
	$.post( "server.php", { '_codebroadcast_action': 'Presenter_CB_Action_PauseBroadcast' }, function( data ) {
		
	});
	$( '#state-text' ).html( '<i class="fa fa-circle"></i> awaiting' );
	$( '#state-text' ).css( { color: '' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	$( '#state' ).css({ color: '', opacity: 1 });
	indicatorStop = 1;
}

function UnselectLine( line ) {
		setTimeout(
			function() { $( '.line' + line ).removeClass( 'selectedline' ); },
			5000
		);
	}
	
	function SelectLine( line ) {
		$( '.line' + line ).addClass( 'selectedline' );
		UnselectLine( line );
	}

function HandleLines() {
	$(document).ready(function() {
	  $('pre').each(function(i, block) {
		hljs.highlightBlock(block);
	  });

	  newhtml = '';
	  lines = $('#code pre').html().split( "\n" );
	  for( i = 0; i < lines.length; i++ ) newhtml += '<span class="lineselector line' + i + '" onclick="SelectLine(' + i + ');">' + lines[ i ] + '</span>' + "\r\n";
	  $('#code pre').html( newhtml );
	});
}

var fadingFlags = { 1: 0 };
function MakeFading( element, interval, opacity, secondcall ) {	
	if( secondcall != true ) fadingFlags[ element + 'state' ] = 1;
	if( fadingFlags[ element + 'state' ] == 1 ) {
		$( element ).fadeTo( 300, 1 );
		fadingFlags[ element + 'state' ] = 0;
	} else if( fadingFlags[ element + 'state' ] == 2 ) {
		$( element ).fadeTo( 300, 1 );
		return false;
	} else {
		$( element ).fadeTo( 300, opacity );
		fadingFlags[ element + 'state' ] = 1;
	}
	
	setTimeout(
		function() { MakeFading( element, interval, opacity, true ); },
		interval
	);
}

function StopFading( element ) { fadingFlags[ element + 'state' ] = 2; }

function Init_Connection() {
	
}

var Config = '';
function ReadConfigParam( param ) {
	parts = Config.toString().split( param + '=' );
	parts = parts[ 1 ].split( ';' );
	return parts[ 0 ];
}

xFlag = 0;
xStart = 0;
xLast = '';
function SendServerCommand() {
	xStart = 1;
	req = $.post( "server.php", { '_codebroadcast_action': 'Viewer_Action_GetSetting_And_Code' }, function( data ) {
		xStart = 0;
		Config = ' ' + data;
		if( ReadConfigParam( 'CB_Session_Broadcast' ) == '0' ) {
			if( $( '.workbenchmessage .window' ).html().indexOf( 'wait') > 1 ) { setTimeout( function() { SendServerCommand(); }, 50 ); return false; }
			StopFading( '#conn' );
			xFlag = 0;
			$( '.workbenchmessage .window' ).html( '<span id="nobroadcast">You\'re connected to the broadcasting server. Please wait for the broadcast to start.</span>' );
			$( '.workbenchmessage' ).css( { display: 'block' } );
		} else { 
			if( xFlag == 0 ) {
				$( '.workbenchmessage .window' ).html( '' ); 
				$( '.workbenchmessage' ).fadeOut( 'fast' ); 
				xFlag = 1;
			}
			code = data.split( '##CB_CODE_START' );
			if( xLast != code[1] ) {
				xLast = code[ 1 ];
				$('#code pre').html( code[ 1 ].split( '<' ).join( '&lt;' )  );
				HandleLines();
			}
		}
		setTimeout( function() { SendServerCommand(); }, 20 );
	});
	
}

lastValue = '';
function SaveCurrentCode() {
	if( indicatorStop == 1 ) return false;
	newValue = editor.getValue();
	if( lastValue == newValue ) { setTimeout( function() { SaveCurrentCode(); }, 20 ); return false; }
	 lastValue = newValue;
	$.post( "server.php", { '_codebroadcast_action': 'Broadcast_Save_Code', 'data': lastValue }, function( data ) {
		setTimeout( function() { SaveCurrentCode(); }, 20 );
	});
	
}

function Viewer_Init() {
	$( '.workbenchmessage .window' ).html( '<span id="conn">Connecting to the broadcasting server...</span>' );
	MakeFading( '#conn', 500, 0.2 );
	SendServerCommand();
}