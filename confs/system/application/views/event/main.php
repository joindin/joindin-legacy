<?php
$admin=false;
?>
<img src="/inc/img/current_events.gif"/>
<br/><br/>
<?php
//echo '<pre>'; print_r($events); echo '</pre>'; 

$evt=array();
foreach($events as $k=>$v){
	$evt[]=array(
		'day_start'	=> date('d',$v->event_start),
		'day_end'	=> date('d',$v->event_end),
		'title'		=> $v->event_name,
		'link'		=> '/event/view/'.$v->ID
	);
}

/*$evt=array(
	array('day_start'=>2,'title'=>'foo','link'=>'http://foo.com'),
	array('day_start'=>4,'day_end'=>6,'title'=>'foo 2','link'=>'http://foo.com')
);*/
$estart	= mktime(0,0,0,$mo,$day,$yr);
$eend	= mktime(23,59,59,$mo,$day,$yr);
?>

<div style="float:left;padding-right:15px"><?php buildCal($mo,$day,$yr,$evt); ?></div>
<?php
$style='';
foreach($events as $k=>$v){
	if(isset($all) && $all==false){
		$style=($estart>=$v->event_start && $eend<=$v->event_end) ? 'color:#5181C1;background-color:#EEEEEE;padding:4px' : 'color:#CCCCCC';
	}
	
	echo '<a style="font-size:13px;font-weight:bold;'.$style.'" href="/event/view/'.$v->ID.'">'.$v->event_name.'</a><br/><div style="padding-left:8px;padding-top:5px">'.$v->event_desc.'<br/>';
	echo '<span style="color:#A2A2A2">'.date('m.d.Y',$v->event_start).'-'.date('m.d.Y',$v->event_end).'</span><br/>';
	echo '</div><br/>';
}
if(count($events)==0){ 
	echo sprintf('
		<h2>No events found for this month!</h2>
		<p>
		Know of an event happening this month? <a href="/event/submit">Let us know!</a>
		We love to get the word out about events the community would be interested in and
		you can help us spread the word!
		</p>
	'); 
}
?>

<?php if($admin){ ?>
<a href="/event/add">add new event</a>
<?php } ?>