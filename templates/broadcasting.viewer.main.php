<div class="heading">
	<h1 class="logo"><?php print CB_DEFAULT_TITLE; ?></h1>
	<h2 class="logodesc"><?php print CB_DEFAULT_SUBTITLE; ?></h2>
</div>
<div class="panel">
	<h6>Current session</h6>
	<div class="item" onclick="AskQuestion();">
		<i class="fa fa-info-circle" style="color: #4F95CD;"></i>
		Ask question
	</div>

</div>
<div class="panel">
	<h6>Feedback</h6>
	<div class="item feedback compile disabled" onclick="SendFeedback();">
		<i class="fa fa-send"></i>
		Send
	</div>
</div>
<div class="panel">
</div>
<div class="break"></div>
<div class="block">
	<div class="info" onclick="startindicator();">Presenter's code</div>
		<div id="code">
			<pre></pre>
	</div>
	<script type="text/javascript">Viewer_Init();</script>
</div>
<div class="block compilation opacity50">
	<div class="info">Compilation result</div>
	<div class="compilationresult unavailable">
		This content is currently unavailable.
	</div>
</div>
</form>