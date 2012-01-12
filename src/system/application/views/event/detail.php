<script type="text/javascript" src="/inc/js/jq.joindIn.js"></script>
<script type="text/javascript" src="/inc/js/event.js"></script>
<?php
menu_pagetitle('Event: ' . escape($event_detail->event_name));

// Load up our detail view
$data=array(
    'event_detail'	=> $event_detail,
    'attend'		=> $attend,
    'admins'		=> $admins
);
$this->load->view('event/modules/_event_detail', $data);

// These are our buttons below the event detail
$data=array(
    'admin'			=> $admin,
    'event_detail'	=> $event_detail
);
$this->load->view('event/modules/_event_buttons', $data);
?>

<?php
$data=array(
    'talks'			=> $talks,
    'comments'		=> $comments,
    'slides_list'	=> $slides_list,
    'admin'			=> $admin,
    'tracks'		=> $tracks,	
    'talk_stats'	=> $talk_stats,
    'event_detail'	=> $event_detail,
    'event_url'		=> '/event/view/'. $event_detail->ID.'/',
    'tab'			=> $tab
);


if ($prompt_event_comment)
{
        
    $this->load->view('event/_event_prompt_comment', array());
}


$this->load->view('event/modules/_event_tabs', $data);
?>

<script type="text/javascript">
(function($) {
    $(document).ready(function() {
        $('#event-tabs').joindIn_tabs();
        <?php if (count($talks) == 0): ?>
            $('a[rel=comments]').click();
            <?php endif; ?>
        if (window.location.hash == '#comment-form' || window.location.hash == '#comments') {
            window.location.hash = '#';
            $('a[rel=comments]').click();
        }
    });
})(jQuery);

function eventOpenTab(_rel)
{
    $('#event-tabs a[rel=' + _rel + ']').click();
}

JI_event.init();
</script>
