<?php
	include( TS_ROOT_DIR . '/pages/core.headers.php' );
?>
<form method="post">
<div class="container-small top200">
	<h1><i class="fa fa-info-circle"></i> Система тестирования</h1>
	<?php if( defined( 'SIGNIN_ERROR' ) ) { ?><div class="error"><?php print SIGNIN_ERROR; ?></div><?php } ?>
	<?php if( defined( 'SIGNIN_WARNING' ) ) { ?><div class="warning"><?php print SIGNIN_WARNING; ?></div><?php } ?>
	<h3>Добро пожаловать, <?php print USERNAME; ?>. Согласно расписанию, сейчас вы должны тестирование по теме "<b><?php print SUBJECT; ?></b>". Если вы готовы начать тестирование, пожалуйста, нажмите кнопку ниже:</h3>
	<a href="?action=start" class="start">Начать тестирование</a>
	<div class="tile"><i class="fa fa-clock-o"></i> Продолжительность: 75 мин.</div>
	<br /><br />
</div>
</form>