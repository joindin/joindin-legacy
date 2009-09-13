<?php menu_pagetitle('Blog: ' . escape($post->getTitle())); ?>

<div class="detail">

	<h1><?= $post->getTitle() ?></h1>

	<p class="info">
		Written <strong><?= date('M j, Y',$post->getDate()) ?></strong> 
		at <strong><?= date('H:i',$post->getDate()) ?></strong> 
		(<?= $post->getAuthor() ?>)
	</p>

	<div class="desc">
		<?= auto_p(auto_link($post->getContent())); ?>
	</div>
</div>

<?php if(user_is_admin()): ?>
<p class="admin">
	<a class="btn-small" href="/blog/edit/<?= $post->getId() ?>">Edit post</a>	
	<a class="btn-small" href="/blog/delete/<?= $post->getId() ?>">Delete post</a>
</p>
<?php endif; ?>

<?php
if(empty($msg)) { $msg = $this->session->flashdata('msg'); }
if (!empty($msg)){ 
    $this->load->view('message/info', array('message' => $msg));
}
?>

<div class="box">

    <h2 id="comments">Comments</h2>

<?php 
if ($post->getCommentCount() == 0) {
    $this->load->view('message/info', array('message' => 'No comments yet.'));
} else {

    foreach ($post->getComments() as $blogComment) {
        if(null !== $blogComment->getAuthor()) {
            $usernameHtml = '<a href="/user/view/' . $blogComment->getAuthor()->getId() . '">' . $blogComment->getAuthor()->getUsername() . '</a>';
            $usernameCssClass = '';
        }
        else {
            $usernameHtml = '<span class="anonymous">' . $blogComment->getAuthorName() . '</span>';
            $usernameCssClass = 'row-blog-comment-anonymous';
        }

?>
    <div id="comment-<?= $blogComment->getId() ?>" class="row row-blog-comment<?= $usernameCssClass ?>">
        <p class="info">
        	On <strong><?= date('M j, Y', $blogComment->getDate()) ?></strong> 
        	at <strong><?= date('H:i', $blogComment->getDate()) ?></strong> 
        	by <strong><?= $usernameHtml; ?></strong>
        </p>
        <div class="desc">
        	<?= auto_p(escape(trim($blogComment->getComment()))) ?>
        </div>
        <?php if (user_is_admin()): ?>
        <p class="admin">
            <a class="btn-small" href="#" onClick="delBlogComment(<?= $blogComment->getId() ?>);return false;">Mark as Spam</a>
		    <a class="btn-small" href="#" onClick="delBlogComment(<?= $blogComment->getId() ?>);return false;">Delete</a>
	    </p>
	    <?php endif; ?>

	    <div class="clear"></div>
    </div>
<<<<<<< HEAD:system/application/views/blog/view.php
    <?php if (user_is_admin()): ?>
    <p class="admin">
		<a class="btn-small" href="#" onClick="delBlogComment(<?=$v->ID?>);return false;">Delete</a>
	</p>
	<?php endif; ?>

	<div class="clear"></div>
</div>
=======
>>>>>>> orange_refactor:system/application/views/blog/view.php
<?php
    }
}
?>
</div>

<h3 id="comment-form">Write a comment</h3>
<?= form_open('blog/view/' . $post->getId() . '#comment-form', array('class' => 'form-blog')); ?>

<?php if (isset($error) && !empty($error)): ?>
    <?php $this->load->view('message/error', array('message' => $error)); ?>
<?php endif; ?>

<div class="row">
	<label for="comment">Name</label>
	<?php 
	if(user_is_authenticated()) {
	    echo user_get_displayname();
	} 
	else {
        $options = array(
		    'name' => 'author_name',
		    'id' => 'author_name',
		    'size' => 30,
		    'value' => (isset($comment) ? $comment->getAuthorName() : '')
	    );
	    echo form_input($options);
	}
    ?>
    <div class="clear"></div>
</div>
<div class="row">
	<label for="comment">Comment</label>
	<?php 
    $options = array(
		'name' => 'comment',
		'id' => 'comment',
		'cols' => 40,
		'rows' => 9,
		'value'	=> (isset($comment) ? $comment->getComment() : '')
	);
	echo form_textarea($options); 
    ?>
    <div class="clear"></div>
</div>
<div class="row row-buttons">
	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Make Comment'); ?>
</div>
<?php  echo form_close(); ?>
