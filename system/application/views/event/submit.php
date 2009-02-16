<h1 class="icon-event">Submit an event</h1>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
    <?php echo form_open('event/submit', array('class' => 'form-event-submit')); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>
    
    <div class="row">
    	<label for="event_title">Event Title</label>
    	<?php echo form_input(array('name' => 'event_title', 'id' => 'event_title'), $this->validation->event_title); ?>
    
        <div class="clear"></div>
    </div>

	<div class="row">
    	<label for="event_title">Event Location</label>
    	<?php echo form_input(array('name' => 'event_loc', 'id' => 'event_title'), $this->validation->event_loc); ?>
    
        <div class="clear"></div>
    </div>

	<div class="row">
    	<label for="event_title">Event Stub</label>
    	<?php echo form_input(array('name' => 'event_stub', 'id' => 'event_title'), $this->validation->event_stub); ?>
    	<span style="color:#3567AC;font-size:11px">What's a <b>stub</b>? It's the "shortcut" part of the URL to help visitors get to your event faster. An example might be "phpevent" in the address "joind.in/event/phpevent". If no stub is given, you can still get to it via the event ID.</span>
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="event_contact_name">Event Contact Name</label>
    	<?php echo form_input(array('name' => 'event_contact_name', 'id' => 'event_contact_name'), $this->validation->event_contact_name); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="event_contact_email">Event Contact Email</label>
    	<?php echo form_input(array('name' => 'event_contact_email', 'id' => 'event_contact_email'), $this->validation->event_contact_email); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="start">Event Start Date</label>
    	<?php
		foreach(range(1,12) as $v){ $start_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $start_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $start_yr[$v]=$v; }
		echo form_dropdown('start_mo',$start_mo,$this->validation->start_mo);
		echo form_dropdown('start_day',$start_day,$this->validation->start_day);
		echo form_dropdown('start_yr',$start_yr,$this->validation->start_yr);
		?>
 	<div class="clear"></div>
    </div>
 <div class="row">
        <label for="start">Event End Date</label>
	<?php
		foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }
		echo form_dropdown('end_mo',$start_mo,$this->validation->end_mo);
		echo form_dropdown('end_day',$start_day,$this->validation->end_day);
		echo form_dropdown('end_yr',$start_yr,$this->validation->end_yr);
		?>
	 <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="event_desc">Event Description</label>
    	<?php 
		$attr=array(
			'name'	=> 'event_desc',
			'id'	=> 'event_desc',
			'cols'	=> 50,
			'rows'	=> 10,
			'value'	=> $this->validation->event_desc
		);
		echo form_textarea($attr); 
	    ?>
        <div class="clear"></div>
    </div>
	<div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit event'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>
