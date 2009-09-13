<?php menu_pagetitle('Submit an event'); ?>

<h1 class="icon-event">Submit an event</h1>

<?php if(isset($message) && !empty($message)) {
    $this->load->view('message/info', array('message' => $message));
} ?>

<div class="box">
    <?php 
        echo form_open('event/submit', array('class' => 'form-event-submit'));
        
        if(isset($error) && !empty($error)) {
            $this->load->view('message/error', array('message' => $error));
        }
    ?>    
    <div class="row">
    	<label for="event_title">Title</label>
    	<?php echo form_input(array('name' => 'title', 'id' => 'title'), $event->getTitle()); ?>
    
        <div class="clear"></div>
    </div>

	<div class="row">
    	<label for="event_location">Location</label>
    	<?php echo form_input(array('name' => 'location', 'id' => 'location'), $event->getLocation()); ?>
    
        <div class="clear"></div>
    </div>

	<div class="row">
    	<label for="event_stub">Stub</label>
    	<span style="color:#3567AC;font-size:11px">What's a <b>stub</b>? It's the "shortcut" part of the URL to help visitors get to your event faster. An example might be "phpevent" in the address "joind.in/event/phpevent". If no stub is given, you can still get to it via the event ID.</span>
    	<?php echo form_input(array('name' => 'stub', 'id' => 'stub'), $event->getStub()); ?>
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="event_contact_name">Contact Name</label>
    	<?php echo form_input(array('name' => 'contact_name', 'id' => 'contact_name'), $event->getContactName()); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="event_contact_email">Contact Email</label>
    	<?php echo form_input(array('name' => 'contact_email', 'id' => 'contact_email'), $event->getContactEmail()); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row" style="float: left; width: 40%; border-bottom: 0; margin-bottom: 0;">
    	<label for="start">Start Date</label>
        <small>Start date for the event (mm/dd/yyyy).</small>
        <input type="text" id="start_string" name="start_string" class="datepicker" value="<?= ($event->getStart() != '') ? date('m/d/Y', $event->getStart()) : '' ?>" />
        <script type="text/javascript">
            $(document).ready(function(){
                $("#start_string").datepicker();
            });
        </script>
 	<div class="clear"></div>
    </div>
    
     <div class="row" style="float: right; width: 40%; border-bottom: 0; margin-bottom: 0;">
        <label for="end">End Date</label>
        <small>End date for the event (mm/dd/yyyy).</small>
        <input type="text" id="end_string" name="end_string" class="datepicker" value="<?= ($event->getEnd() != '') ? date('m/d/Y', $event->getEnd()) : '' ?>" />
        <script type="text/javascript">
            $(document).ready(function(){
                $("#end_string").datepicker();
            });
        </script>
	
	 <div class="clear"></div>
    </div>
    
	<div class="row">
		<div class="clear"></div>
	</div>
	
    <div class="row">
    	<label for="description">Description</label>
    	<?= form_textarea(array(
    	    'name' => 'description',
			'id' => 'description',
			'cols' => 50,
			'rows' => 10,
			'value'	=> $event->getDescription()
    	)) ?>
        <div class="clear"></div>
    </div>
	
	<!--
	<div class="row">
		<label for="hashtag">Hashtag</label>
		<?= form_input(array(
			'id' => 'hashtag',
			'name' => 'hashtag',
			'value' => $event->getHashtag(),
			'style' => 'width: 150px'
		)); ?>
		<div class="clear"></div>
	</div>
	
	<div class="row">
		<label for="link">Link</label>
		<?= form_input('link'); ?>
		<div class="clear"></div>
	</div>
    -->
	<div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit event'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>
