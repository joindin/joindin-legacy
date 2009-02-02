<div class="row row-event">
	<div class="img">
		<div class="frame"><img src="/inc/img/_event<?php echo mt_rand(1,4); ?>.gif"/></div>
	</div>
	<div class="text">
    	<h3 style="<?php echo isset($style) ? $style : ''; ?>"><a href="/event/view/<?php echo $event->ID; ?>"><?php echo htmlspecialchars($event->event_name); ?></a></h3>
    	<p class="info"><strong><?php echo date('M j, Y',$event->event_start); ?></strong> - <strong><?php echo date('M j, Y',$event->event_end); ?></strong> at <strong><?php echo htmlspecialchars($event->event_loc); ?></strong></p>
    	<p class="desc">
        <?php 
    	$p=explode(' ',$event->event_desc);
    	$str='';
    	for($i=0;$i<20;$i++){ if(isset($p[$i])){ $str.=$p[$i].' '; } } echo htmlspecialchars(trim($str)).'...';
        ?>
    	</p>
    	<p class="opts">
    		<a href="/event/view/<?php echo $event->ID; ?>#comments"><?php echo $event->num_comments; ?> comment<?php echo $event->num_comments == 1 ? '' : 's'?></a> |
    		<strong><?php echo $event->num_attend; ?> attending</strong> | 
    		<a href="" class="btn-small">Will you be there?</a>
    	</p>
	</div>
	<div class="clear"></div>
</div>