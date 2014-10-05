var LiveState = 0;
var BroadcastIndicator = 2;
function StartIndicator() {
	if( BroadcastIndicator == 1 ) return false;
	
	if( LiveState == 0 ) {
		$( '#state' ).css({ color: 'red', opacity: 1 });
		LiveState = 1;
	} else {
		$( '#state' ).css({ opacity: 0 });
		LiveState = 0;
	}
	
	setTimeout( 'StartIndicator();', 750 );
}


if (!Object.prototype.watch) {
	Object.defineProperty(Object.prototype, "watch", {
		  enumerable: false
		, configurable: true
		, writable: false
		, value: function (prop, handler) {
			var
			  oldval = this[prop]
			, newval = oldval
			, getter = function () {
				return newval;
			}
			, setter = function (val) {
				oldval = newval;
				return newval = handler.call(this, prop, oldval, val);
			}
			;
			
			if (delete this[prop]) { // can't watch constants
				Object.defineProperty(this, prop, {
					  get: getter
					, set: setter
					, enumerable: true
					, configurable: true
				});
			}
		}
	});
}
 
// object.unwatch
if (!Object.prototype.unwatch) {
	Object.defineProperty(Object.prototype, "unwatch", {
		  enumerable: false
		, configurable: true
		, writable: false
		, value: function (prop) {
			var val = this[prop];
			delete this[prop]; // remove accessors
			this[prop] = val;
		}
	});
}







/*
 *	Peer-to-Peer Server Side.
 *
 */
var ConnectedPeers = {};
var ServerPeer = '';
var ConnectedUsers = 0;
function StartBroadcastingServer() {
	ServerPeer = new Peer(
		'CodeBroadcastServer',
		{
			key: '50swixbllw12a9k9'
		}
	);
	
	ServerPeer.on(
		'connection', 
		function( IncomingConnection ) {
			IncomingConnection.on( 
				'data', //CB_Register_Reply
				function( Message ) {
					switch( Message[ 'action' ] ) {
						case 'CB_Register':
							PeerConnectionObject = ServerPeer.connect( Message[ 'peerID' ] );
							PeerConnectionObject.on(
								'open', function(){
									
									PeerConnectionObject.send(
										{
											answer: 'CB_Register_Reply',
											BroadcastEnabled: false
										}
									);
									
									if( ConnectedPeers[ Message[ 'peerID' ] ] == undefined ) {
										ConnectedUsers ++;
										$( '#connectedViewers' ).html( ConnectedUsers );
									}
									
									ConnectedPeers[ Message[ 'peerID' ] ] = PeerConnectionObject;
								}
							);
							
							break;
							
						case 'CB_Ask_Question':
							SelectedLine = parseFloat( Message[ 'LineID' ] );
							$( '.askselector' ).css( { display: 'block', marginTop: ( 10 + 14 * SelectedLine ) } );
							setTimeout( function() { $( '.askselector' ).fadeOut( 500 ); }, 3000 );
							break;
					}
				}
			);
		}
	);
}


function GoLive() {
	if( BroadcastIndicator == 0 ) return false;

	$( '#state-text' ).html( '<i class="fa fa-circle"></i> broadcast' );
	$( '#state-text' ).css( { color: 'red' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	BroadcastIndicator = 0;
	StartIndicator();
	
	$.each( ConnectedPeers,
		function( peerID, client ) {
			client.send(
				{
					answer: 'CB_Updated_Data',
					BroadcastEnabled: true
				}
			);
		}
	);
	
	SaveCurrentCode();
}


function Pause() {
	if( BroadcastIndicator == 1 ) return false;
	
	$( '#state-text' ).html( '<i class="fa fa-circle"></i> awaiting' );
	$( '#state-text' ).css( { color: '' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	$( '#state' ).css({ color: '', opacity: 1 });
	BroadcastIndicator = 1;
	
	$.each( ConnectedPeers,
		function( peerID, client ) {
			client.send(
				{
					answer: 'CB_Updated_Data',
					BroadcastEnabled: false
				}
			);
		}
	);
}

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

function Init_Connection() {
	
}

var Config = '';
function ReadConfigParam( param ) {
	parts = Config.toString().split( param + '=' );
	parts = parts[ 1 ].split( ';' );
	return parts[ 0 ];
}

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

var LatestData = '';
function Register_Peer( reconnect ) {
		
	if( PeerConnection == '' ) PeerConnection = new Peer(
		PeerID,
		{key: '50swixbllw12a9k9'}
	);
		
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
							
							if( Message[ 'currectCode' ] != LatestData && Message[ 'currectCode' ] != undefined ) {
								$( '#code pre' ).html( Message[ 'currectCode' ].split( '<' ).join( '&lt;' ) );
								LatestData = Message[ 'currectCode' ];
								HandleLines();
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

function AskQuestion() {
	PeerConnectionObject.send(
		{
			action: 'CB_Ask_Question',
			LineID:	SelectedLine
		}
	);
}

var CodeLastValue = '';
function SaveCurrentCode() {
	if( BroadcastIndicator == 1 ) return false;
	CurrentValue = editor.getValue();
	if( CodeLastValue == CurrentValue ) { setTimeout( function() { SaveCurrentCode(); }, 20 ); return false; }

	CodeLastValue = CurrentValue;
	$.each( ConnectedPeers,
		function( peerID, client ) {
			client.send(
				{
					answer: 'CB_Updated_Data',
					
					BroadcastEnabled: true,
					currectCode: CurrentValue
				}
			);
		}
	);
	setTimeout( function() { SaveCurrentCode(); }, 20 );
}

function Viewer_Init() {
	Register_Peer();
	$( '.workbenchmessage' ).css( { display: 'block' } );
	$( '.workbenchmessage .window' ).html( '<span id="conn">Establishing Peer-to-Peer connection with CodeBroadcast Server...</span>' );
	MakeFading( '#conn', 500, 0.2 );
}