<?php
$det=$events[0]; //print_r($det);
$cl=array();
foreach($claimed as $k=>$v){ 
	//echo '<pre>'; print_r($v); echo '</pre>';
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
}

menu_pagetitle('Event: ' . escape($det->event_name));
?>
<div class="detail">
	
	<div class="header">
        <?php $this->load->view('event/_event-icon',array('event'=>$det)); ?>
    
    	<div class="title">
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
        			$link_txt="I was there!"; $showt=1;
        		}else{ $link_txt="I'll be there!"; $showt=2; }
        	}else{
        		if($det->event_end<time()){
        			$link_txt="Were you there?"; $showt=3; 
        		}else{ $link_txt="Will you be there?"; $showt=4; }
        	}
        	//if they're not logged in, show the questions
        	if(!user_is_auth()){ $attend=false; }
        	?>
        		
        		<a class="btn<?php echo $attend ? ' btn-success' : ''; ?>" href="#" onclick="markAttending(this,<?=$det->ID?>,<?php echo $det->event_end<time() ? 'true' : 'false'; ?>);return false;"><?=$link_txt?></a>
        		<span class="attending"><strong><span class="event-attend-count-<?php echo $det->ID; ?>"><?php echo (int)$attend_ct; ?></span> People</strong> <?php echo (time()<=$det->event_end) ? ' attending so far':' said they attended'; ?>. <a href="javascript:void(0);"  onclick="toggleAttendees(this, <?=$det->ID?>);" class="show">Show &raquo;</a></span>
        	</p>
        	<div class="clear"></div>

        </div>
        <div class="clear"></div>
	</div>

	<div class="desc">
		<?php echo auto_p(auto_link(escape($det->event_desc))); ?>
		<hr/>

	<?php if(!empty($det->event_href) || !empty($det->event_hastag)){ ?>
		<div class="related">
		<?php if(!empty($det->event_href)){ ?>
		<?php $hrefs = array_map('trim', explode(',',$det->event_href)); ?>
        	<div class="links">
        		<h2 class="h4">Link<?php if (count($hrefs) != 1): ?>s<?php endif; ?></h2>
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
        	<div class="clear"></div>
    	</div>
    <?php } ?>
			<?php
			/*if(!empty($det->event_href) || !empty($det->event_hastag)){
				echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td width="50%" valign="top" style="font-size:11px">';
				if(!empty($det->event_href)){
					echo '<b>Links</b><br/>'; 
					foreach(explode(',',$det->event_href) as $v){ echo '<a href="'.$v.'">'.$v.'</a><br/>'; }
				}
				echo '</td><td valign="top" width="50%" style="font-size:11px">';
				if(!empty($det->event_hashtag)){
					echo '<b>Hashtags</b><br/>'; foreach(explode(',',$det->event_hashtag) as $v){ 
						echo '<a href="http://hashtags.org/tag/'.str_replace('#','',$v).'">'.$v.'</a><br/>'; 
					}
				}
				echo '</td></tr></table>';
			}*/
		?>
	</div>
</div>

<?php if($admin): ?>
<p class="admin">
	<a class="btn-small" href="/event/delete/<?=$det->ID?>">Delete event</a>
	<a class="btn-small" href="/event/edit/<?=$det->ID?>">Edit event</a>
	<a class="btn-small" href="/event/approve/<?=$det->ID?>">Approve event</a>
	&nbsp;
	<a class="btn-small" href="/talk/add/event/<?=$det->ID?>">Add new talk</a>
	&nbsp;
	<a class="btn-small" href="/event/codes/<?=$det->ID?>">Get talk codes</a>
	<?php if(isset($det->pending) && $det->pending==1){
		echo '<a class="btn-small" href="/approve">Approve Event</a>';
	} ?>
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
	</ul>
	<div id="talks">
	<?php if (count($by_day) == 0): ?>
		<?php $this->load->view('msg_info', array('msg' => 'No talks available at the moment.')); ?>
	<?php else: ?>
		<table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
        <?php 
        foreach ($by_day as $k=>$v):
            $ct = 0;
        ?>
        	<tr>
        		<th colspan="4">
        			<h4 id="talks-<?php echo $k; ?>"><?php echo date('M j, Y', strtotime($k)); ?></h4>
        		</th>
        	</tr>
        	<?php foreach($v as $ik=>$iv): ?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        		<?php $sp=(array_key_exists((string)$iv->ID,$cl)) ? '<a href="/user/view/'.$cl[$iv->ID].'">'.escape($iv->speaker).'</a>' : escape($iv->speaker); ?>
        		<td>
        			<a href="/talk/view/<?php echo $iv->ID; ?>"><?php echo escape($iv->talk_title); ?></a>
        		</td>
        		<td nowrap="nowrap">
        			<?php echo escape(strtoupper($iv->tcid)); ?>
        		</td>
        		<td>
        			<img src="/inc/img/flags/<?php echo $iv->lang; ?>.gif" alt="<?php echo escape($iv->lang); ?>"/>
        		</td>
        		<td>
        			<?php echo $sp; ?>
        		</td>
        	</tr>
        <?php
        	    $ct++;
            endforeach;
        endforeach;
        ?>
        </table>
    <?php endif; ?>
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
		    $uname	= ($v->user_id!=0) ? '<a href="/user/view/'.$v->user_id.'">'.escape($v->cname).'</a>' : escape($v->cname);
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
    <?php endif; ?>

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
    	<?php  echo form_close(); ?>
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
