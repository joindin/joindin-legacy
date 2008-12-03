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
</div>

<?php if($admin){ ?>
<a href="/event/delete/<?=$det->ID?>"><img src="/inc/img/redx.png" border="0" alt="Delete event"/></a>
<a href="/event/edit/<?=$det->ID?>"><img src="/inc/img/sticky.gif" border="0" alt="Edit event"/></a>
<a href="/talk/add"><img src="/inc/img/pending.png" border="0" alt="Add new talk"/></a>
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
echo '</table>';
?>