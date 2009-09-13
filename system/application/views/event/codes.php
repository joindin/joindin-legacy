<?php 
menu_pagetitle('Send Codes: ' . escape($details[0]->event_name));
?>
<?php
menu_pagetitle('Session Codes: ' . escape($event->getTitle()));

$this->load->view('sidebar/claim-session.php');
?>

<div class="box">
	
	<h1 class="icon-event">Session Codes: <?= escape($event->getTitle())?></h1>
	<?php $this->load->view('message/flash'); ?>
	<p>
		To claim their talks, speakers will need the codes below. To send the codes, put the speaker's email address in the field and check the box to signify you want to send to them. If there are multiple speakers for a talk, seperate the addresses with a comma and an email will be sent to both.
	</p>
	
	<form action="/event/codes/<?= $event->getId(); ?>" method="post">
	<h2>Unclaimed sessions</h2>
	<?php $this->load->view('message/area'); ?>
	<table class="data-table">
		<thead>
			<tr>
				<td>Session</td>
				<td>Speaker(s)</td>
				<td>Code</td>
				<td>Email</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach($event->getSessions() as $session) : if(!$session->isClaimed()) : ?>
			<tr>
				<td><a href="/session/view/<?= $session->getId(); ?>" target="_blank"><?= escape($session->getTitle()); ?></td>
				<td><?= escape($session->getSpeakerName()) ?></td>
				<td>
					<?php if('' == $session->getClaimToken()): ?>
					<em>Generate</em>
					<?php else : ?>
					<?= $session->getClaimToken(); ?>
					<?php endif; ?>
				</td>
				<td>
					<input type="checkbox" name="send[<?= $session->getId() ?>]" <?= isset($email[$session->getId()]) ? 'checked="checked"' : ''; ?> style="vertical-align: middle; display: inline;"/>&nbsp;
					<input type="text" name="email[<?= $session->getId() ?>]" value="<?= isset($email[$session->getId()]) ? $email[$session->getId()] : ''; ?>" style="display: inline; width: 200px;" />
				</td>
			</tr>
		<?php endif; endforeach; ?>
		</tbody>
	</table>
	
	<input class="btn" type="submit" value="Send Codes" />
	&nbsp;or&nbsp;<a href="/event/view/<?= $event->getId(); ?>">cancel</a>
	</form>
	
	<br />
	
	<h2>Claimed sessions</h2>
	<table class="data-table">
		<thead>
			<tr>
				<td>Session</td>
				<td>Speaker(s)</td>
				<td>Claimed by</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach($event->getSessions() as $session) : if($session->isClaimed()) : ?>
			<tr>
				<td><a href="/session/view/<?= $session->getId(); ?>" target="_blank"><?= escape($session->getTitle()); ?></a></td>
				<td><?= escape($session->getSpeakerName()); ?></td>
				<td>
					<?php $user = $session->getTalk()->getSpeaker()->getUser(); ?>
					<a href="/user/view/<?= $user->getId(); ?>">
						<?= escape($user->getDisplayName()); ?> (<?= escape($user->getUsername()); ?>)
					</a>
				</td>
			</tr>
		<?php endif; endforeach; ?>
		</tbody>
	</table>
</div>