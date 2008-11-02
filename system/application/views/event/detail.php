<?php
$det=$events[0]; //print_r($det);
?>

<div style="padding:10px;border:0px solid #B86F09;background-color:#E4F1E8">
<h1 class="title"><?=$det->event_name?></h1>
<span style=";font-size:10px">
<?=$det->event_loc?><br/>
<?=date('m.d.Y',$det->event_start).' - '.date('m.d.Y',$det->event_end)?>
<br/><br/>
<?=nl2br($det->event_desc)?><br/>
</span>
</div>

<?php if($admin){ ?>
<a href="/event/delete/<?=$det->ID?>"><img src="/inc/img/redx.png" border="0" alt="Delete event"/></a>
<a href="/event/edit/<?=$det->ID?>"><img src="/inc/img/sticky.gif" border="0" alt="Edit event"/></a>
<a href="/talk/add"><img src="/inc/img/pending.png" border="0" alt="Add new talk"/></a>
<br/>
<a href="/event/codes/<?=$det->ID?>">get talk codes</a>
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
echo '<table cellpadding="3" cellspacing="0" border="0">';
foreach($by_day as $k=>$v){
	echo '<tr><td colspan="2"><b>'.str_replace('_','.',$k).'</b></td></tr>';
	foreach($v as $ik=>$iv){
		//echo '<tr><td align="right">'.str_repeat('*',$iv->rank).'</td>';
		echo '<tr><td align="right">';
		for($i=1;$i<=$iv->rank;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
		echo '</td>';
		echo '<td><a href="/talk/view/'.$iv->ID.'">'.$iv->talk_title.' ('.$iv->speaker.')</a></td><tr/>';
	}
}
echo '</table>';
?>