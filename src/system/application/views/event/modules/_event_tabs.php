<?php
$cl=array();

foreach($claimed as $k=>$v){ 
	$cl[$v->rcode]=array('rid'=>$v->rid,'uid'=>$v->uid); 
}

// work through the talks list and split into days
$by_day=array();
foreach($talks as $t){
	$day = strtotime($t->display_date);
	$by_day[$day][]=$t;
}
ksort($by_day);
$ct=0;
?>
<div id="event-tabs">
	<ul>
		<li><a href="#talks">Talks (<?php echo count($talks)?>)</a></li>
		<li><a href="#comments">Comments (<?php echo $talk_stats['comments_total'] ?>)</a></li>
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
	$this->load->view('event/modules/_event_tab_talks',array(
		'by_day'	=> $by_day,
		'cl'		=> $cl,
		'ct'		=> $ct,
		'claims'	=> $claims
	));
	$this->load->view('event/modules/_event_tab_comments');
	if($admin){ $this->load->view('event/modules/_event_tab_admin',array(
		'talk_stats'=>$talk_stats)); }
	$this->load->view('event/modules/_event_tab_tracks');
	$this->load->view('event/modules/_event_tab_slides',array('ct'=>$ct));
	$this->load->view('event/modules/_event_tab_evtrelated');
	?>
</div>