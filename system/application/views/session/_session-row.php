<div class="row row-talk">
	<div class="img">
		<?= rating_image($session->getRating()); ?>
	</div>
	<div class="text">
    	<h3><a href="/session/view/<?= escape($session->getId()); ?>"><?= escape($session->getTitle()); ?></a></h3>
    	<p class="opts">
    		at <a href="/event/view/<?= escape($session->getEventId()); ?>"><?= escape($session->getEvent()->getTitle()); ?></a> |
    		<a href="/session/view/<?= escape($session->getId()); ?>#comments"><?= $session->getCommentCount(); ?> comment<?= $session->getCommentCount() == 1 ? '' : 's'?></a>
    	</p>
	</div>
	<div class="clear"></div>
</div>
