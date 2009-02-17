<div class="row row-talk">
	<div class="img">
		<?php echo rating_image($talk->tavg); ?>
	</div>
	<div class="text">
    	<h3><a href="/talk/view/<?php echo escape($talk->ID); ?>"><?php echo escape($talk->talk_title); ?></a></h3>
    	<p class="opts">
    		at <a href="/event/view/<?php echo escape($talk->eid); ?>"><?php echo escape($talk->event_name); ?></a> |
    		<a href="/talk/view/<?php echo escape($talk->ID); ?>#comments"><?php echo $talk->ccount; ?> comment<?php echo $talk->ccount == 1 ? '' : 's'?></a>
    	</p>
	</div>
	<div class="clear"></div>
</div>