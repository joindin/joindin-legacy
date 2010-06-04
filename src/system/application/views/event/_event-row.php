<?php 
$this->load->helper('text');
$this->load->library('timezone');
?>
<div class="row row-event">
	<?php $this->load->view('event/_event-icon',array('event'=>$event, 'showlink' => true)); ?>
	<div class="text">
    	<h3><a href="/event/view/<?php echo $event->ID; ?>"><?php echo escape($event->event_name); ?></a></h3>
		<p class="info"><strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($event->event_start, $event->event_tz_cont.'/'.$event->event_tz_place, 'M j, Y'); ?></strong> - <strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($event->event_end, $event->event_tz_cont.'/'.$event->event_tz_place, 'M j, Y'); ?></strong> at <strong><?php echo escape($event->event_loc); ?></strong></p>
    	<div class="desc">
        <?php echo auto_p(escape(word_limiter($event->event_desc, 20))); ?>
    	</div>
    	<p class="opts">
    		<a href="/event/view/<?php echo $event->ID; ?>#comments"><?php echo $event->num_comments; ?> comment<?php echo $event->num_comments == 1 ? '' : 's'?></a> |
    		<strong><span class="event-attend-count-<?php echo $event->ID; ?>"><?php echo $event->num_attend; ?></span> attending</strong> | 

			<!--<input type="checkbox" name="attend" value="1"/> Attending?-->

    <?php 
		if($event->event_end<time()){
			$link_txt="I attended";
		}else{ $link_txt="I'm attending"; }
	?>
    		<a class="btn-small<?php echo $event->user_attending ? ' btn-success' : ''; ?>" href="#" onclick="markAttending(this,<?php echo $event->ID?>,<?php echo $event->event_end<time() ? 'true' : 'false'; ?>);return false;"><?php echo $link_txt?></a>

    	</p>
	</div>
	<div class="clear"></div>
</div>
