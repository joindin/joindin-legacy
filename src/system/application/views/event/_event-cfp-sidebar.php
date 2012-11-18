<?php
if (!empty($events)) {
    $cfp_data 	= '<ul>';
    foreach ($events as $event) {
        $cfp_data.='<li><a href="/event/view/'.$event->ID.'">'.$event->event_name.'</a>';
    }
    $cfp_data	.= '</ul>';
    $cfp_data	.= '<div style="padding-left:20px"><a href="/event/callforpapers">more...</a></div>';

    $this->template->write_view('sidebar2', 'main/_sidebar-block', array(
        'title'		=> 'Currently Open Call for Papers',
        'content'	=> $cfp_data
    ));
}
