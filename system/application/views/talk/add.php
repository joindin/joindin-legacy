<?php
$event_list	= array(); 
$cat_list	= array();
$lang_list	= array();

//echo '<pre>'; print_r($cats); echo '</pre>';
$ev=$events[0];
foreach($cats as $k=>$v){ $cat_list[$v->ID]=$v->cat_title; }
foreach($langs as $k=>$v){ $lang_list[$v->ID]=$v->lang_name; }

if(!empty($this->validation->error_string)){
    $this->load->view('msg_info', array('msg' => $this->validation->error_string));
}

if(isset($this->edit_id)){
	echo form_open('talk/edit/'.$this->edit_id);
	$sub	= 'Save Edits';
	$title	= 'Edit Session: '.$detail[0]->talk_title;
	menu_pagetitle('Edit Session: '.$detail[0]->talk_title);
}else{ 
	echo form_open('talk/add/event/'.$ev->ID);
	$sub	= 'Add Session';
	$title	= 'Add Session';
	menu_pagetitle('Add Session');
}
echo '<h2>'.$title.'</h2>';

if(isset($msg) && !empty($msg)){ $this->load->view('msg_info', array('msg' => $msg)); }
if(isset($err) && !empty($err)){ $this->load->view('msg_info', array('msg' => $err)); }
?>

<div id="box">
    <div class="row">
	<label for="event"></label>
	<?php
	echo form_hidden('event_id',$ev->ID);
	echo '<b>'.escape($ev->event_name).' ('.date('M d.Y',$ev->event_start).' - '.date('M d.Y',$ev->event_end).')</b>';
	?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="talk_title">Session Title</label>
	<?php echo form_input('talk_title',$this->validation->talk_title);?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="speaker">Speaker</label>
	<?php echo form_input('speaker',$this->validation->speaker);?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="session_date">Date of Session</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $given_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $given_day[$v]=$v; }
	foreach(range(2007,date('Y')+5) as $v){ $given_yr[$v]=$v; }
	echo form_dropdown('given_mo',$given_mo,$this->validation->given_mo);
	echo form_dropdown('given_day',$given_day,$this->validation->given_day);
	echo form_dropdown('given_yr',$given_yr,$this->validation->given_yr);
	?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="session_type">Session Type</label>
	<?php echo form_dropdown('session_type',$cat_list,$this->validation->session_type); ?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="session_lang">Session Language</label>
	<?php echo form_dropdown('session_lang',$lang_list,$this->validation->session_lang); ?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="session_desc">Session Description</label>
	<?php
	$arr=array(
		'name'=>'talk_desc',
		'value'=>$this->validation->talk_desc,
		'cols'=>40,
		'rows'=>10
	);
	echo form_textarea($arr);
	?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="slides_link">Slides Link</label>
	<td><?php echo form_input('slides_link',$this->validation->slides_link); ?></td>
	<div class="clear"></div>
    </div>
    <div class="row">
	<?php echo form_submit('sub',$sub); ?>
    </div>
</div>

<?php form_close(); ?>