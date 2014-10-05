<?php
	include( TS_ROOT_DIR . '/pages/core.headers.php' );
?>
<form method="post">
<div class="container-small top200">
	<h1><i class="fa fa-lock"></i> Вход в систему тестирования</h1>
	<?php if( defined( 'SIGNIN_ERROR' ) ) { ?><div class="error"><?php print SIGNIN_ERROR; ?></div><?php } ?>
	<?php if( defined( 'SIGNIN_WARNING' ) ) { ?><div class="warning"><?php print SIGNIN_WARNING; ?></div><?php } ?>
	<h3>Добро пожаловать в систему тестирования. Для продолжения, пожалуйста, введите ваш номер зачетной книжки:</h3>
	<input type="text" class="input" name="studentBookID" />
	<button class="signin">Войти</button>
</div>
</form>