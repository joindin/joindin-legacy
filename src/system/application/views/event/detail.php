
<script type="text/javascript" src="/inc/js/event.js"></script>
<?php
menu_pagetitle('Event: ' . escape($event_detail->event_name));

// Load up our detail view
$data=array(
	'event_detail'	=> $event_detail,
	'attend'		=> $attend,
	'admins'		=> $admins
);
$this->load->view('event/modules/_event_detail',$data);

// These are our buttons below the event detail
$data=array(
	'admin'			=> $admin,
	'event_detail'	=> $event_detail
);
$this->load->view('event/modules/_event_buttons',$data);
?>

<!-- google ad -->
<p class="ad">
    <script type="text/javascript"><!--
    google_ad_client = "pub-2135094760032194";
    /* 468x60, created 11/5/08 */
    google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>
<!-- end google ad -->

<?php
$data=array(
	'talks'			=> $talks,
	'comments'		=> $comments,
	'slides_list'	=> $slides_list,
	'admin'			=> $admin,
	'tracks'		=> $tracks,	
	'talk_stats'	=> $talk_stats
);
$this->load->view('event/modules/_event_tabs',$data);
?>

<script type="text/javascript">
$(function() {
	$('#event-tabs').tabs();
	if (window.location.hash == '#comment-form') {
		$('#event-tabs').tabs('select', '#comments');
	} else {
	<?php if (count($talks) == 0): ?>
		$('#event-tabs').tabs('select', '#comments');
	<?php endif; ?>
	}
});
$(document).ready(function(){
	JI_event.init();
})
</script>
