<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
?>
<h1 class="icon-talk">Talks</h1>

<table cellpadding="3" cellspacing="0" border="0">
<?php
foreach(array_slice($talks,0,10) as $v){
	echo '<tr><td align="right" valign="top">';
	for($i=1;$i<=$v->tavg;$i++){
		echo '<a href="#" onClick="setVote('.$i.')"><img id="rate_'.$i.'" src="/inc/img/thumbs_up.jpg" height="20" border="0"/></a>';
	}
	echo '</td>';
	echo '<td><a style="font-size:12px" href="/talk/view/'.$v->ID.'">'.$v->talk_title.'</a><br/>';
	echo '<span style="color:#A6A6A6"><b><a style="color:#A6A6A6" href="/event/view/'.$v->event_id.'">@'.$v->ename.'</a></b>  - '.date('m.d.Y',$v->date_given).'</span>';
	echo '</td></tr>';
	
}
echo '</table>';
?>