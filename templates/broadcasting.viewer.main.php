<div class="heading">
	<h1 class="logo"><?php print CB_SESSION_TITLE; ?></h1>
	<h2 class="logodesc"><?php print CB_SESSION_SUBTITLE; ?></h2>
</div>
<div class="panel">
	<h6>Current session</h6>
	<div class="item" onclick="AskQuestion();">
		<i class="fa fa-info-circle" style="color: #4F95CD;"></i>
		Ask question
	</div>
	<div class="item">
		<i class="fa fa-save"></i>
		Save locally
	</div>
</div>
<div class="panel">
	<h6>Feedback</h6>
	<div class="item compile">
		<i class="fa fa-send"></i>
		Send
	</div>
</div>
<div class="break"></div>
<div class="block">
	<div class="info" onclick="startindicator();">Presenter's code</div>
	
		<div id="code">
			<pre></pre>
	</div>
	<script type="text/javascript">Viewer_Init();</script>
</div>
<div class="block opacity50">
	<div class="info">Compilation result</div>
	<div class="unavailable">
		This content is currently unavailable.
	</div>
</div>
</form>