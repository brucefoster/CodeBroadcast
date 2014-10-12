
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};





var SelectedLine = 0;
function UnselectLine( line ) {
	setTimeout(
		function() { $( '.line' + line ).removeClass( 'selectedline' ); SelectedLine = 0; },
			5000
	);
}
	
function SelectLine( line ) {
	SelectedLine = line;
	$( '.line' + line ).addClass( 'selectedline' );
	UnselectLine( line );
}

function HandleLines() {
	  $('pre').each(function(i, block) {
		hljs.highlightBlock(block);
	  });

	  newhtml = '';
	  lines = $('#code pre').html().split( "\n" );
	  for( i = 0; i < lines.length; i++ ) newhtml += '<div class="lineselector line' + i + '" onclick="SelectLine(' + i + ');">' + lines[ i ] + '</div>' + "\r\n";
	  $('#code pre').html( newhtml );

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

function GenerateUID() {
    text = "";
    possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    for( var i=0; i < 8; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}


/*
 *	Peer-to-Peer Client-Side.
 *
 */
if( localStorage.getItem( 'PeerID' ) != undefined && localStorage.getItem( 'PeerID' ) != '' )
	var PeerID = localStorage.getItem( 'PeerID' )
else {
	localStorage.setItem( 'PeerID', 'Peer' + GenerateUID() );
	var PeerID = localStorage.getItem( 'PeerID' );
}

var PeerConnection = '';
var PeerConnectState = 0;
var PeerConnectionObject = 0;
var FadingInitialized = 0;
var FeedBackDisabled = true;

var LatestData = '';
function Register_Peer( reconnect ) {
	
	if( Options[ 'PeerJS_Use_Developers_Server' ] == true )	{
		if( PeerConnection == '' ) PeerConnection = new Peer(
			PeerID,
			{ key: Options[ 'PeerJS_Developer_Key' ] }
		);
	} else {
		if( PeerConnection == '' ) PeerConnection = new Peer(
			PeerID,
			{ host: Options[ 'PeerJS_Server' ], port: Options[ 'PeerJS_Port' ], path: Options[ 'PeerJS_Domain' ] }
		);
	}
	
	if( reconnect == true )
		PeerConnectionObject = PeerConnection.reconnect('CodeBroadcastServer');
	else
		PeerConnectionObject = PeerConnection.connect('CodeBroadcastServer');
	PeerConnectionObject.on(
		'open', function(){
			PeerConnectionObject.send(
				{
					action: 'CB_Register',
					peerID:	PeerID
				}
			);
			PeerConnectState = 1;
			$( '.workbenchmessage .window' ).html( '<span id="conn">Waiting for incoming Peer-to-Peer connection...</span>' );
		}
	);
	
	PeerConnectionObject.on(
		'close', function(){
			Register_Peer();
			$( '.workbenchmessage' ).css( { display: 'block' } );
			$( '.workbenchmessage .window' ).html( '<span id="conn">Establishing Peer-to-Peer connection with CodeBroadcast Server...</span>' );
			Register_Peer( true );
		}
	);
	
	if( reconnect != true )	
	PeerConnection.on(
		'connection', 
		function( IncomingConnection ) {
			IncomingConnection.on( 
				'data', 
				function( Message ) {
					switch( Message[ 'answer' ] ) {
						case 'CB_Register_Reply':
							if( Message[ 'BroadcastEnabled' ] == true ) {
								$( '.workbenchmessage' ).fadeOut( 100 );
							} else {
								$( '.workbenchmessage .window' ).html( '<span id="awaiting">You\'re successfully connected to CodeBroadcast Server.<br />Currently there is no active code broadcasting.</span>' );
							}
							break;
							
						case 'CB_Updated_Data':
							if( Message[ 'BroadcastEnabled' ] == true ) {
								$( '.workbenchmessage' ).fadeOut( 100 );
							} else if( Message[ 'BroadcastEnabled' ] == false ) {
								$( '.workbenchmessage' ).css( { display: 'block' } );
								$( '.workbenchmessage .window' ).html( '<span id="awaiting">You\'re successfully connected to CodeBroadcast Server.<br />Currently there is no active code broadcasting.</span>' );
							}
							
							if( Message[ 'currectCode' ] != undefined ) {
								LatestData = Message[ 'currectCode' ].split( '<' ).join( '&lt;' );
								Lines = LatestData.split( "\n" );
								position = ( Message[ 'cursor' ][ 'column' ] );
								Lines[ Message[ 'cursor' ][ 'row' ] ] = [Lines[ Message[ 'cursor' ][ 'row' ] ].slice(0, position), '<div class="cursor">|</div>', Lines[ Message[ 'cursor' ][ 'row' ] ].slice(position)].join('');
								$( '#code pre' ).html( Lines.join( "\n" ) );
								HandleLines();
							}
							
							if( Message[ 'cursor' ] ) {
								$( '.line' + Message[ 'cursor' ][ 'row' ] ).addClass( 'currectline' );
								if( FadingInitialized != 1 ) MakeFading( '.cursor', 250, 0.2 );
								FadingInitialized = 1;
							}
							break;
							
						case 'CB_Compilation_Results':
							if( Message[ 'Compilation_Results' ] ) {
								$( '.compilation' ).removeClass( 'opacity50' );
								$( '.compilationresult' ).html( Message[ 'Compilation_Results' ] );
								$( '.compilationresult' ).removeClass( 'unavailable' );
								$( '.compilationresult' ).addClass( 'compilationresult-class' );
							}
							
							
						case 'CB_Feedback_State':
							if( Message[ 'feedBackState' ] == false ) {
								$( '.feedback' ).addClass( 'disabled' );
								FeedBackDisabled = true;
							} else {
								$( '.feedback' ).removeClass( 'disabled' );
								FeedBackDisabled = false;
							}
							
							break;
					}
				}
			);
		}
	);
	
		setTimeout(
			function() {
				if( PeerConnectState == 0 ) {
					Register_Peer();
				}
			},
			5000
		);
		
}

/* 
 *  Client-Side P2P: send line ID with question
 */

function AskQuestion() {
	PeerConnectionObject.send(
		{
			action: 'CB_Ask_Question',
			LineID:	SelectedLine
		}
	);
}

function Viewer_Init() {
	$( '.workbenchmessage' ).css( { display: 'block' } );
	$( '.workbenchmessage .window' ).html( '<span id="conn">Your browser does not support Peer-to-Peer connections.</span>' );
	Register_Peer();
	$( '.workbenchmessage .window' ).html( '<span id="conn">Establishing Peer-to-Peer connection with CodeBroadcast Server...</span>' );
	MakeFading( '#conn', 500, 0.2 );
}

var editor = '';
function SendFeedback() {
	if( FeedBackDisabled == true ) return false;
	$( '.overlay' ).fadeIn( 'fast' );
	$( '.overlay .window' ).html( '<h2>Send Feedback</h2><input type="text" class="username" placeholder="Name, surname" /><div id="code" style="width: 100%;height: 400px;margin-top: 20px;">&lt;?php</div><div class="panel" style="text-align: right;"><div class="item compile" onclick="SendFeedbackText();"><i class="fa fa-send"></i> Send Feedback</div> or <a href="" onclick="CloseFeedback();return false;">Cancel</a></div>' );
		editor = ace.edit("code");
		editor.setTheme("ace/theme/github");
		editor.setShowPrintMargin(false);
		editor.getSession().setMode("ace/mode/php");
}

function SendFeedbackText() {
	if( $( '.username' ).val() == '' ) { alert( 'Please, fill out the field "Name, surname".' ); return false; }
	PeerConnectionObject.send(
		{
			action: 'CB_Send_Feedback',
			code:	editor.getValue().split( '<' ).join( '&lt;' ),
			person:	$( '.username' ).val().split( '<' ).join( '&lt;' )
		}
	);
	$( '.overlay .window' ).html( '<h2>Send Feedback</h2> <br /><div class="successtext"><i class="fa fa-check"></i> Feedback sent successfully. </div><div class="panel" style="text-align: right;"><div class="item" onclick="CloseFeedback();"><i class="fa fa-times"></i> Close window</div></div>' );
}

function CloseFeedback() {
	$( '.overlay' ).fadeOut( 'fast' );
}