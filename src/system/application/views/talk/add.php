<?php
$event_list	= array(); 
$cat_list	= array();
$lang_list	= array();

//echo '<pre>'; print_r($cats); echo '</pre>';
//echo '<pre>'; print_r($tracks); echo '</pre>';

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
$priv=($evt_priv===true) ? ', Private Event' : '';
?>

<div id="box">
    <div class="row">
	<label for="event"></label>
	<?php
	echo form_hidden('event_id',$ev->ID);
	echo '<b><a href="/event/view/'.$ev->ID.'">'.escape($ev->event_name).'</a> ('.date('M d.Y',$ev->event_start).' - '.date('M d.Y',$ev->event_end).$priv.')</b>';
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
	<label for="session_date">Date and Time of Session</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $given_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $given_day[$v]=$v; }
	foreach(range(2007,date('Y')+5) as $v){ $given_yr[$v]=$v; }
	echo form_dropdown('given_mo',$given_mo,$this->validation->given_mo);
	echo form_dropdown('given_day',$given_day,$this->validation->given_day);
	echo form_dropdown('given_yr',$given_yr,$this->validation->given_yr);
	?> at <?php
	foreach(range(0,23) as $v){ $given_hour[$v]=$v; }
	foreach(range(0,55, 5) as $v){ $given_min[$v]=$v; }
	echo form_dropdown('given_hour', $given_hour, $this->validation->given_hour);
	echo form_dropdown('given_min', $given_min, $this->validation->given_min);
	?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="session_type">Session Type</label>
	<?php
		$stype=null;
		if(isset($this->validation->session_type)){
			foreach($cat_list as $k=>$v){
				if($v==$this->validation->session_type){ $stype=$k; }
			}
		}else{ $stype=$this->validation->session_type; }
		echo form_dropdown('session_type',$cat_list,$stype); 
	?>
	<div class="clear"></div>
    </div>

	<?php if(!empty($tracks)): ?>
	<div class="row">
	<label for="session_track">Session Track</label>
	<?php
	$tarr=array('none'=>'No track');
	foreach($tracks as $t){ $tarr[$t->ID]=$t->track_name; }
	echo form_dropdown('session_track',$tarr,$this->validation->session_track); 
	?>
	<div class="clear"></div>
	</div>
	<?php endif; ?>

    <div class="row">
	<label for="session_lang">Session Language</label>
	<?php
		$slang=null;
		if(isset($this->validation->session_lang)){
			foreach($lang_list as $k=>$v){
				if(trim($v)==trim($this->validation->session_lang)){ $slang=$k; }
			}
		}else{ $slang=$this->validation->session_lang; }
		echo form_dropdown('session_lang',$lang_list,$slang); 
	?>
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
