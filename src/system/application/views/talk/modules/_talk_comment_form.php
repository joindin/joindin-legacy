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
	$title=($detail->event_voting=='Y' && $detail->event_start>time()) ? 'Cast your vote' : 'Write a comment';
?>
<a name="comment_form"></a>
<h3 id="comment-form"><?php echo $title; ?></h3>
<?php echo form_open('talk/view/'.$detail->tid . '#comment-form', array('class' => 'form-talk')); ?>

<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>

<?php
if($detail->event_voting=='Y' && $detail->event_start>time()){
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

	<?php if(!user_is_auth()){
		$this->load->view('msg_error', array('msg'=>
			'Please note: you are <b>not logged in</b> and will be posting anonymously!'
		)); 
	}
	?>

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