<form method="post">
<div class="container-small top200">
	<h1>Welcome to CodeBroadcast</h1>
	<?php if( defined( 'SIGNIN_ERROR' ) ) { ?><div class="error"><?php print SIGNIN_ERROR; ?></div><?php } ?>
	<h3>Enter your broadcaster's password:</h3>
	<input type="password" class="input" name="password" />
	<button class="signin">Sign in</button>
</div>
</form>