<?php 
$this->load->helper('text');
?>
<div class="row row-event">
	<?php $this->load->view('event/_event-icon',array('event' => $event, 'showLink' => true)); ?>
	<div class="text">
    	<h3><a href="/event/view/<?= $event->getId() ?>"><?= escape($event->getTitle()) ?></a></h3>
    	
    	<p class="info">
    	    <strong><?= date('M j, Y', $event->getStart()); ?></strong> - 
    	    <strong><?= date('M j, Y', $event->getEnd()) ?></strong> at 
    	    <strong><?= escape($event->getLocation()) ?></strong>
        </p>
    	<div class="desc">
            <?= auto_p(escape(word_limiter($event->getDescription(), 20))); ?>
    	</div>
    	<p class="opts">
    		<a href="/event/view/<?= $event->getId() ?>#sessions"><?= $event->getSessionCount() ?> session<?= ($event->getSessionCount() != 1) ? 's' : '' ?></a>&nbsp;|&nbsp;
			<a href="/event/view/<?= $event->getId() ?>#comments"><?= $event->getTotalCommentCount() ?> comment<?= $event->getTotalCommentCount() == 1 ? '' : 's'?></a>&nbsp;|&nbsp;
    		<strong><span class="event-attend-count-<?= $event->getId() ?>"><?= $event->getAttendanceCount() ?></span> attending</strong> | 

            <?php
	            if($event->userIsAttendee(user_get_id())){
		            if($event->getEnd() < time()) {
			            $link_txt = "I attended";
		            }
		            else { 
		                $link_txt = "I'm attending"; 
		            }
	            }else{
		            if($event->getEnd() < time()) {
			            $link_txt = "I attended";
		            }
		            else { 
		                $link_txt = "I'm attending"; 
                    }
	            }
	        ?>
    		<a class="btn-small<?= $event->userIsAttendee(user_get_id()) ? ' btn-success' : ''; ?>" href="#" onclick="markAttending(this,<?= $event->getID() ?>,<?php ($event->getEnd() < time()) ? 'true' : 'false'; ?>);return false;"><?= $link_txt ?></a>
    	</p>
	</div>
	<div class="clear"></div>
</div>
