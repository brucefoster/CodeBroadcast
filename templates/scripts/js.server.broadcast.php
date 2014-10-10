<?php
	session_start();
	if( $_SESSION[ 'CodeBroadcast_Broadcaster_Auth_Passed' ] != true ) exit( '// Access is denied.' );
?>
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

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};






/*
 *	Peer-to-Peer Server Side.
 *
 */
var ConnectedPeers = {};
var FeedBackList = {};
var ServerPeer = '';
var ConnectedUsers = 0;
function StartBroadcastingServer() {
	
	if( Options[ 'PeerJS_Use_Developers_Server' ] == true )
		ServerPeer = new Peer(
			'CodeBroadcastServer',
			{ key: Options[ 'PeerJS_Developer_Key' ] }
		);
	else
		ServerPeer = new Peer(
			'CodeBroadcastServer',
			{ host: Options[ 'PeerJS_Server' ], port: Options[ 'PeerJS_Port' ], path: Options[ 'PeerJS_Domain' ] }
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
											BroadcastEnabled: ( BroadcastIndicator == 1 ? true : false )
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
							
						case 'CB_Send_Feedback':
							FeedBackList[ Message[ 'person' ] ] = Message[ 'code' ];
							$( '#sentFeedbacks' ).html( Object.size( FeedBackList ) );
							break;
					}
				}
			);
		}
	);
}


function _BuildTable( columns, data ) {
	table = '<table><tr>';
	$.each( columns, function( value ) { table += '<td>' + value + '</td>'; } )
	
	table += '</tr>';
	$.each( data, function( columns ) { 
		table += '<tr>';
		$.each( columns, function( value ) { table += '<td>' + value + '</td>'; } )
		table += '</tr>'; 
	});
	
	table += '</table>';
	return table;
}

function ShowFeedbacks() {
	$( '.overlay' ).fadeIn( 'fast' );
	$( '.overlay .window' ).html( '<h2>Feedbacks</h2><div class="feedbackdata" style="height: 360px;overflow-y: scroll;"></div>' );
	$.each( FeedBackList, function( key, value ) {
		$( '.feedbackdata' ).append( '<div class="feedbacklink"><a href="" onclick="ShowCode(\'' + key + '\');return false;">' + key + '</a></div>' );
	});
	
}

function ShowCode( codeID ) {
	$( '.overlay .window' ).html( '<h2>Feedback from ' + codeID + '</h2><div id="code" style="height: 360px;"><pre>' + FeedBackList[ codeID ] + '</pre></div> <div class="panel" style="text-align: right;"><div class="item" onclick="CloseFeedback();"><i class="fa fa-times"></i> Close window</div></div>' );
	HandleLines();
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

function HandleLines() {
	  $('pre').each(function(i, block) {
		hljs.highlightBlock(block);
	  });

	  newhtml = '';
	  lines = $('#code pre').html().split( "\n" );
	  for( i = 0; i < lines.length; i++ ) newhtml += '<div class="lineselector line' + i + '" onclick="SelectLine(' + i + ');">' + lines[ i ] + '</div>' + "\r\n";
	  $('#code pre').html( newhtml );

}

var LastSettings = 'param=value';
function CompileCode( settings ) {
	if( settings ) LastSettings = settings;
	$.post( 
		'server.php',
		{
			'_codebroadcast_action': 'CB_Compile',
			'data': editor.getValue()
		},
		function( data ) {
			executionRequest = $.get( 
				'tempdata/currentsession/exec.code.php' + ( settings ? ( '?' + settings ) : '' ),
				function( getData ) {
					$( '.compilation' ).removeClass( 'opacity50' );
					$( '.compilationresult' ).html( getData );
					$( '.compilationresult' ).removeClass( 'unavailable' );
					$( '.compilationresult' ).addClass( 'compilationresult-class' );
				}
			).fail(function() {
				$( '.compilation' ).removeClass( 'opacity50' );
				$( '.compilationresult' ).html( '<div class="cbcompilationfail"><h5>CodeBroadcast Compilation Error</h5>Unable to compile the currect code because it contains fatal errors.</div>' );
				$( '.compilationresult' ).removeClass( 'unavailable' );
				$( '.compilationresult' ).addClass( 'compilationresult-class' );
			}).always(
				function() {
					$.each( ConnectedPeers,
						function( peerID, client ) {
							client.send(
								{
									answer: 'CB_Compilation_Results',
									Compilation_Results: $( '.compilationresult' ).html()
								}
							);
						}
					);
				}
			);
		}
	);
}

function CompileCodeWithSettings( directOutput ) {
	if( directOutput == true ) { CompileCode( LastSettings ); return false; }
	$( '.overlay' ).fadeIn( 'fast' );
	$( '.overlay .window' ).html( '<h2>Advanced Compilation</h2><br /><div class="caption">GET Parameters:</div> <input type="text" id="CompilationSettings" class="input" value="' + LastSettings + '" /> <br /><div class="panel" style="text-align: right;"><div class="item" onclick="CompileCode( $(\'#CompilationSettings\').val() );$( \'.overlay\' ).fadeOut( \'fast\' );"><i class="fa fa-times"></i> Proceed to compilation</div></div>' );
	$( '.feedbackdata' ).append( '<div class="feedbacklink"><a href="" onclick="ShowCode(\'' + key + '\');return false;">' + key + '</a></div>' );
	
	
}

var CodeLastValue = '';
var CursorLastValueX = '';
var CursorLastValueY = '';
function SaveCurrentCode() {
	if( BroadcastIndicator == 1 ) return false;
	
	CurrentValue = editor.getValue();
	CurrentCursor = editor.selection.getCursor();
	
	if( CodeLastValue == CurrentValue && CursorLastValueX == CurrentCursor[ 'row' ] && CursorLastValueY == CurrentCursor[ 'column' ] ) { setTimeout( function() { SaveCurrentCode(); }, 20 ); return false; }
	CursorLastValueX = CurrentCursor[ 'row' ];
	CursorLastValueY = CurrentCursor[ 'column' ];
	CodeLastValue = CurrentValue;
	$.each( ConnectedPeers,
		function( peerID, client ) {
			client.send(
				{
					answer: 'CB_Updated_Data',
					
					BroadcastEnabled: true,
					currectCode: CurrentValue,
					cursor: editor.selection.getCursor()
				}
			);
		}
	);
	setTimeout( function() { SaveCurrentCode(); }, 20 );
}

function SetFeedBackState( state ) {
	if( state == true ) {
		$( '.feedbackon' ).addClass( 'disabled' );
		$( '.feedbackoff' ).removeClass( 'disabled' );
	} else {
		$( '.feedbackoff' ).addClass( 'disabled' );
		$( '.feedbackon' ).removeClass( 'disabled' );
	}
	
	$.each( ConnectedPeers,
		function( peerID, client ) {
			client.send(
				{
					answer: 'CB_Feedback_State',
					feedBackState: state
				}
			);
		}
	);
}

function CloseFeedback() {
	$( '.overlay' ).fadeOut( 'fast' );
}