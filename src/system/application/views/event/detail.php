<?php
$cl				= array();

foreach($claimed as $k=>$v){ 
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
}

menu_pagetitle('Event: ' . escape($event_detail->event_name));

?>
<div class="detail">
	
	<div class="header">
        <?php $this->load->view('event/_event-icon',array('event'=>$event_detail)); ?>
    
    	<div class="title">
        	<div class="head">
            	<h1><?=escape($event_detail->event_name)?> <?=(($event_detail->pending==1) ? '(Pending)':'')?></h1>
            
            	<p class="info">
					<strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($det->event_start, $det->event_tz_cont.'/'.$det->event_tz_place, 'M j, Y'); ?></strong> - <strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($det->event_end, $det->event_tz_cont.'/'.$det->event_tz_place, 'M j, Y'); ?></strong>
            		<br/> 
            		<strong><?php echo escape($event_detail->event_loc); ?></strong>
            	</p>
            	
            	<p class="opts">
            	<?php 
            	/*
            	if its set, but the event was in the past, just show the text "I was there!"
            	if its set, but the event is in the future, show a link for "I'll be there!"
            	if its not set show the "I'll be there/I was there" based on time
            	*/
            	if($attend && user_is_auth()){
            		if($event_detail->event_end<time()){
            			$link_txt="I attended"; $showt=1;
            		}else{ $link_txt="I'm attending"; $showt=2; }
            	}else{
            		if($event_detail->event_end<time()){
            			$link_txt="I attended"; $showt=3; 
            		}else{ $link_txt="I'm attending"; $showt=4; }
            	}
            	//if they're not logged in, show the questions
            	if(!user_is_auth()){ $attend=false; }
            	?>
            		
            		<a class="btn<?php echo $attend ? ' btn-success' : ''; ?>" href="javascript:void(0);" onclick="return markAttending(this,<?=$event_detail->ID?>,<?php echo $event_detail->event_end<time() ? 'true' : 'false'; ?>);"><?=$link_txt?></a>
            		<span class="attending"><strong><span class="event-attend-count-<?php echo $event_detail->ID; ?>"><?php echo (int)$attend_ct; ?></span> people</strong> <?php echo (time()<=$event_detail->event_end) ? ' attending so far':' said they attended'; ?>. <a href="javascript:void(0);"  onclick="return toggleAttendees(this, <?=$event_detail->ID?>);" class="show">Show &raquo;</a></span>
            	</p>
            </div>
            <div class="func">
            	<a class="icon-ical" href="/event/ical/<?php echo $event_detail->ID; ?>">Add to calendar</a>
            </div>
        	<div class="clear"></div>

        </div>
        <div class="clear"></div>
	</div>

	<div class="desc">
		<?php echo auto_p(auto_link(escape($event_detail->event_desc))); ?>
		<hr/>

	<?php if(!empty($event_detail->event_href) || !empty($event_detail->event_hastag) || !empty($event_detail->event_stub)){ ?>
		<div class="related">
		<?php if(!empty($event_detail->event_href)){ ?>
		<?php $hrefs = array_map('trim', explode(',',$event_detail->event_href)); ?>
        	<div class="links">
        		<h2 class="h4">Event Link<?php if (count($hrefs) != 1): ?>s<?php endif; ?></h2>
    			<ul>
    			<?php foreach ($hrefs as $href): ?>
    				<li><a href="<?php echo escape($href); ?>" rel="external"><?php echo escape($href); ?></a></li>
    			<?php endforeach; ?>
                </ul>
        	</div>
        <?php } ?>
        <?php if(!empty($event_detail->event_hashtag)){ ?>
        <?php $hashtags = array_map('trim', explode(',',$event_detail->event_hashtag)); ?>
        	<div class="hashtags">
        		<h2 class="h4">Hashtag<?php if (count($hashtags) != 1): ?>s<?php endif; ?></h2>
    			<ul>
    			<?php foreach ($hashtags as $hashtag): ?>
    				<?php $hashtag = str_replace('#', '', $hashtag); ?>
    				<li>#<a href="http://hashtags.org/<?php echo escape($hashtag); ?>" rel="external"><?php echo escape($hashtag); ?></a></li>
    			<?php endforeach; ?>
                </ul>
        	</div>
        <?php } ?>
		<?php if(!empty($event_detail->event_stub)){ ?>
			<div class="links">
        		<h2 class="h4">Quicklink</h2>
    			<ul>
					<li>
					<a href="/event/<?php echo $event_detail->event_stub; ?>">http://joind.in/event/<?php echo $event_detail->event_stub;?></a>
					</li>
                </ul>
        	</div>
		<?php } ?>
        	<div class="clear"></div>
    	</div>
    <?php } ?>
			<?php 
			// If there's a Call for Papers open for the event, let them know
			if(!empty($event_detail->event_cfp_start) || !empty($event_detail->event_cfp_end)){ 
			$cfp_status=($event_detail->event_cfp_end>=time() && $event_detail->event_cfp_start<=time()) ? 'Open!' : 'Closed';
			?>
			<div class="links">
				<b>Call for Papers Status: <?php echo $cfp_status; ?> </b> 
			</div>
			<div class="clear"></div>
			<?php } ?>
	</div>
</div>

<?php if($admin): ?>
<p class="admin">
	<!--<a class="btn-small" href="/event/codes/<?=$event_detail->ID?>">Get talk codes</a>-->
	<?php if(isset($event_detail->pending) && $event_detail->pending==1){
		echo '<a class="btn-small" href="/event/approve/'.$event_detail->ID.'">Approve Event</a>';
	} ?>
	<a class="btn-small" href="#" onClick="claimEvent(<?=$event_detail->ID?>);return false;">Claim event</a>
	<a class="btn-small" href="/event/import/<?php echo $event_detail->ID; ?>">Import Event Info</a>
</p>
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

// work through the talks list and split into days
$by_day=array();
foreach($talks as $t){
	$day = strtotime($t->display_date);
	$by_day[$day][]=$t;
}
ksort($by_day);
$ct=0;
?>

<div id="event-tabs">
	<ul>
		<li><a href="#talks">Talks (<?php echo count($talks)?>)</a></li>
		<li><a href="#comments">Comments (<?php echo count($comments)?>)</a></li>
		<?php if(count($evt_sessions)>0): ?>
			<li><a href="#evt_related">Event Related (<?php echo count($evt_sessions)?>)</a></li>
		<?php endif; ?>
		<li><a href="#slides">Slides (<?php echo count($slides_list)?>)</a></li>
		<?php if($admin): ?>
		<li><a href="#estats">Statistics</a></li>
		<?php endif; ?>
		<?php if(count($tracks)>0): ?>
			<li><a href="#tracks">Tracks (<?php echo count($tracks); ?>)</a></li>
		<?php endif; ?>
	</ul>
	<div id="talks">
	<?php if (count($by_day) == 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No talks available at the moment.')); ?>
	<?php else: 
		if(isset($track_filter)){
			echo '<span style="font-size:13px">Sessions for track <b>'.$track_data->track_name.'</b></span>';
			echo ' <span style="font-size:11px"><a href="/event/view/'.$event_detail->ID.'">[show all sessions]</a></span>';
			echo '<br/><br/>';
		}
		?>
		<table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
        <?php
		$total_comment_ct   = 0;
		$session_rate	    = 0;
        foreach ($by_day as $talk_section_date=>$talk_section_talks): // was $k=>$v
            $ct = 0;
        ?>
        	<tr>
        		<th colspan="4">
        			<h4 id="talks"><?php echo date('M d, Y', $talk_section_date); ?></h4>
        		</th>
        	</tr>
        	<?php foreach($talk_section_talks as $ik=>$talk): 
		    $session_rate+=$talk->rank;
			
			if(isset($track_filter)){
				//Filter to the track ID
				if(empty($talk->tracks)){ 
					// If there's no track ID on the talk, don't show it
					continue; 
				}else{
					// There are tracks on the session, let's see if any match...
					$filter_pass=false;
					foreach($talk->tracks as $talk_track){
						if($talk_track->ID==$track_filter){ $filter_pass=true; }
					}
					if(!$filter_pass){ continue; }
				}
			}
		?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        		<td>
        			<?php $type = !empty($talk->tcid) ? $talk->tcid : 'Talk'; ?>
        			<span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>" title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
        		</td>
        	    <?php 
					$sp_names=array();
					foreach($talk->codes as $ck => $cv){
						
						$iscl=(array_key_exists($talk->ID, $times_claimed)) ? true : false;
					
						//If there's an exactly matching claim (name too) or... 
						if(array_key_exists($cv,$cl) || $iscl){
							//we match the code, but we need to find the speaker...

							$spk_split=explode(',',$talk->speaker);
							foreach($spk_split as $spk=>$spv){
								if(trim($spv)==trim($ck)){
									if(isset($cl[$cv])){ 
										$uid=$cl[$cv]['uid']; 
									}else{ 
										if(count($spk_split)>1){ $sp_names[]=escape($ck); continue; }
										$uid=$claimed_uids[$talk->ID];
									}
									$sp_names[]='<a href="/user/view/'.$uid.'">'.escape($spv).'</a>';
								}else{
									
								}
							}
						}else{ $sp_names[]=escape($ck); }
						$sp=implode(', ',$sp_names);
					}
					?>
        		<td>
        			<a href="/talk/view/<?php echo $talk->ID; ?>"><?php echo escape($talk->talk_title); ?></a>
					<?php
						if($talk->display_time != '00:00') {echo '(' . $talk->display_time . ')';}
					?>
        		</td>
        		<td>
        			<?php echo $sp; ?>
        		</td>
        		<td>
					<a class="comment-count" href="/talk/view/<?php echo $talk->ID; ?>/#comments"><?php echo $talk->comment_count; ?></a>
				</td>
        	</tr>
        <?php
        	    $ct++;
		    $total_comment_ct+=$talk->comment_count;
            endforeach;
        endforeach;
        ?>
        </table>
    <?php endif; ?>
	</div>
	<div id="evt_related">
	    <?php $ct=0; ?>
	    <table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
	    <?php foreach($evt_sessions as $ik=>$iv): ?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
		    <td>
		    <?php $type = !empty($iv->tcid) ? $iv->tcid : 'Talk'; $type='Social Event'; ?>
		    <span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>"
			  title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
		    </td>
		    <td>
			<a href="/talk/view/<?php echo $iv->ID; ?>"><?php echo escape($iv->talk_title); ?></a>
		    </td>
		    <td><?php echo $iv->speaker; ?></td>
		    <td>
			<a class="comment-count" href="/talk/view/<?php echo $iv->ID; ?>/#comments"><?php echo $iv->comment_count; ?></a>
		    </td>
		</tr>
	    <?php $total_comment_ct+=$iv->comment_count; endforeach; ?>
	    </table>
	</div>
	<?php if($admin){ ?>
	<div id="estats">
	    <h3>Event Statistics</h3>
	    <table cellpadding="0" cellspacing="0" border="0">
	    <tr><td><b>Number of Sessions:</b></td><td style="padding:3px"><?php echo count($talks); ?></td></tr>
	    <tr><td><b>Last Comment:</b></td><td style="padding:3px"><?php 
			echo (isset($latest_comment[0])) ? date('m.d.Y H:i:s',$latest_comment[0]->max_date) : '[none]';
		?></td></tr>
	    <tr><td><b>Total # of Comments</b></td><td style="padding:3px""><?php 
			echo (isset($total_comment_ct)) ? $total_comment_ct : '[none]'; 
		?></td></tr>
	    <tr><td><b>Average Session Rating</b></td><td style="padding:3px"><?php 
			echo (isset($session_rate)) ? round($session_rate/count($talks),2) : '[none]';
		?></td></tr>
	    </table>
	</div>
	<?php } ?>
	<div id="slides">
	    <table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
	    <?php foreach($slides_list as $sk=>$sv): ?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
		    <td>
		    <a href="/talk/view/<?php echo $sk; ?>"><?php echo $sv['title']; ?></a>
		    </td>
		    <td>
			<?php echo $sv['speaker']; ?>
		    </td>
		    <td>
			<a href="<?php echo $sv['link']; ?>">Slides</a>
		    </td>
		</tr>
	    <?php endforeach; ?>
	    </table>
	</div>
	<div id="tracks">
		<?php
		foreach($tracks as $k=>$tr){
			echo '<div style="padding:3px">';
			if($tr->used>0){
				echo '<a style="font-size:13px;font-weight:bold" href="/event/view/'.$event_detail->ID.'/track/'.$tr->ID.'">'.$tr->track_name.'</a><br/>';
			}else{ echo '<span style="font-size:13px;font-weight:bold;">'.$tr->track_name.'</span><br/>'; }
			echo $tr->track_desc.'<br/>';
			echo $tr->used.' sessions';
			echo '</div>';
		}
		?>
	</div>
	<div id="comments">
	
	<?php
    $msg=$this->session->flashdata('msg');
    if (!empty($msg)): 
    ?>
        <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
    <?php endif; ?>
	
	<?php if (count($comments) == 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
	<?php else: ?>

		<?php 
		foreach ($comments as $k => $v): 
			if($v->user_id != 0) {
				$uname = '<strong><a href="/user/view/'.$v->user_id.'">'.escape($v->cname).'</a></strong>';
			} elseif(isset($v->cname)) {
				$uname = '<strong>'.escape($v->cname).'</strong>';
			} else {
				$uname = "<span class=\"anonymous\">Anonymous</span>";
			}
		    $type	= ($event_detail->event_start>time()) ? 'Suggestion' : 'Feedback';
		?>
    	<div id="comment-<?php echo $v->ID ?>" class="row row-event-comment">
        	<div class="text">
            	<p class="info">
            		<strong><?php echo date('M j, Y, H:i',$v->date_made); ?></strong> by <strong><?php echo $uname; ?></strong> (<?php echo escape($type); ?>)
            	</p>
            	<div class="desc">
            		<?php echo auto_p(escape($v->comment)); ?>
            	</div>
        	</div>
        	<div class="clear"></div>
        </div>
        <?php endforeach; ?>
    <?php endif;

    $adv_mo=strtotime('+3 months',$event_detail->event_start);
    if(time()<$adv_mo): ?>

    	<h3 id="comment-form">Write a comment</h3>
    	<?php echo form_open('event/view/'.$event_detail->ID.'#comment-form', array('class' => 'form-event')); ?>
    
        <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
        <?php endif; ?>
    
        <?php
    	
    	$types=array(
    		'Suggestion'		=> 'Suggestion',
    		'General Comment'	=> 'General Comment',
    		'Feedback'			=> 'Feedback'
    	);
    	
    	$type=($event_detail->event_start>time()) ? 'Suggestion':'Feedback';

    	?>

    	<div class="row">
        	<label for="type">Type</label>
        	<div class="input"><?=$type?></div>
            <div class="clear"></div>
        </div>
    	
    	<div class="row">
        	<label for="event_comment">Comment</label>
        	<?php 
            $arr = array(
        			'name'=>'event_comment',
                    'id'=>'event_comment',
        			'value'=>$this->validation->event_comment,
        			'cols'=>40,
        			'rows'=>10
            );
            echo form_textarea($arr);
            ?>
            <div class="clear"></div>
        </div>
    	<div class="row row-buttons">
        	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit Comment'); ?>
        </div>
    	<?php endif; echo form_close(); ?>
	</div>
</div>

<script type="text/javascript">
$(function() { 
	$('#event-tabs').tabs();
	if (window.location.hash == '#comment-form') {
		$('#event-tabs').tabs('select', '#comments');
	} else {
	<?php if (count($talks) == 0): ?>
		$('#event-tabs').tabs('select', '#comments');
	<?php endif; ?>
	}
});
</script>
