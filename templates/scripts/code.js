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
	$( '#state-text' ).html( '<i class="fa fa-circle"></i> broadcast' );
	$( '#state-text' ).css( { color: 'red' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	indicatorStop = 0;
	startindicator();
}

function Pause() {
	if( indicatorStop == 1 ) return false;
	$( '#state-text' ).html( '<i class="fa fa-circle"></i> awaiting' );
	$( '#state-text' ).css( { color: '' } );
	$( '.pause-broadcast' ).toggleClass( 'disabled' );
	$( '.broadcast' ).toggleClass( 'disabled' );
	$( '#state' ).css({ color: '', opacity: 1 });
	indicatorStop = 1;
}