<div class="ui-widget">
	<div class="ui-state-error ui-corner-all">
		<span class="ui-icon ui-icon-alert">&nbsp;</span>
		<div class="msg">
			<?php 
			if(is_array($msg)) {
				foreach($msg as $text) {
					echo $text . "<br />";
				}
			} else { 
				echo $msg;
			} 
			?>
		</div>
		<div class="clear"></div>
	</div>
</div>
