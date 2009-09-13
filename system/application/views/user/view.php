<h1>
<?php
    if($user->getDisplayName() !== '') {
        echo "{$user->getDisplayName()} ({$user->getUsername()})";
    }
    else {
        echo $user->getUsername();
    }
?>
</h1>

<div class="box">
    <h2>Session</h2>
    <?php if (count($user->getSessions()) == 0): ?>
	<p>This user did not give any sessions</p>
    <?php else: ?>
    <?php
        foreach($user->getSessions() as $session){
        	$this->load->view('session/_session-row', array('talk' => $session));
        }
    ?>
    <?php endif; ?>
</div>

<div class="box">
    <h2>Comments</h2>
    <?php if (count($user->getSessionComments()) == 0): ?>
	<p>This user did not place any comments yet</p>
    <?php else: foreach($user->getSessionComments() as $comment): ?>
    <div class="row">
    	<strong>
    	    <a href="/session/view/<?= $comment->getSessionId() ?>#comment-<?= $comment->getId() ?>">
    	        <?= escape($comment->getSessionTitle()) ?>
    	    </a>
    	</strong>
    	<div class="clear"></div>
    </div>
    <?php endforeach; endif; ?>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td style="width: 48%;">
		<div class="box">
			<h2>Events They'll Be At</h2>
			<?php if(count($futureEventsAttending) === 0) : ?>
			<p>No events so far</p>
			<?php else: foreach($futureEventsAttending as $event) : ?>
			<div class="row">
		    	<strong><a href="/event/view/<?= $event->getId() ?>"><?= escape($event->getTitle()) ?></a></strong>
				<?= date('M d, Y',$event->getStart()); ?>
				<?php if(($user->getId() != user_get_id()) && ($event->userIsAttendee(user_get_id()))) : ?>
				<br/><span style=\"color:#92C53E;font-size:11px\">you'll be there as well!</span>
				<?php endif; ?>
		    	<div class="clear"></div>
		    </div>
			<?php endforeach; endif; ?>
		</div>
	</td>
	<td>&nbsp;</td>
	<td style="width: 48%;">
		<div class="box">
			<h2>Events They Were At</h2>
			<?php if(count($pastEventsAttended) === 0) : ?>
			<p>No events so far</p>
			<?php else: foreach($pastEventsAttended as $event) : ?>
			<div class="row">
		    	<strong><a href="/event/view/<?= $event->getId() ?>"><?= escape($event->getTitle()) ?></a></strong>
				<?= date('M d, Y',$event->getStart()); ?>
				<?php if(($user->getId() != user_get_id()) && ($event->userIsAttendee(user_get_id()))) : ?>
				<br/><span style=\"color:#92C53E;font-size:11px\">you were there as well!</span>
				<?php endif; ?>
		    	<div class="clear"></div>
		    </div>
			<?php endforeach; endif; ?>
		</div>
	</td>
</tr>
</table>
