<?php
	include( TS_ROOT_DIR . '/pages/core.headers.php' );
	
	if( $_POST ) {
		if( $_POST[ 'select' ] ) {
			if( $_POST[ 'select' ] == $_SESSION[ 'CurrentTest' ][ 'CorrectID' ] ) define( 'SIGNIN_WARNING', 'Правильный ответ' ); else  define( 'SIGNIN_ERROR', 'Неправильный ответ!' );
		}
		
		if( $_POST[ 'input' ] ) {
			similar_text( $_POST[ 'input' ], $_SESSION[ 'CurrentTest' ][ 'CorrectID' ], $percent );
			if( $percent >= $_SESSION[ 'CurrentTest' ][ 'CorrectSimilarity' ] ) define( 'SIGNIN_WARNING', 'Правильный ответ' ); else  define( 'SIGNIN_ERROR', 'Неправильный ответ!' );
		}
	}
	
	switch( $question[ 'qInputType' ] ) {
			case 'SELECT':
				$variants = array();
				$correct  = $question[ 'qCorrectAnswerID' ];
				for( $items = 1; $items <= 6; $items ++ ) {
					if( $question[ 'qAnswer' . $items ] ) $variants[] = $question[ 'qAnswer' . $items ];
				}
				
				$correctValue = $variants[ $correct - 1 ];
				$answersList = null;
				shuffle( $variants );
				foreach( $variants as $key => $value ) {
					$answersList .= '<label><input name="select" type="radio" value="' . $key . '" /> ' . $value . '</label><br />';
					if( $value == $correctValue ) $_SESSION[ 'CurrentTest' ][ 'CorrectID' ] = $key;
				}
				break;
			case 'INPUT':
				$_SESSION[ 'CurrentTest' ][ 'CorrectID' ] = $question[ 'qDefinedAnswer' ];
				$_SESSION[ 'CurrentTest' ][ 'CorrectSimilarity' ] = $question[ 'qSimilarity' ];
				$answersList = '<input type="text" name="input" class="answer" placeholder="Введите ваш ответ..." />';
				
				break;
		}
?>
<div class="heading">
	<h1 class="logo"><i id="state" class="fa fa-circle"></i> <?php print CB_SESSION_TITLE; ?></h1>
	<h2 class="logodesc"><?php print CB_SESSION_SUBTITLE; ?></h2>
</div>
<div class="panel">
	<h6>Broadcasting</h6>
	<div class="item broadcast" onclick="GoLive();">
		<i class="fa fa-video-camera" style="color: darkred;"></i>
		Go Live
	</div>
	<div class="item disabled pause-broadcast" onclick="Pause();">
		<i class="fa fa-pause"></i>
		Pause
	</div>
</div>
<div class="panel">
	<h6>Code options</h6>
	<div class="item compile">
		<i class="fa fa-play"></i>
		Compile
	</div>
	<div class="item">
		<i class="fa fa-save"></i>
		Save locally
	</div>
</div>
<div class="panel">
	<h6>Feedback</h6>
	<div class="item">
		<i class="fa fa-eye"></i>
		Show
	</div>
	<div class="item disabled">
		<i class="fa fa-times"></i>
		Hide
	</div>
</div>
<div class="panel">
	<h6>Settings</h6>
	<div class="item">
		<i class="fa fa-cog"></i>
		Settings
	</div>
</div>
<div class="status">
	<div class="info">State:</div>
	<div class="data" id="state-text"><i class="fa fa-circle"></i> awaiting</div>
	<div class="info">Current window:</div>
	<div class="data"><i class="fa fa-bug"></i> compile</div>
	<div class="info">Active users:</div>
	<div class="data"><i class="fa fa-users"></i> 0</div>
</div>
<div class="block">
	<div class="info" onclick="startindicator();">Presenter's code</div>
	<div id="code" style="width: 100%;height: 400px;">&lt;?php
	</div>
	<script src="templates/src-min/ace.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		var editor = ace.edit("code");
		editor.setTheme("ace/theme/github");
		editor.setShowPrintMargin(false);
		editor.getSession().setMode("ace/mode/php");
	</script>
</div>
<div class="block opacity50">
	<div class="info">Compilation result</div>
	<div class="unavailable">
		This content is currently unavailable.
	</div>
</div>
</form>