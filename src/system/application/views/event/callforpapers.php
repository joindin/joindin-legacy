<?php menu_pagetitle('Open Call for Papers'); ?>

<h2>Open Call For Papers</h2>

<?php
foreach ($current_cfp as $eventData) {
    $eventData->is_cfp = true;
    $this->load->view('event/_event-row', array('event'=>$eventData));
}
