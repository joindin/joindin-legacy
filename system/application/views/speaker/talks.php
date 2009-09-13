<?php
// Load some sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');

$this->load->view('message/area');
$this->load->view('message/flash');
?>

<h1>My Talks</h2>

<div class="box">
	<table class="data-table">
		<tbody>
		<?php foreach($speaker->getTalks() as $talk) : ?>
			<tr>
				<td><a href="/talk/view/<?= $talk->getId() ?>"><?= escape($talk->getTitle()) ?></a>
				<td style="width: 250px;">
					<?php
						$rating = min(5, ceil((float) $talk->getRating()));
						$sessionCount = count($talk->getSessions());
					?>
					<img class="rating rating-<?= $rating ?> middle" src="/inc/img/rating-<?= $rating ?>.gif" alt="Rating: <?= $rating ?> of 5"/>
					&nbsp;(<?= $sessionCount ?> <?= (($sessionCount != 1) ? 'Sessions' : 'Session') ?>)
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<a href="/talk/add" class="btn btn-green">Add Talk</a>
</div>
