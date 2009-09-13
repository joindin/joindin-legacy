<?php

/*
$cl = array();
foreach($claimed as $k=>$v){ 
	//echo '<pre>'; print_r($v); echo '</pre>';
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
}
*/


menu_pagetitle('Event: ' . escape($event->getTitle()));

//foreach($claimed as $k=>$v){
	//echo "update user_admin set rcode='".$v->tdata['codes'][0]."' where uid=".$v->uid." and rid=".$v->rid." and rtype='talk';<br/>";
//}

// Catch flash messages
$this->load->view('message/flash');
?>
<div class="detail">
	
	<div class="header">
        <?php $this->load->view('event/_event-icon', array('event' => $event)); ?>
    
    	<div class="title">
        	<div class="head">
            	<h1><?= $event->getTitle() ?> <?= (($event->isPending()) ? '(Pending)':'') ?></h1>
            
            	<p class="info">
            		<strong><?= date('M j, Y', (int) $event->getStart()); ?></strong> - <strong><?= date('M j, Y', (int) $event->getEnd()); ?></strong>
            		<br/> 
            		<strong><?= escape($event->getLocation()); ?></strong>
            	</p>
            	
            	<p class="opts">
            	<?php 
            	/*
            	if its set, but the event was in the past, just show the text "I was there!"
            	if its set, but the event is in the future, show a link for "I'll be there!"
            	if its not set show the "I'll be there/I was there" based on time
            	*/
            	if($event->userIsAttendee(user_get_id())) {
            		if($event->getEnd() < time()) {
            			$link_txt = "I attended"; 
            			$showt = 1;
            		} 
            		else { 
            		    $link_txt = "I'm attending"; 
            		    $showt = 2; 
                    }
            	} else {
            		if($event->getEnd() < time()) {
            			$link_txt = "I attended";
            			$showt = 3; 
            		} else { 
            		    $link_txt = "I'm attending"; 
            		    $showt = 4; 
                    }
            	}
            	?>
            		<a class="btn<?= ($event->userIsAttendee(user_get_id())) ? ' btn-success' : ''; ?>" href="javascript:void(0);" onclick="return markAttending(this,<?= $event->getId() ?>,<?= ($event->getEnd() < time()) ? 'true' : 'false'; ?>);"><?= $link_txt ?></a>
            		<span class="attending">
            		    <strong>
            		        <span class="event-attend-count-<?= $event->getId(); ?>"><?= (int)$event->getAttendanceCount(); ?></span> people
                        </strong> 
                        <?= (time() <= $event->getEnd()) ? ' attending so far':' said they attended'; ?>. <a href="javascript:void(0);"  onclick="return toggleAttendees(this, <?= $event->getId() ?>);" class="show">Show &raquo;</a></span>
            	</p>
            </div>
            <div class="func">
            	<a class="icon-ical" href="/event/ical/<?= $event->getId(); ?>">Add to calendar</a>
            </div>
        	<div class="clear"></div>

        </div>
        <div class="clear"></div>
	</div>

	<div class="desc">
		<?php echo auto_p(auto_link(escape($event->getDescription()))); ?>
		<hr/>
    <div class="related">
    <?php 
        if($event->getLink() != '') {
            $links = array_map('trim', explode(',', $event->getLink())); 
    ?>
        	<div class="links">
        		<h2 class="h4">Link<?= (count($links) != 1) ? 's' : '' ?></h2>
			    <ul>
			    <?php foreach($links as $link) : ?>
				    <li><a href="<?= escape($link) ?>" rel="external"><?= escape($link); ?></a></li>
			    <?php endforeach; ?>
                </ul>
        	</div>
    <?php 
        } 
        if($event->getHashtag() != '') {
            $hashtags = array_map('trim', explode(',', $event->getHashtag()));
    ?>
            <div class="hashtags">
        		<h2 class="h4">Hashtag<?php if (count($hashtags) != 1): ?>s<?php endif; ?></h2>
    			<ul>
    			<?php foreach ($hashtags as $hashtag): ?>
    				<?php $hashtag = str_replace('#', '', $hashtag); ?>
    				<li>#<a href="http://hashtags.org/tag/<?= escape($hashtag); ?>" rel="external"><?= escape($hashtag); ?></a></li>
    			<?php endforeach; ?>
                </ul>
        	</div>
    <?php
        }
    ?>
        <div class="clear"></div>
    </div>

	</div>
</div>

<?php if(user_is_admin() || $event->isEventManager(user_get_id())): ?>
<div class="admin">
	<a class="btn-small" href="/event/edit/<?=$event->getId()?>">Edit event</a>
	<a class="btn-small" href="/event/managers/<?=$event->getId()?>">View managers</a>
	&nbsp;|&nbsp;
	<a class="btn-small" href="/session/add/<?=$event->getId()?>">Add Session</a>
	<a class="btn-small" href="/event/codes/<?=$event->getId()?>">Get session codes</a>
	&nbsp;|&nbsp;
	<a class="btn-small" href="/event/delete/<?=$event->getId()?>">Delete event</a>
	<?php if($event->isPending()){
		echo '<a class="btn-small btn-green" href="/event/approve/'. $event->getId() .'">Approve Event</a>';
	} ?>
</div>
<?php endif; ?>

<p class="ad">
    <script type="text/javascript"><!--
    google_ad_client = "pub-2135094760032194";
    /* 468x60, created 11/5/08 */
    google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>


<?php
// Sort sessions by day
$sorted = array();
$sessions = $event->getSessions();
foreach($sessions as $session) {
    $day = date('Y-m-d', $session->getDate());
    $sorted[$day][] = $session;
}
ksort($sorted);

$rowCount = 0;

?>

<div id="event-tabs">
	<ul>
		<li><a href="#talks">Sessions (<?= count($event->getSessions()) ?>)</a></li>
		<li><a href="#comments">Comments (<?= $event->getCommentCount() ?>)</a></li>
	</ul>
	<div id="talks">
	<?php if (count($sorted) === 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No sessions available at the moment.')); ?>
	<?php else: ?>
		<table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
        <?php 
        foreach ($sorted as $day => $sessions):
        ?>
        	<tr>
        		<th colspan="4">
        			<h4 id="talks-<?=$day; ?>"><?= date('M j, Y', strtotime($day)); ?></h4>
        		</th>
        	</tr>
        	<?php foreach($sessions as $session): ?>
        	<tr class="<?= ($rowCount % 2 == 0) ? 'row1' : 'row2'; ?>">
        		<td>
        			<span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $session->getType())); ?>" title="<?= escape($session->getType()); ?>"><?= escape(strtoupper($session->getType())); ?></span>
        		</td>
        	    <?php 
					/*$sp_names=array();
					foreach($iv->codes as $ck => $cv){
						if(array_key_exists($cv,$cl)){ 
							//echo $iv->talk_title.' '.$cv.' '.$iv->speaker.' -> '.$ck.'<br/>';
							//we match the code, but we need to find the speaker...
							$spk_split=explode(',',$iv->speaker);
							foreach($spk_split as $spk=>$spv){
								if(trim($spv)==trim($ck)){
									$uid=$cl[$cv]['uid'];
									$sp_names[]='<a href="/user/view/'.$uid.'">'.escape($spv).'</a>';
								}
							}
						}else{ $sp_names[]=escape($ck); }
						$sp=implode(', ',$sp_names);
					}*/
					?>
        		<td>
        			<a href="/session/view/<?= $session->getId() ?>"><?= escape($session->getTitle()); ?></a>
        		</td>
        		<td>
        			<?= $session->getSpeaker() ?>
        		</td>
        		<td>
					<a class="comment-count" href="/talk/view/<?= $session->getId(); ?>/#comments"><?php echo $session->getCommentCount(); ?></a>
				</td>
        	</tr>
        <?php
        	    $rowCount++;
            endforeach;
        endforeach;
        ?>
        </table>
    <?php endif; ?>
	</div>
	<div id="comments">
	
	<?php
        $message = $this->session->flashdata('msg');
        if (!empty($message)) {
            $this->load->view('msg_info', array('msg' => $message));
        }
    ?>
	
	<?php if (count($event->getComments()) === 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
	<?php else: ?>

		<?php 
		foreach ($event->getComments() as $eventComment): 
		    
		?>
    	<div id="comment-<?php echo $eventComment->getId() ?>" class="row row-event-comment">
        	<div class="text">
            	<p class="info">
            		<strong><?php echo date('M j, Y, H:i',$eventComment->getDate()); ?></strong> 
            		by <strong><?php print_comment_author($eventComment); ?> </strong>
            		(<?= escape($eventComment->getType()); ?>)
            	</p>
            	<div class="desc">
            		<?php echo auto_p(escape($eventComment->getComment())); ?>
            	</div>
        	</div>
        	<div class="clear"></div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    	<h3 id="comment-form">Write a comment</h3>
    	<?php echo form_open('event/view/' . $event->getId() . '#comment-form', array('class' => 'form-event')); ?>
    
        <?php if (isset($commentErrors) && !empty($commentErrors)): ?>
            <?php $this->load->view('msg_error', array('msg' => $commentErrors)); ?>
        <?php endif; ?>
    
        <?php
    	
    	$types = array(
    		'Suggestion'		=> 'Suggestion',
    		'General Comment'	=> 'General Comment',
    		'Feedback'			=> 'Feedback'
    	);
    	
    	$type = ($event->getStart() > time()) ? 'Suggestion' : 'Feedback';

    	?>

    <?php if(user_get_id() == 0): ?>
    	<div class="row">
        	<label for="cname">Name</label>
        	<?= form_input('author_name', (isset($comment) ? $comment->getAuthorName() : '')); ?>
            <div class="clear"></div>
        </div>
    <?php else : ?>
        <input type="hidden" name="user_id" id="user_id" value="<?= user_get_id() ?>" />
    <?php endif; ?>
    	
    	<div class="row">
        	<label for="type">Type</label>
        	<div class="input"><?= $type ?></div>
            <div class="clear"></div>
        </div>
    	
    	<div class="row">
        	<label for="comment">Comment</label>
        	<?php 
            $textarea = array(
        			'name' => 'comment',
                    'id' => 'comment',
        			'value' => (isset($commet) ? $comment->getComment() : ''),
        			'cols' => 40,
        			'rows' => 10
            );
            echo form_textarea($textarea);
            ?>
            <div class="clear"></div>
        </div>
    	
    	<div class="row row-buttons">
        	<?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit Comment'); ?>
        </div>
    	<?php  echo form_close(); ?>
	</div>
</div>

<script type="text/javascript">
$(function() { 
	$('#event-tabs').tabs();
	if (window.location.hash == '#comment-form') {
		$('#event-tabs').tabs('select', '#comments');
	} else {
	<?php if (count($event->getSessions()) == 0): ?>
		$('#event-tabs').tabs('select', '#comments');
	<?php endif; ?>
	}
});
</script>
