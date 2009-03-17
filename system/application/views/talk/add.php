<?php 
menu_pagetitle('Add Talk');
?>
<?php
$event_list	= array(); 
$cat_list	= array();
$lang_list	= array();

//echo '<pre>'; print_r($cats); echo '</pre>';
$ev=$events[0];
foreach($cats as $k=>$v){ $cat_list[$v->ID]=$v->cat_title; }
foreach($langs as $k=>$v){ $lang_list[$v->ID]=$v->lang_name; }

echo $this->validation->error_string;

if(isset($this->edit_id)){
	echo form_open('talk/edit/'.$this->edit_id);
	$sub='Edit Talk';
}else{ 
	echo form_open('talk/add/event/'.$ev->ID);
	$sub='Add Talk';
}
echo '<h2>'.$sub.'</h2>';

if(isset($msg) && !empty($msg)){ $this->load->view('msg_info', array('msg' => $msg)); }
if(isset($err) && !empty($err)){ $this->load->view('msg_info', array('msg' => $err)); }
?>

<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Event:</td>
	<td>
		<?php 
		echo form_hidden('event_id',$ev->ID);
		echo '<b>'.escape($ev->event_name).' ('.date('m.d.Y',$ev->event_start).'-'.date('m.d.Y',$ev->event_end).')</b>';
		?>
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
		foreach(range(2007,date('Y')+5) as $v){ $given_yr[$v]=$v; }
		echo form_dropdown('given_mo',$given_mo,$this->validation->given_mo);
		echo form_dropdown('given_day',$given_day,$this->validation->given_day);
		echo form_dropdown('given_yr',$given_yr,$this->validation->given_yr);
		?>
	</td>
</tr>
<tr>
	<td class="title">Session Type:</td>
	<td>
		<?php echo form_dropdown('session_type',$cat_list,$this->validation->session_type); ?>
	</td>
</tr>
<tr>
	<td class="title">Session Language:</td>
	<td>
		<?php echo form_dropdown('session_lang',$lang_list,$this->validation->session_lang); ?>
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