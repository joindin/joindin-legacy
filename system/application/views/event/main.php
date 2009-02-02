<?php
$admin=false;
?>
<h1 class="icon-event">
	<?php if(user_is_admin()){ ?>
	<span style="float:left">
	<?php } ?>
	Events
	<?php if(user_is_admin()){ ?>
	</span>
	<?php } ?>
	<?php if(user_is_admin()){ ?>
	<a class="btn" style="float:right" href="/event/add">Add new event</a>
	<div class="clear"></div>
    <?php } ?>
</h1>
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

ob_start();
buildCal($mo,$day,$yr,$evt);
menu_sidebar('Calendar', ob_get_clean());

?>

<?php
$style='';
foreach($events as $k=>$v){
    if(isset($all) && $all==false){
		$style=($estart>=$v->event_start && $eend<=$v->event_end) ? 'color:#5181C1;background-color:#EEEEEE;padding:4px' : 'color:#CCCCCC';
	}
	
	$this->load->view('event/_event-row', array('event'=>$v, 'style' => $style));
?>
<?php
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