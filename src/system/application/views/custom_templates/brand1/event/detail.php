
<script type="text/javascript" src="/inc/js/event.js"></script>
<?php
menu_pagetitle('Event: ' . escape($event_detail->event_name));

// Load up our detail view
$data=array(
	'event_detail'	=> $event_detail,
	'attend'		=> $attend
);
$this->load->view('event/modules/_event_detail',$data);
?>
