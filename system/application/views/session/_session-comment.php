<?php
/**
 * View file for a single comment on a session. A {@see SessionComment} has to 
 * be provided.
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */ 

$commentClass = '';

if($comment->isAnonymous()) {
    $commentClass .= ' row-talk-comment-anonymous';
}
if($comment->isPrivate()) {
    $commentClass .= ' row-talk-comment-private';
}

?>

<div id="comment-<?= $comment->getId() ?>" class="row row-talk-comment<?= $commentClass ?>">

    <div class="img">
	<?php if($comment->isSpeakerComment()): ?>
		<span class="speaker">Speaker comment:</span>
	<?php else: ?>
		<?= rating_image($comment->getRating()); ?>
	<?php endif; ?>
	</div>

    <div class="text">
    	<p class="info">
    		<strong><?= date('M j, Y, H:i', $comment->getDate()); ?></strong> 
    		by <strong><?= $comment->getAuthorName(); ?></strong>
    	    <?php if ($comment->isPrivate()): ?>
    		<span class="private">Private</span>
    	    <?php endif; ?>
    	    <?php if (user_is_administrator()): ?>
		    <span style="margin-left: 10px;">
		        (<?= delete_link(
    				'/session/deletecomment/' . $comment->getId(), 
    				"Are you sure you want to delete the comment by " . $comment->getAuthorName() . "?") 
    			?>)
		    </span>
	        <?php endif; ?>
    	</p>
    	<div class="desc">
    		<?php echo auto_p(escape($comment->getComment())); ?>
    	</div>
	</div>
	<div class="clear"></div>
</div>
