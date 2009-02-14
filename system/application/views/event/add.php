<?php
//$tz_list=array('Select Continent');
//foreach($tz as $k=>$v){ $tz_list[(string)$v->offset]=floor((string)$v->offset/3600); }

$offset_list=array_merge(range(-12,12),range(0,12));
foreach($offset_list as $k=>$v){ 
	if($v<0){ 
		$tz_list[$v]='UTC '.$v.' hours'; 
	}elseif($v>0){
		$tz_list[$v]='UTC +'.$v.' hours'; 
	}else{ $tz_list[$v]='UTC 0 hours'; }
}

echo $this->validation->error_string;
if(isset($this->edit_id) && $this->edit_id){
	echo form_open_multipart('event/edit/'.$this->edit_id);
	$sub='Edit Event';
}else{ 
	echo form_open_multipart('event/add'); 
	$sub='Add Event';
}

if(isset($msg)){ echo '<div class="notice">'.$msg.'</div>'; }
?>

<table cellpadding="3" cellspacing="0" border="0" class="form_table">
<tr>
	<td class="title">Event Name:</td>
	<td><?php echo form_input('event_name',$this->validation->event_name); ?></td>
</tr>
<tr>
	<td class="title">Event Start:</td>
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
	<td class="title">Event End:</td>
	<td>
		<?php
		foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }
		echo form_dropdown('end_mo',$end_mo,$this->validation->end_mo);
		echo form_dropdown('end_day',$end_day,$this->validation->end_day);
		echo form_dropdown('end_yr',$end_yr,$this->validation->end_yr);
		?>
	</td>
</tr>
<tr>
	<td class="title">Event Location:</td>
	<td><?php echo form_input('event_loc',$this->validation->event_loc); ?></td>
</tr>
<tr>
	<td class="title">Event Timezone:</td>
	<td>
		<?php echo form_dropdown('event_tz',$tz_list,$this->validation->event_tz); ?>
	</td>
</tr>
<tr>
	<td valign="top" class="title">Event Description:</td>
	<td>
		<?php 
		$arr=array(
			'name'	=> 'event_desc',
			'cols'	=> 45,
			'rows'	=> 12,
			'value'	=> $this->validation->event_desc
		);
		echo form_textarea($arr); 
		?>
	</td>
</tr>
<tr>
	<td valign="top" class="title">Event Icon:</td>
	<td>
		<input type="file" name="event_icon" size="20" />
	</td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub',$sub); ?></td></tr>
</table>
<?php echo form_close(); ?>