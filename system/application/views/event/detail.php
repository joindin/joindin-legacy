<?php
$det			= $events[0]; //print_r($det);
$cl				= array();
$times_claimed	= array();
$claimed_uids	= array();
//echo 'has event started?'; var_dump($started);

foreach($claimed as $k=>$v){ 
	//echo '<pre>'; print_r($v); echo '</pre>';
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
	if(isset($times_claimed[$v->rid])){ $times_claimed[$v->rid]++; }else{ $times_claimed[$v->rid]=1; }
	$claimed_uids[$v->rid]=$v->uid;
}

//echo '<pre>'; print_r($times_claimed); echo '</pre>';

menu_pagetitle('Event: ' . escape($det->event_name));

//echo '<pre>'; print_r($claimed); echo '</pre>';

foreach($claimed as $k=>$v){
	//echo "update user_admin set rcode='".$v->tdata['codes'][0]."' where uid=".$v->uid." and rid=".$v->rid." and rtype='talk';<br/>";
}

//echo '<pre>'; print_r($talks); echo '</pre>';

// Pick out our "event-related" ones...
$evt_sessions	= array();
$slides		= array();
foreach($talks as $k=>$v){
    if($v->tcid=='Event Related'){
	$evt_sessions[]=$v; unset($talks[$k]);
    }
    // If they have slides, add them to the array
    if(!empty($v->slides_link)){
	$slides[$v->ID]=array('link'=>$v->slides_link,'speaker'=>$v->speaker,'title'=>$v->talk_title);
    }
}

//echo '<pre>'; print_r($cl); echo '</pre>';
//echo '<pre>'; print_r($slides); echo '</pre>';
?>
<div class="detail">
	
	<div class="header">
        <?php $this->load->view('event/_event-icon',array('event'=>$det)); ?>
    
    	<div class="title">
        	<div class="head">
            	<h1><?=escape($det->event_name)?> <?=(($det->pending==1) ? '(Pending)':'')?></h1>
            
            	<p class="info">
            		<strong><?php echo date('M j, Y',$det->event_start); ?></strong> - <strong><?php echo date('M j, Y',$det->event_end); ?></strong>
            		<br/> 
            		<strong><?php echo escape($det->event_loc); ?></strong>
            	</p>
            	
            	<p class="opts">
            	<?php 
            	/*
            	if its set, but the event was in the past, just show the text "I was there!"
            	if its set, but the event is in the future, show a link for "I'll be there!"
            	if its not set show the "I'll be there/I was there" based on time
            	*/
            	if($attend && user_is_auth()){
            		if($det->event_end<time()){
            			$link_txt="I attended"; $showt=1;
            		}else{ $link_txt="I'm attending"; $showt=2; }
            	}else{
            		if($det->event_end<time()){
            			$link_txt="I attended"; $showt=3; 
            		}else{ $link_txt="I'm attending"; $showt=4; }
            	}
            	//if they're not logged in, show the questions
            	if(!user_is_auth()){ $attend=false; }
            	?>
            		
            		<a class="btn<?php echo $attend ? ' btn-success' : ''; ?>" href="javascript:void(0);" onclick="return markAttending(this,<?=$det->ID?>,<?php echo $det->event_end<time() ? 'true' : 'false'; ?>);"><?=$link_txt?></a>
            		<span class="attending"><strong><span class="event-attend-count-<?php echo $det->ID; ?>"><?php echo (int)$attend_ct; ?></span> people</strong> <?php echo (time()<=$det->event_end) ? ' attending so far':' said they attended'; ?>. <a href="javascript:void(0);"  onclick="return toggleAttendees(this, <?=$det->ID?>);" class="show">Show &raquo;</a></span>
            	</p>
            </div>
            <div class="func">
            	<a class="icon-ical" href="/event/ical/<?php echo $det->ID; ?>">Add to calendar</a>
            </div>
        	<div class="clear"></div>

        </div>
        <div class="clear"></div>
	</div>

	<div class="desc">
		<?php echo auto_p(auto_link(escape($det->event_desc))); ?>
		<hr/>

	<?php if(!empty($det->event_href) || !empty($det->event_hastag) || !empty($det->event_stub)){ ?>
		<div class="related">
		<?php if(!empty($det->event_href)){ ?>
		<?php $hrefs = array_map('trim', explode(',',$det->event_href)); ?>
        	<div class="links">
        		<h2 class="h4">Event Link<?php if (count($hrefs) != 1): ?>s<?php endif; ?></h2>
    			<ul>
    			<?php foreach ($hrefs as $href): ?>
    				<li><a href="<?php echo escape($href); ?>" rel="external"><?php echo escape($href); ?></a></li>
    			<?php endforeach; ?>
                </ul>
        	</div>
        <?php } ?>
        <?php if(!empty($det->event_hashtag)){ ?>
        <?php $hashtags = array_map('trim', explode(',',$det->event_hashtag)); ?>
        	<div class="hashtags">
        		<h2 class="h4">Hashtag<?php if (count($hashtags) != 1): ?>s<?php endif; ?></h2>
    			<ul>
    			<?php foreach ($hashtags as $hashtag): ?>
    				<?php $hashtag = str_replace('#', '', $hashtag); ?>
    				<li>#<a href="http://hashtags.org/tag/<?php echo escape($hashtag); ?>" rel="external"><?php echo escape($hashtag); ?></a></li>
    			<?php endforeach; ?>
                </ul>
        	</div>
        <?php } ?>
		<?php if(!empty($det->event_stub)){ ?>
			<div class="links">
        		<h2 class="h4">Quicklink</h2>
    			<ul>
					<li>
					<a href="/event/<?php echo $det->event_stub; ?>">http://joind.in/event/<?php echo $det->event_stub;?></a>
					</li>
                </ul>
        	</div>
		<?php } ?>
        	<div class="clear"></div>
    	</div>
    <?php } ?>
			<?php 
			// If there's a Call for Papers open for the event, let them know
			if(!empty($det->event_cfp_start) || !empty($det->event_cfp_end)){ 
			$cfp_status=($det->event_cfp_end>=time() && $det->event_cfp_start<=time()) ? 'Open!' : 'Closed';
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
	<!--<a class="btn-small" href="/event/codes/<?=$det->ID?>">Get talk codes</a>-->
	<?php if(isset($det->pending) && $det->pending==1){
		echo '<a class="btn-small" href="/event/approve/'.$det->ID.'">Approve Event</a>';
	} ?>
	<a class="btn-small" href="#" onClick="claimEvent(<?=$det->ID?>);return false;">Claim event</a>
	<a class="btn-small" href="/event/import/<?php echo $det->ID; ?>">Import Event Info</a>
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

$by_day=array();
//echo '<pre>'; print_r($talks); echo '</pre>';
foreach($talks as $v){
	//echo '<a href="/talk/view/'.$v->ID.'">'.$v->talk_title.' ('.$v->speaker.')</a><br/>';
	$day=date('Y-m-d',$v->date_given);
	$by_day[$day][]=$v;
}
ksort($by_day);
$ct=0;

?>

<div id="event-tabs">
	<ul>
		<li><a href="#talks">Talks (<?=count($talks)?>)</a></li>
		<li><a href="#comments">Comments (<?=count($comments)?>)</a></li>
		<?php if(count($evt_sessions)>0): ?>
			<li><a href="#evt_related">Event Related (<?=count($evt_sessions)?>)</a></li>
		<?php endif; ?>
		<li><a href="#slides">Slides (<?=count($slides)?>)</a></li>
		<?php if($admin): ?>
		<li><a href="#estats">Statistics</a></li>
		<?php endif; ?>
	</ul>
	<div id="talks">
	<?php if (count($by_day) == 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No talks available at the moment.')); ?>
	<?php else: ?>
		<table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
        <?php
		$total_comment_ct   = 0;
		$session_rate	    = 0;
        foreach ($by_day as $k=>$v):
            $ct = 0;
        ?>
        	<tr>
        		<th colspan="4">
        			<h4 id="talks-<?php echo $k; ?>"><?php echo date('M j, Y', strtotime($k)); ?></h4>
        		</th>
        	</tr>
        	<?php foreach($v as $ik=>$iv): 
		    $session_rate+=$iv->rank;
		?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        		<td>
        			<?php $type = !empty($iv->tcid) ? $iv->tcid : 'Talk'; ?>
        			<span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>" title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
        		</td>
        	    <?php 
					$sp_names=array();
					foreach($iv->codes as $ck => $cv){
						
						//echo $cv.' - '.$iv->ID.' '.((array_key_exists($iv->ID, $times_claimed)) ? 'yes' : 'no').'<br/>';
						
						$iscl=(array_key_exists($iv->ID, $times_claimed)) ? true : false;
						//var_dump($iscl);
						
						//If there's an exactly matching claim (name too) or... 
						if(array_key_exists($cv,$cl) || $iscl){
							//echo $iv->talk_title.' '.$cv.' '.$iv->speaker.' -> '.$ck.'<br/><br/>';
							//we match the code, but we need to find the speaker...

							$spk_split=explode(',',$iv->speaker);
							foreach($spk_split as $spk=>$spv){
								if(trim($spv)==trim($ck)){
									if(isset($cl[$cv])){ 
										$uid=$cl[$cv]['uid']; 
									}else{ 
										if(count($spk_split)>1){ $sp_names[]=escape($ck); continue; }
										$uid=$claimed_uids[$iv->ID];
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
        			<a href="/talk/view/<?php echo $iv->ID; ?>"><?php echo escape($iv->talk_title); ?></a>
        		</td>
        		<!--<td>
        			<img src="/inc/img/flags/<?php echo $iv->lang; ?>.gif" alt="<?php echo escape($iv->lang); ?>"/>
        		</td>-->
        		<td>
        			<?php echo $sp; ?>
        		</td>
        		<td>
					<a class="comment-count" href="/talk/view/<?php echo $iv->ID; ?>/#comments"><?php echo $iv->comment_count; ?></a>
				</td>
        	</tr>
        <?php
        	    $ct++;
		    $total_comment_ct+=$iv->comment_count;
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
	    <?php foreach($slides as $sk=>$sv): ?>
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
		    $type	= ($det->event_start>time()) ? 'Suggestion' : 'Feedback';
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

    $adv_mo=strtotime('+3 months',$det->event_start);
    if(time()<$adv_mo): ?>

    	<h3 id="comment-form">Write a comment</h3>
    	<?php echo form_open('event/view/'.$det->ID.'#comment-form', array('class' => 'form-event')); ?>
    
        <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
        <?php endif; ?>
    
        <?php
    	
    	$types=array(
    		'Suggestion'		=> 'Suggestion',
    		'General Comment'	=> 'General Comment',
    		'Feedback'			=> 'Feedback'
    	);
    	
    	$type=($det->event_start>time()) ? 'Suggestion':'Feedback';

    	?>

    <?php if($user_id == 0): ?>
    	<div class="row">
        	<label for="cname">Name</label>
        	<?php echo form_input('cname',$this->validation->cname); ?>
            <div class="clear"></div>
        </div>
    <?php endif; ?>
    	
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
