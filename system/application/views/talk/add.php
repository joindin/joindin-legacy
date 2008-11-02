<?php
$event_list=array(); 
//echo '<pre>'; print_r($events); echo '</pre>';

foreach($events as $k=>$v){
	$event_list[$v->ID]=$v->event_name.' ('.date('m.d.Y',$v->event_start).'-'.date('m.d.Y',$v->event_end).')';
}

echo $this->validation->error_string;

if(isset($this->edit_id)){
	echo form_open('talk/edit/'.$this->edit_id);
	$sub='Edit Talk';
}else{ 
	echo form_open('talk/add');
	$sub='Add Talk';
}
echo '<h2>'.$sub.'</h2>';
?>

<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Event:</td>
	<td>
		<?php echo form_dropdown('event_id',$event_list,$this->validation->event_id); ?>
	</td>
</tr>
<tr>
	<td class="title">Talk title:</td>
	<td><?php echo form_input('talk_title',$this->validation->talk_title);?></td>
</tr>
<tr>
	<td class="title">Speaker:</td>
	<td><?php echo form_input('speaker',$this->validation->speaker);?></td>
</tr>
<tr>
	<td class="title">Date Given:</td>
	<td>
		<?php
		foreach(range(1,12) as $v){ $given_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $given_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $given_yr[$v]=$v; }
		echo form_dropdown('given_mo',$given_mo,$this->validation->given_mo);
		echo form_dropdown('given_day',$given_day,$this->validation->given_day);
		echo form_dropdown('given_yr',$given_yr,$this->validation->given_yr);
		?>
	</td>
</tr>
<tr>
	<td valign="top" class="title">Talk Description:</td>
	<td>
		<?php 
		$arr=array(
			'name'=>'talk_desc',
			'value'=>$this->validation->talk_desc,
			'cols'=>40,
			'rows'=>10
		);
		echo form_textarea($arr);
		?>
	</td>
</tr>
<tr>
	<td class="title">Slides link:</td>
	<td><?php echo form_input('slides_link',$this->validation->slides_link); ?></td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub',$sub); ?></td></tr>
</table>

<?php form_close(); ?>