<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all">
		<span class="ui-icon ui-icon-info">&nbsp;</span>
		<div class="msg">
			<?php 
			if(is_array($message)) {
				foreach($message as $text) {
					echo $text . "<br />";
				}
			} else { 
				echo $message;
			} 
			?>
		</div>
		<div class="clear"></div>
	</div>
</div>
