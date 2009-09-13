<?php menu_pagetitle('Blog'); ?>

<h1 class="icon-event">
	<span style="float:left">Blog</span>
	<?php if(user_is_admin()){ ?>
	<a class="btn" style="float:right" href="/blog/add">Add blog post</a>
    <?php } ?>
	<div class="clear"></div>
</h1>

<?php
    // check the flash messages
    $message = $this->session->flashdata('message');
    if(!empty($message)) {
        $this->load->view('message/info', array('message' => $message));
    }
    $error = $this->session->flashdata('error');
    if(!empty($error)) {
        $this->load->view('message/error', array('message' => $error));
    }
?>

<?php if(count($posts) > 0): ?>
	<?php foreach($posts as $post): ?>
    <div class="row row-blog">
        <h2 class="h3"><a href="/blog/view/<?= $post->getId() ?>"><?= $post->getTitle() ?></a></h2>
        <div class="desc">
        	<?php echo auto_p(auto_link($post->getContent())); ?>
        </div>
        <p class="opts">
        	<a href="/blog/view/<?= $post->getId() ?>#comments"><?= $post->getCommentCount() ?> comment<?= $post->getCommentCount() == 1 ? '' : 's'?></a> |
        	 Written <strong><?= date('M j, Y',$post->getDate()) ?></strong> 
        	 at <strong><?= date('H:i',$post->getDate()); ?></strong> 
        	 (<?= $post->getAuthor() ?>)
        </p>
	
	    <?php if(user_is_admin()): ?>
	    <div class="admin">
		    <a class="btn-small" href="/blog/edit/<?= $post->getId() ?>">Edit</a>
		    <a class="btn-small" href="/blog/delete/<?= $post->getId() ?>">Delete</a>
	    </div>
	    <?php endif; ?>
	    <div class="clear"></div>
    </div>
	<?php endforeach; ?>
<?php else: ?>
	<?php $this->load->view('msg_info', array('msg' => 'No posts yet! Come back soon!')); ?>
<?php endif; ?>
