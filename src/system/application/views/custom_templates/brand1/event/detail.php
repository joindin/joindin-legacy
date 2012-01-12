
<script type="text/javascript" src="/inc/js/event.js"></script>
<?php
menu_pagetitle('Event: ' . escape($event_detail->event_name));

// Load up our detail view
$data=array(
    'event_detail'	=> $event_detail,
    'attend'		=> $attend
);
$this->load->view('event/modules/_event_detail', $data);

$data=array(
    'talks'			=> $talks,
    'comments'		=> $comments,
    'slides_list'	=> $slides_list,
    'admin'			=> $admin,
    'tracks'		=> $tracks
);
$this->load->view('event/modules/_event_tabs', $data);
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
$(document).ready(function() {
    JI_event.init();
})
</script>
