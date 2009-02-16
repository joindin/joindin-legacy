<?php 
$v=$details[0];
?>
<div class="detail">

	<h1><?=$v->title?></h1>

	<p class="info">
		Written <strong><?php echo date('M j, Y',$v->date_posted); ?></strong> at <strong><?php echo date('H:i',$v->date_posted); ?></strong> (<?php echo $v->author_id; ?>)
	</p>

	<div class="desc">
		<? echo nl2br($v->content); ?>
	</div>
</div>

<?php if(user_is_admin()): ?>
<p class="admin">
	<a class="btn-small" href="/blog/edit/<?php echo $v->ID; ?>">Edit post</a>	
	<a class="btn-small" href="">Delete post</a>
</p>
<?php endif; ?>

<?php
if (empty($msg)) {
	$msg=$this->session->flashdata('msg');
}
if (!empty($msg)): 
?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">

<h2 id="comments">Comments</h2>

<?php

if (empty($comments)) {
?>
<?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
<?php
    
} else {

    foreach ($comments as $k => $v) {
        if (isset($v->author_id) && $v->author_id != 0){ 
    		$uname = '<a href="/user/view/'.$v->author_id.'">'.$v->uname.'</a> ';
    	}else{ 
    		$uname = '<span class="anonymous">Anonymous</span>'; 
    	}

    	$class = '';

    	if ($v->author_id == 0) {
    	    $class .= ' row-blog-comment-anonymous';
    	}

?>
<div id="comment-<?php echo $v->ID ?>" class="row row-blog-comment<?php echo $class?>">
    <p class="info">
    	<strong><?php echo htmlspecialchars($v->title); ?></strong> by <strong><?php echo $uname; ?></strong>
    </p>
    <div class="desc">
    	<?php echo nl2br(htmlspecialchars($v->content)); ?>
    </div>
    <p class="admin">
		<a class="btn-small" href="">Delete</a>
	</p>

	<div class="clear"></div>
</div>
<?php
    }
}
?>
</div>

<h3 id="comment-form">Write a comment</h3>
<?php echo form_open('blog/view/'.$pid . '#comment-form', array('class' => 'form-blog')); ?>

<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>

<div class="row">
	<label for="comment">Title</label>
	<?php 
    $p=array(
		'name'	=>'title',
		'id'	=>'title',
		'size'	=>30,
		'value'	=>$this->validation->title
	);
	echo form_input($p);
    ?>
    <div class="clear"></div>
</div>
<div class="row">
	<label for="comment">Comment</label>
	<?php 
    $p=array(
		'name'	=>'comment',
		'id'	=>'comment',
		'cols'	=>40,
		'rows'	=>9,
		'value'	=>$this->validation->comment
	);
	echo form_textarea($p); 
    ?>
    <div class="clear"></div>
</div>
<div class="row row-buttons">
	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Make Comment'); ?>
</div>
<?php  echo form_close(); ?>
