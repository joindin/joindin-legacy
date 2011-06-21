<h2>Tagged with "<?php echo $tagString; ?>"</h2>
<?php

foreach($eventDetail as $event){
    $eventData->is_cfp = true;
	$this->load->view('event/_event-row', array('event'=>$event));
}

?>