<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
?>
<img src="/inc/img/latest_talks.gif"/>
<br/><br/>
<table cellpadding="3" cellspacing="0" border="0">
<?php
foreach(array_slice($talks,0,10) as $v){
	echo '<tr><td align="right" valign="top">';
	for($i=1;$i<=$v->tavg;$i++){
		echo '<a href="#" onClick="setVote('.$i.')"><img id="rate_'.$i.'" src="/inc/img/thumbs_up.jpg" height="20" border="0"/></a>';
	}
	echo '</td>';
	echo '<td><a href="/talk/view/'.$v->ID.'">'.$v->talk_title.'</a><br/>';
	echo '<span style="color:#A6A6A6"><b>@'.$v->ename.'</b> ('.date('m.d.Y',$v->date_given).')</span>';
	echo '</td></tr>';
	
}
echo '</table>';
?>