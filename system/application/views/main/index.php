<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
//echo '<pre>'; print_r($events); echo '</pre>';
?>
<h2>Upcoming Events</h2>
<?php
foreach($events as $k=>$v){
	echo '<div>';
	echo '<h3><a href="/event/view/'.$v->ID.'">'.$v->event_name.'</a></h3>';
	echo date('m.d.Y',$v->event_start).' - '.date('m.d.Y',$v->event_end).'<br/>';
	$p=explode(' ',$v->event_desc);
	$str='';
	for($i=0;$i<20;$i++){ if(isset($p[$i])){ $str.=$p[$i].' '; } } echo trim($str).'...';
	echo '</div><br/>';
}
?>
<h2>Popular Talks</h2>
<?php 
echo '<table cellpadding="3" cellspacing="0" border="0">';
foreach($talks as $k=>$v){
	$ccount=($v->ccount>1) ? $v->ccount.' comments' : '1 comment';
	echo '<tr><td align="right" valign="top">';
	for($i=1;$i<=$v->tavg;$i++){
		echo '<img id="rate_'.$i.'" src="/inc/img/thumbs_up.jpg" height="20" border="0"/>';
	}
	echo '<td/>';
	echo '<td><h3><a href="/talk/view/'.$v->ID.'">'.$v->talk_title.'</a></h3><span style="color:#999999;font-size:10px">('.$ccount.')</span></td></tr>';
}
echo '</table>';
?>

<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
