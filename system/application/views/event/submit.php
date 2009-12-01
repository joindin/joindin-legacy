<?php 
menu_pagetitle('Submit an event');
?>
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
    	<label for="event_location">Event Location</label>
    	<?php echo form_input(array('name' => 'event_loc', 'id' => 'event_location'), $this->validation->event_loc); ?>
    
        <div class="clear"></div>
    </div>

	<div class="row">
    	<label for="event_stub">Event Stub</label>
    	<?php echo form_input(array('name' => 'event_stub', 'id' => 'event_stub'), $this->validation->event_stub); ?>
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
	
	<?php if($is_auth): ?>
	<div class="row">
		<label for="is_event_admin">Event Admin</label>
		<?php echo form_checkbox('is_admin','1',$this->validation->is_admin); ?> I'm an event admin!<br/>
		<span style="color:#3567AC;font-size:11px">If you're an organizer or an admin for this event, check the box above. As an admin you will be able to manage talks, approve claims, moderate comments, etc.</span><br/>
		<div class="clear"></div>
	</div>
	<?php endif; ?>
	
    <div class="row">
    	<label for="start">Event Start Date</label>
    	<?php
		/*foreach(range(1,12) as $v){ $start_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $start_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $start_yr[$v]=$v; }*/

    	foreach(range(1,12) as $v){ $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $start_day[$v]=sprintf('%02d', $v); }
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
		/*foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }*/

	    foreach(range(1,12) as $v){ $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $start_day[$v]=sprintf('%02d', $v); }
    	foreach(range(date('Y'),date('Y')+5) as $v){ $start_yr[$v]=$v; }

		echo form_dropdown('end_mo',$start_mo,$this->validation->end_mo);
		echo form_dropdown('end_day',$start_day,$this->validation->end_day);
		echo form_dropdown('end_yr',$start_yr,$this->validation->end_yr);
		?>
	 <div class="clear"></div>
    </div>
    
	<div class="row">
        <label for="start">Is the event private?</label>
		<?php
		echo form_radio('is_private','Y',$this->validation->is_private).' Yes'; 
		echo form_radio('is_private','N',$this->validation->is_private). 'No'; 
		?><br/>
		<span style="color:#3567AC;font-size:11px"><b>Private Events:</b> If a event is marked as private, it's an 
		invite-only event.</span><br/>
	</div>

	<div class="row">
		<span style="color:#3567AC;font-size:11px"><b>Call for Papers:</b> Are you opening up your conference to let people submit ideas? Use these dates to define the time period when they can submit! </span><br/>
		<?php 
			$js='onClick="toggleCfpDates()"';
			echo form_checkbox('is_cfp','1',$this->validation->cfp_checked,$js); 
		?> Yes, we're going to have a Call for Papers
		<br/><br/>
        <label for="start">Call for Papers Start Date</label>
	<?php
		/*foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }*/

	    foreach(range(1,12) as $v){ $cfp_start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $cfp_start_day[$v]=sprintf('%02d', $v); }
    	foreach(range(date('Y'),date('Y')+5) as $v){ $cfp_start_yr[$v]=$v; }

		$js=($this->validation->cfp_checked==1) ? '' : 'disabled';
		
		echo form_dropdown('cfp_start_mo',$cfp_start_mo,$this->validation->cfp_start_mo,'id="cfp_start_mo" '.$js);
		echo form_dropdown('cfp_start_day',$cfp_start_day,$this->validation->cfp_start_day,'id="cfp_start_day" '.$js);
		echo form_dropdown('cfp_start_yr',$cfp_start_yr,$this->validation->cfp_start_yr,'id="cfp_start_yr" '.$js);
		?>
	 <div class="clear"></div>
    </div>
 <div class="row">
        <label for="start">Call for Papers End Date</label>
	<?php
		/*foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }*/

	    foreach(range(1,12) as $v){ $cfp_end_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $cfp_end_day[$v]=sprintf('%02d', $v); }
    	foreach(range(date('Y'),date('Y')+5) as $v){ $cfp_end_yr[$v]=$v; }

		echo form_dropdown('cfp_end_mo',$cfp_end_mo,$this->validation->cfp_end_mo,'id="cfp_end_mo" '.$js);
		echo form_dropdown('cfp_end_day',$cfp_end_day,$this->validation->cfp_end_day,'id="cfp_end_day" '.$js);
		echo form_dropdown('cfp_end_yr',$cfp_end_yr,$this->validation->cfp_end_yr,'id="cfp_end_yr" '.$js);
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
