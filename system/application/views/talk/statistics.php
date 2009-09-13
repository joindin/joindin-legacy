<?php
menu_pagetitle('Speaker: talk statistics');
// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li><a href="/talk/view/<?= $talk->getId(); ?>">Talk details</a></li>
        <li><a href="/talk/sessions/<?= $talk->getId(); ?>">Talk sessions</a></li>
        <li class="active"><a href="/talk/statistics/<?= $talk->getId(); ?>">Talk statistics</a></li>
		<li><a href="/talk/access/<?= $talk->getId(); ?>">Talk access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<div class="box">

	<h1 class="blue"><?= escape($talk->getTitle()); ?></h1>
	
	<table class="data-table">
		<tbody>
			<tr>
				<td colspan="2" class="divider">General</td>
			</tr>
			<tr>
				<td class="sub">Overal rating</td>
				<td>
					<?= rating_image($talk->getRating()); ?>
				</td>
			</tr>
			<tr>
				<td class="sub">Number of sessions</td>
				<td>
					<?= $talk->getSessionCount(); ?>&nbsp;
					<?= ($talk->getSessionCount() == 1) ? 'session' : 'sessions'; ?>
				</td>
			</tr>
			<tr>
				<td class="sub">Total number of comments</td>
				<td>
					
				</td>
			</tr>
			<!-- -->
			<tr>
				<td colspan="2" class="divider">Sessions</td>
			</tr>
			<tr>
				<td class="sub">Best session</td>
				<td>
					
				</td>
			</tr>
			<tr>
				<td class="sub">Worst session</td>
				<td>
					
				</td>
			</tr>
			<!-- -->
			<tr>
				<td colspan="2" class="divider">Comments</td>
			</tr>
			<tr>
				<td class="sub">Anonymous comments</td>
				<td>
					
				</td>
			</tr>
			<tr>
				<td class="sub">Private comments</td>
				<td>
					
				</td>
			</tr>
			<tr>
				<td class="sub">Unique commenters</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="sub">Top commenter</td>
				<td>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="bluebar right">
		<a href="/speaker/talks">back to talks</a>
	</div>
	
</div>