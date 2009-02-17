<?php
$det=$events[0]; //print_r($det);
$cl=array();
foreach($claimed as $k=>$v){ 
	//echo '<pre>'; print_r($v); echo '</pre>';
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
}
?>

<div class="detail">
	
	<?php $this->load->view('event/_event-icon',array('event'=>$det)); ?>

	<h1><?=$det->event_name?></h1>

	<p class="info">
		<strong><?php echo date('M j, Y',$det->event_start); ?></strong> - <strong><?php echo date('M j, Y',$det->event_end); ?></strong>
		<br/> 
		<strong><?php echo htmlspecialchars($det->event_loc); ?></strong>
	</p>

	<p class="desc">
		<?=nl2br($det->event_desc)?>
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

	</p>
	<div class="clear"></div>
    (<span class="event-attend-count-<?php echo $det->ID; ?>"><?php echo (int)$attend_ct; ?></span><?php echo (time()<=$det->event_end) ? ' attending so far':' said they attended'; ?>)
</div>

<?php if($admin): ?>
<p class="admin">
	<a class="btn-small" href="/event/delete/<?=$det->ID?>">Delete event</a>
	<a class="btn-small" href="/event/edit/<?=$det->ID?>">Edit event</a>
	<a class="btn-small" href="/talk/add">Add new talk</a>
	&nbsp;
	<a class="btn-small" href="/event/codes/<?=$det->ID?>">Get talk codes</a>
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
        		<?php $sp=(array_key_exists((string)$iv->ID,$cl)) ? '<a href="/user/view/'.$cl[$iv->ID].'">'.$iv->speaker.'</a>' : $iv->speaker; ?>
        		<td>
        			<a href="/talk/view/<?php echo $iv->ID; ?>"><?php echo $iv->talk_title; ?></a>
        		</td>
        		<td nowrap="nowrap">
        			<?php echo strtoupper($iv->tcid); ?>
        		</td>
        		<td>
        			<img src="/inc/img/flags/<?php echo $iv->lang; ?>.gif" alt="<?php echo $iv->lang; ?>"/>
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
		    $uname	= ($v->user_id!=0) ? '<a href="/user/view/'.$v->user_id.'">'.$v->cname.'</a>' : $v->cname;
		    $type	= ($det->event_start>time()) ? 'Suggestion' : 'Feedback';
		?>
    	<div id="comment-<?php echo $v->ID ?>" class="row row-event-comment">
        	<div class="text">
            	<p class="info">
            		<strong><?php echo date('M j, Y, H:i',$v->date_made); ?></strong> by <strong><?php echo $uname; ?></strong> (<?php echo $type; ?>)
            	</p>
            	<p class="desc">
            		<?php echo nl2br($v->comment); ?>
            	</p>
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
