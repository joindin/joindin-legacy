<?php
error_reporting(E_ALL);

menu_pagetitle('Talk: ' . escape($detail->talk_title));

if(!empty($claim_msg)){
	$class=($claim_status) ? 'notice' : 'err';
	if($claim_msg && !empty($claim_msg)){ echo '<div class="'.$class.'">'.escape($claim_msg).'</div><br/>'; }
}
?>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>
<?php
$speaker_ids= array();
$speaker    = array();

if(empty($speaker_claim)){ $speaker[]=escape($detail->speaker); }
$speaker_txt= implode(', ',$speaker);
$rstr 		= rating_image($detail->tavg);

?>
<div class="detail">
	<h1><?=$detail->talk_title?></h1>

	<p class="info">
		<strong><?php echo $speaker_txt; ?></strong> (<?php echo $detail->display_datetime; ?>)
		<br/> 
		<?php echo escape($detail->tcid); ?> at <strong><a href="/event/view/<?php echo $detail->event_id; ?>"><?php echo escape($detail->event_name); ?></a></strong> (<?php echo escape($detail->lang_name);?>)
	</p>

	<p class="rating">
		<?php echo $rstr; ?>
	</p>

	<div class="desc">
		<?=auto_p(auto_link(escape($detail->talk_desc)));?>
	</div>
	
	<p class="quicklink">
		Quicklink: <strong><a href="http://joind.in/<?php echo $detail->tid; ?>">http://joind.in/<?php echo $detail->tid; ?></a></strong>
	</p>
	
	<?php if(!empty($track_info)): ?>
	<p class="quicklink">
	<?php
	echo '<b>Track(s):</b> '; foreach($track_info as $t){ echo $t->track_name; }
	?>
	</p>
	<?php endif; ?>
	
	<?php if(!empty($detail->slides_link)): ?>
	<p class="quicklink">
		Slides: <strong><a href="<?php echo $detail->slides_link; ?>"><?php echo $detail->talk_title; ?></a></strong>
	</p>
	<?php endif; ?>

	<?php if(isset($claimed[0]) && $this->session->userdata('ID')==$claimed[0]->userid): ?>
	<!--<p class="opts">
		<a class="btn-small" href="/user/comemail/talk/<?php echo $detail->tid; ?>">Email me my comments</a>
	</p>-->
	<?php endif; ?>
	<div class="clear"></div>
</div>

<p class="admin">
<?php if($admin):?>
	<a class="btn-small" href="/talk/delete/<?php echo $detail->tid; ?>">Delete talk</a>	
	<a class="btn-small" href="/talk/edit/<?php echo $detail->tid; ?>">Edit talk</a>
<?php endif; ?>
<?php
if(empty($claims) || $ftalk<count($speaker)): ?>
	<a class="btn-small" href="#" id="claim_btn" onClick="claimTalk(<?php echo $detail->tid; ?>)">Claim This Talk</a>	
<?php endif; ?>
</p>

<p class="ad">
    <script type="text/javascript"><!--
    google_ad_client = "pub-2135094760032194";
    /* 468x60, created 11/5/08 */
    google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>


<?php
$msg=$this->session->flashdata('msg');
if (!empty($msg)): ?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">


<?php
if(!$detail->allow_comments) {
	$this->load->view('msg_info', array('msg' => 'Comments closed.'));
	$comment_closed=true;
}
if (empty($comments)) {
?>
<?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
<?php

} else {
	
	// Sort out the votes from the comments
	$votes=array(); $for=0; $against=0;
	foreach($comments as $k=>$v){
		if($v->rating==1){ $against++; }elseif($v->rating==5){ $for++; }
		if($v->comment_type=='vote'){ $votes[]=$v; unset($comments[$k]); }
	}
	
	if(count($votes)){ 
		echo '<h2 id="comments">Votes '; 
		echo '<span style="font-size:12px;color:#898989">&nbsp;&nbsp;('.$for.' for / '.$against.' against)</span></h2>'; 
	}
	foreach($votes as $k=>$v){
		$uname 		= '<a href="/user/view/'.$v->user_id.'">'.escape($v->uname).'</a> ';
		$vote_str	=($v->rating==1) ? '-1 vote' : '+1 vote';
		?>
		<div>
			<div class="text">
			<p class="info">
				<a class="btn-small" href="#"><?php echo $vote_str; ?></a>&nbsp;
	    		<strong><?php echo date('M j, Y, H:i',$v->date_made); ?></strong> by <strong><?php echo $uname; ?></strong>
	    	</p>
			</div>
		</div>
		<?php
	}
	echo '<br/>';
	echo '<h2 id="comments">Comments</h2>';
	
    foreach ($comments as $k => $v) {
        if ($v->private && !$admin){ 
            continue; 
        }
    
        if (isset($v->user_id) && $v->user_id != 0){ 
    		$uname = '<a href="/user/view/'.$v->user_id.'">'.escape($v->uname).'</a> ';
    	}else{ 
    		$uname = '<span class="anonymous">Anonymous</span>'; 
    	}

    	$class = '';

    	if ($v->user_id == 0) {
    	    $class .= ' row-talk-comment-anonymous';
    	}

        if ($v->private == 1) {
    	    $class .= ' row-talk-comment-private';
    	}
    	
    	if (isset($claimed[0]->userid) && $claimed[0]->userid != 0 && isset($v->user_id) && $v->user_id == $claimed[0]->userid) {
    	    $class .= ' row-talk-comment-speaker';
    	}

?>
<div id="comment-<?php echo $v->ID ?>" class="row row-talk-comment<?php echo $class?>">
	<div class="img">
	<?php if (isset($claimed[0]->userid) && $claimed[0]->userid != 0 && isset($v->user_id) && $v->user_id == $claimed[0]->userid): ?>
		<span class="speaker">Speaker comment:</span>
	<?php else: ?>
		<?php echo rating_image($v->rating); ?>
	<?php endif; ?>
	</div>
	<div class="text">
    	<p class="info">
    		<strong><?php echo date('M j, Y, H:i',$v->date_made); ?></strong> by <strong><?php echo $uname; ?></strong>
    	<?php if ($v->private == 1): ?>
    		<span class="private">Private</span>
    	<?php endif; ?>
    	</p>
    	<div class="desc">
    		<?php echo auto_p(escape($v->comment)); ?>
    	</div>
		<p class="admin">
			<?php if (user_is_admin() || $v->user_id==$user_id): ?>
				<a class="btn-small" href="#" onClick="editTalkComment(<?=$v->ID?>);return false;">Edit</a>
			<?php endif; ?>
			<?php if (user_is_admin()): ?>
				<a class="btn-small" href="#" onClick="delTalkComment(<?=$v->ID?>);return false;">Delete</a>
			<?php endif; ?>
			<?php if (isset($claimed[0]->userid) && $claimed[0]->userid != 0 && isset($v->user_id) && $v->user_id == $claimed[0]->userid): ?>
				<a class="btn-small" href="#" onClick="commentIsSpam(<?=$v->ID?>,'talk');return false;">Is Spam</a>
			<?php endif; ?>
		</p>
		<?php if (user_is_admin()): ?>
		<p class="admin">
			
		</p>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
</div>
<?php
    }
}
?>
</div>
<?php
if(!$detail->allow_comments) {
?>
<p class="info">Currently not open for comment.</p>
<?php
} else {
    if (false && !$auth) {
?>
<p class="info">Want to comment on this talk? <a href="/user/login">Log in</a> or <a href="/user/register">create a new account</a>.</p>
<?php 
    } else {
	$title=($detail->event_voting=='Y' && !$detail->allow_comments) ? 'Cast your vote' : 'Write a comment';
?>
<a name="comment_form"></a>
<h3 id="comment-form"><?php echo $title; ?></h3>
<?php echo form_open('talk/view/'.$detail->tid . '#comment-form', array('class' => 'form-talk')); ?>

<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>

<?php
if($detail->event_voting=='Y' && !$detail->allow_comments){
	?>
	<div style="text-align:center" class="row row-buttons">
		<?php 
			if($user_attending){
				echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), '+1 vote'); echo '&nbsp;';
				echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), '-1 vote'); 
			}
		?><br/><br/>
			<span style="color:#3567AC;font-size:11px">You must be listed as attending the event 
			<a href="/event/view/<?php echo $detail->event_id; ?>"><?php echo $detail->event_name; ?></a> to vote on 
			this talk.</span>
	</div>
	<?php
}else{
?>

<div class="row">
	<?php echo form_hidden('edit_comment'); ?>
	<label for="comment">Comment</label>
	<?php 
    echo form_textarea(array(
		'name'	=> 'comment',
		'id'	=> 'comment',
		'value'	=> $this->validation->comment,
		'cols'	=> 40,
		'rows'	=> 10
    ));
    ?>
    <label class="checkbox">
        <?php echo form_checkbox('private','1'); ?>
        Mark as private?
    </label>
    <div class="clear"></div>
</div>
<?php if (isset($claimed[0]->userid) && $claimed[0]->userid != 0 && user_get_id() == $claimed[0]->userid): ?>
<?php else: ?>
<div class="row">
	<label for="rating">Rating</label>
	<div class="rating">
	    <?php echo rating_form('rating', $this->validation->rating); ?>
	</div>
	<div class="clear"></div>
</div>
<?php endif; ?>
<div class="row row-buttons">
	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit Comment'); ?>
</div>
<?php 
}
        echo form_close(); 
        /* close if for date */
    }
}
?>
