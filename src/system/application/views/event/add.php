<?php
//$tz_list=array('Select Continent');
//foreach($tz as $k=>$v){ $tz_list[(string)$v->offset]=floor((string)$v->offset/3600); }

echo $this->validation->error_string;
if(isset($this->edit_id) && $this->edit_id){
	echo form_open_multipart('event/edit/'.$this->edit_id);
	$sub='Save Edits';
	$title='Edit Event: <a style="text-decoration:none" href="/event/view/'.$detail[0]->ID.'">'.$detail[0]->event_name.'</a>';
	$curr_img=$detail[0]->event_icon;
	menu_pagetitle('Edit Event: '.$detail[0]->event_name);
}else{ 
	echo form_open_multipart('event/add'); 
	$sub	= 'Add Event';
	$title	= 'Add Event';
	$curr_img='none.gif';
	menu_pagetitle('Add an Event');
}

echo '<h2>'.$title.'</h2>';
?>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
    <div class="row">
    	<label for="event_name">Event Name:</label>
	<?php echo form_input('event_name',$this->validation->event_name); ?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_start">Event Start:</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $start_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $start_day[$v]=$v; }
	foreach(range($min_start_yr,date('Y')+5) as $v){ $start_yr[$v]=$v; }
	echo form_dropdown('start_mo',$start_mo,$this->validation->start_mo);
	echo form_dropdown('start_day',$start_day,$this->validation->start_day);
	echo form_dropdown('start_yr',$start_yr,$this->validation->start_yr);
	?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_end">Event End:</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $end_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $end_day[$v]=$v; }
	foreach(range($min_end_yr,date('Y')+5) as $v){ $end_yr[$v]=$v; }
	echo form_dropdown('end_mo',$end_mo,$this->validation->end_mo);
	echo form_dropdown('end_day',$end_day,$this->validation->end_day);
	echo form_dropdown('end_yr',$end_yr,$this->validation->end_yr);
	?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_location">Event Location:</label>
	<?php echo form_input('event_loc',$this->validation->event_loc); ?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_timezone">Event Timezone:</label>
		<?php echo custom_timezone_menu('event_tz', $this->validation->event_tz_cont, $this->validation->event_tz_place ); ?>
	<span style="color:#3567AC;font-size:11px">For more information on locations and 
	their time zone, see <a href="http://en.wikipedia.org/wiki/List_of_time_zones">this
	page on Wikipedia</a></span>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_description">Event Description:</label>
	<?php
	$arr=array(
		'name'	=> 'event_desc',
		'cols'	=> 45,
		'rows'	=> 12,
		'value'	=> $this->validation->event_desc
	);
	echo form_textarea($arr);
	?>
    </div>
    <div class="clear"></div>
	<div class="row">
    	<label for="event_stub">Event Stub</label>
    	<?php echo form_input(array('name' => 'event_stub', 'id' => 'event_stub'), $this->validation->event_stub); ?>
    	<span style="color:#3567AC;font-size:11px">What's a <b>stub</b>? It's the "shortcut" part of the URL to help visitors get to your event faster. An example might be "phpevent" in the address "joind.in/event/phpevent". If no stub is given, you can still get to it via the event ID.</span>
        <div class="clear"></div>
    </div>
	<div class="clear"></div>
	<div class="row">
	<label for="event_icon">Allow Voting?</label>
	<?php 
		$ev=($this->validation->event_voting=='Y') ? true : false;
		echo form_checkbox('event_voting','Y',$ev); 
	?><br/>
	<span style="color:#3567AC;font-size:11px">
		If you'd like to allow voting on event sessions, check here to turn this feature on 
		(useful for things like Unconferences). This can be enabled at any time, but comments will
		only count as "votes" prior to the start of the session.
	</span>
	</div>
    <div class="clear"></div>
	<div class="row">
	<label for="event_icon">Is event private?</label>
	<?php
		$ev_y=($this->validation->event_private=='Y') ? true : false;
		$ev_n=($this->validation->event_private=='N') ? true : false;
		if(empty($this->validation->event_private)){ $ev_n=true; }

		echo form_radio('event_private','Y',$ev_y).' Yes'; 
		echo form_radio('event_private','N',$ev_n).' No'; 
	?>
	</div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_icon">Event Icon:</label>
	<input type="file" name="event_icon" size="20" /><br/><br/>
	<img src="/inc/img/event_icons/<?php echo $curr_img; ?>"/>
	<span style="color:#3567AC;font-size:11px">
		<b>Please Note:</b> Only icons that are 90 pixels by 90 pixels will be accepted!<br/>
		Allowed types: gif, jpg, png
	</span>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_link">Event Link(s):</label>
	<?php echo form_input('event_href',$this->validation->event_href); ?><br/>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_hashtag">Event Hashtag(s):</label>
	<?php echo form_input('event_hashtag',$this->validation->event_hashtag); ?>
	<span style="color:#3567AC;font-size:11px">Seperate tags with commas</span>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<?php echo form_submit('sub',$sub); ?>
    </div>
</div>
<?php echo form_close(); ?>
