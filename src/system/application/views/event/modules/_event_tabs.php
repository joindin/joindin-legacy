<div id="event-tabs">
	<ul>
		<li><a href="#talks">Talks (<?php echo count($talks)?>)</a></li>
		<li><a href="#comments">Comments (<?php echo count($comments)?>)</a></li>
		<?php if(isset($evt_sessions) && count($evt_sessions)>0): ?>
			<li><a href="#evt_related">Event Related (<?php echo count($evt_sessions)?>)</a></li>
		<?php endif; ?>
		<li><a href="#slides">Slides (<?php echo count($slides_list)?>)</a></li>
		<?php if($admin): ?>
		<li><a href="#estats">Statistics</a></li>
		<?php endif; ?>
		<?php if(count($tracks)>0): ?>
			<li><a href="#tracks">Tracks (<?php echo count($tracks); ?>)</a></li>
		<?php endif; ?>
	</ul>
	<?php
	$this->load->view('event/modules/_event_tab_talks');
	$this->load->view('event/modules/_event_tab_comments');
	if($admin){ $this->load->view('event/modules/_event_tab_admin'); }
	$this->load->view('event/modules/_event_tab_tracks');
	$this->load->view('event/modules/_event_tab_slides');
	$this->load->view('event/modules/_event_tab_evtrelated');
	?>
</div>