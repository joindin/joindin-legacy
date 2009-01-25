<?php
$det=$events[0]; //print_r($det);
$cl=array();
foreach($claimed as $k=>$v){ $cl[$v->rid]=$v->uid; }

?>

<div style="padding:10px;border:0px solid #B86F09;background-color:#E4F1E8">
<h1 class="title"><?=$det->event_name?></h1>
<span style=";font-size:10px">
<?=$det->event_loc?><br/>
<?=date('m.d.Y',$det->event_start).' - '.date('m.d.Y',$det->event_end)?>
<br/><br/>
<?=nl2br($det->event_desc)?><br/>
</span>
<br/>
<?php 
/*
if its set, but the event was in the past, just show the text "I was there!"
if its set, but the event is in the future, show a link for "I'll be there!"
if its not set show the "I'll be there/I was there" based on time
*/
if($attend){
	if($det->event_end<time()){
		$link_txt="I was there!"; $showt=1;
	}else{ $link_txt="I'll be there!"; $showt=2; }
}else{
	if($det->event_end<time()){
		$link_txt="Were you there?"; $showt=3; 
	}else{ $link_txt="Will you be there?"; $showt=4; }
}
?>
<b>::></b> <a href="#" id="attend_link" onClick="markAttending(<?=$det->ID?>,<?=$showt?>);return false;"><?=$link_txt?></a>
(<?=$attend?> attending so far)
</div>

<?php if($admin){ ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<a href="/event/edit/<?=$det->ID?>"><img src="/inc/img/sticky.gif" border="0" alt="Edit event"/></a>
		<a href="/talk/add"><img src="/inc/img/pending.png" border="0" alt="Add new talk"/></a>
	</td>
	<td width="50" align="right">
		<a href="/event/delete/<?=$det->ID?>"><img src="/inc/img/redx.png" border="0" alt="Delete event"/></a>
	</td>
</tr>
</table>
<br/>
<a href="/event/codes/<?=$det->ID?>">get talk codes</a>
<br/>
<center>
<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</center>
<br/>

<?php
}
echo '<br/>';
$by_day=array();
//echo '<pre>'; print_r($talks); echo '</pre>';
foreach($talks as $v){
	//echo '<a href="/talk/view/'.$v->ID.'">'.$v->talk_title.' ('.$v->speaker.')</a><br/>';
	$day=date('m_d_Y',$v->date_given);
	$by_day[$day][]=$v;
}
ksort($by_day);
$ct=0;

?>
<style>
td.selected {
	background-color: #5181C1;
	font-weight: bold;
	font-size: 12px;
	width: 130px;
	text-align: center;
}
td.nselected {
	background-color: #EEEEEE;
	font-weight: bold;
	font-size: 12px;
	width: 130px;
	text-align: center;
}
td.selected a {
	color: #FFFFFF;
	text-decoration: none;
}
td.nselected a {
	color: #5181C1;
	text-decoration: none;
}
</style>
<script>
function switchCell(n){
	if(n=='talks'){
		document.getElementById('cell_comments').className='nselected';
		document.getElementById('cell_talks').className='selected';
		$('#talks_div').css('display','block');
		$('#comments_div').css('display','none');		
	}else{
		document.getElementById('cell_comments').className='selected';
		document.getElementById('cell_talks').className='nselected';
		$('#talks_div').css('display','none');
		$('#comments_div').css('display','block');
	}
	return false;
}
</script>

<center>
<table cellpadding="4" cellspacing="0" border="0">
<tr>
	<td class="selected" id="cell_talks"><a href="#" onClick="switchCell('talks');return false;">Talks (<?=count($talks)?>)</a></td>
	<td class="nselected" id="cell_comments"><a href="#" onClick="switchCell('comments');return false;">Comments (<?=count($comments)?>)</a></td>
</tr>
</table>
</center>

<?php
echo '<div style="border:2px solid #5181C1;padding:3px;" id="talks_div">';
echo '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
foreach($by_day as $k=>$v){
	echo '<tr><td colspan="2"><a name="'.$k.'"></a><b>'.str_replace('_','.',$k).'</b></td></tr>';
	foreach($v as $ik=>$iv){
		$style=($ct%2==0) ? 'row1' : 'row2';
		//echo '<tr><td align="right">'.str_repeat('*',$iv->rank).'</td>';
		echo '<tr class="'.$style.'"><td align="right">';
		for($i=1;$i<=$iv->rank;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
		echo '</td>';
		$sp=(array_key_exists((string)$iv->ID,$cl)) ? '<a href="/user/view/'.$cl[$iv->ID].'">'.$iv->speaker.'</a>' : $iv->speaker;
		echo '<td><a href="/talk/view/'.$iv->ID.'">'.$iv->talk_title.'</a></td><td style="font-size:10px;font-weight:bold;color:#858585">'.strtoupper($iv->tcid).'</td><td>';
		echo '<img src="/inc/img/flags/'.$iv->lang.'.gif"/></td><td>'.$sp.'</td><tr/>';
		$ct++;
	}
}
?>
</table></div>

<div style="border:2px solid #5181C1;padding:3px;" id="comments_div">
	<?php
	if(isset($msg)){ echo '<div class="notice">'.$msg.'</div>'; }
	
	echo $this->validation->error_string;
	echo form_open('event/view/'.$det->ID.'#comments');
	
	$types=array(
		'Suggestion'		=> 'Suggestion',
		'General Comment'	=> 'General Comment',
		'Feedback'			=> 'Feedback'
	);
	$type=($det->event_start>time()) ? 'Suggestion':'Feedback';
	?>
	<table cellpadding="3" cellspacing="0" border="0">
	<?php
	if($user_id==0){
	?>
	<tr>
		<td>Name:</td>
		<td><?php echo form_input('cname',$this->validation->cname); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>Type:</td>
		<td><?=$type?></td>
	</tr>
	<tr>
		<td valign="top">Comment:</td>
		<td>
		<?php 
		$arr=array(
			'name'=>'event_comment',
			'value'=>$this->validation->event_comment,
			'cols'=>50,
			'rows'=>8
		);
		echo form_textarea($arr);
		?>
		</td>
	</tr>
	<tr><td colspan="2" align="right"><?php echo form_submit('sub','Submit'); ?></td></tr>
	</table>
	<?php 
	echo form_close(); 
	
	//print_r($comments);
	$ct=0;
	echo '<table cellpadding="0" cellspacing="2" border="0" width="100%" class="event_comments">';
	foreach($comments as $k=>$v){
		$class  = ($ct%2==0) ? 'row1' : 'row2';
		$name	= ($v->user_id!=0) ? '<a href="/user/view/'.$v->user_id.'">'.$v->cname.'</a>' : $v->cname;
		$type	= ($det->event_start>time()) ? 'Suggestion' : 'Feedback';
		
		echo '<tr class="'.$class.'"><td><span class="comment">'.$v->comment.'</span><br/>';
		echo '<span class="meta">'.$name.', '.date('m.d.Y H:i:s',$v->date_made).' ('.$type.')</td></tr>';
		$ct++;
	}
	echo '</table><br/>';
	?>
</div>

<script>
if(window.location.hash=='#comments'){
	switchCell('comments');
}else{ 
	var talk_num=<?=count($talks)?>;
	if(talk_num<=0){
		switchCell('comments'); 
	}else{ switchCell('talks'); }
}
</script>
	