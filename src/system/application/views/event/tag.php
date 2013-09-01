<h2>Tagged with "<?php echo escape($tagString); ?>"</h2>
<?php

foreach ($eventDetail as $event) {
    $this->load->view('event/_event-row', array('event'=>$event));
}
?>
<?php if (count($eventDetail)==0): ?>
    No events were found with this tag!<br/>
    <br/>
    Didn't find what you were looking for? Try our <a href="/event/all">full events list</a>!
<?php endif; 
