<?php
//submitting a new event
echo form_open('event/submit');
?>

<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09">Submit an Event</h1>
<?php 
echo $this->validation->error_string;
if(isset($msg)){ echo '<div class="notice">'.$msg.'</div>'; } echo '<br/>'; 
?>

<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Event Title:</td>
	<td><?php echo form_input('event_title',$this->validation->event_title); ?></td>
</tr>
<tr>
	<td class="title">Event Contact Name:</td>
	<td><?php echo form_input('event_contact_name',$this->validation->event_contact_name); ?></td>
</tr>
<tr>
	<td class="title">Event Contact Email:</td>
	<td><?php echo form_input('event_contact_email',$this->validation->event_contact_email); ?></td>
</tr>
<tr>
	<td class="title">Event Start Date:</td>
	<td>
		<?php
		foreach(range(1,12) as $v){ $start_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $start_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $start_yr[$v]=$v; }
		echo form_dropdown('start_mo',$start_mo,$this->validation->start_mo);
		echo form_dropdown('start_day',$start_day,$this->validation->start_day);
		echo form_dropdown('start_yr',$start_yr,$this->validation->start_yr);
		?>
	</td>
</tr>
<tr>
	<td class="title" valign="top">Event Description:</td>
	<td><?php 
		$attr=array(
			'name'	=> 'event_desc',
			'id'	=> 'event_desc',
			'cols'	=> 50,
			'rows'	=> 10,
			'value'	=> $this->validation->event_desc
		);
		echo form_textarea($attr); 
	?></td>
</tr>
<tr>
	<td align="right" colspan="2"><?php echo form_submit('sub','Submit Event'); ?></td>
</tr>
</table>
<?php echo form_close(); ?>
