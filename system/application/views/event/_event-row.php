<div class="row row-event">
	<div class="img">
		<div class="frame">
			<?php $this->load->view('event/_event-icon',array('img'=>$event->event_icon)); ?>
		</div>
	</div>
	<div class="text">
    	<h3><a href="/event/view/<?php echo $event->ID; ?>"><?php echo htmlspecialchars($event->event_name); ?></a></h3>
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
    		<strong><span class="event-attend-count-<?php echo $event->ID; ?>"><?php echo $event->num_attend; ?></span> attending</strong> | 

    <?php 
	if($event->user_attending){
		if($event->event_end<time()){
			$link_txt="I was there!";;
		}else{ $link_txt="I'll be there!"; }
	}else{
		if($event->event_end<time()){
			$link_txt="Were you there?";
		}else{ $link_txt="Will you be there?"; }
	}
	?>
    		<a class="btn-small<?php echo $event->user_attending ? ' btn-success' : ''; ?>" href="#" onclick="markAttending(this,<?=$event->ID?>,<?php echo $event->event_end<time() ? 'true' : 'false'; ?>);return false;"><?=$link_txt?></a>
    	</p>
	</div>
	<div class="clear"></div>
</div>