<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
//echo '<pre>'; print_r($events); echo '</pre>';
?>
<center>
<div style="width:90%;background-color:#E4F1E8;padding:5px">
<p>
<b>Welcome to Joind.in</b> linking those presenting with their audience. Forget those paper slips or not even getting feedback, we're here to help you and those interested attendees connect. 
</p>
<p>
If you have an event that you'd like to post, <a href="/about/contact">let us know!</a>.
</p>
</div>
</center>

<br/>
<img src="/inc/img/curr_up.gif"/><br/>
<br/>
<table cellpadding="0" cellspacing="0" border="0" width="95%">
<tr>
<td width="60%">
<?php
foreach($events as $k=>$v){
	echo '<div>';
	echo '<a style="font-weight:bold;font-size:12px" href="/event/view/'.$v->ID.'">'.$v->event_name.'</a><br/>';
	echo date('m.d.Y',$v->event_start).' - '.date('m.d.Y',$v->event_end).'<br/>';
	$p=explode(' ',$v->event_desc);
	$str='';
	for($i=0;$i<20;$i++){ $str.=$p[$i].' '; } echo trim($str).'...';
	echo '</div><br/>';
}
echo '<br/>';
echo '<img src="/inc/img/pop_talk.gif"/>';
echo '<table cellpadding="3" cellspacing="0" border="0">';
foreach($talks as $k=>$v){
	$ccount=($v->ccount>1) ? $v->ccount.' comments' : '1 comment';
	echo '<tr><td align="right" valign="top">';
	for($i=1;$i<=$v->tavg;$i++){
		echo '<a href="#" onClick="setVote('.$i.')"><img id="rate_'.$i.'" src="/inc/img/thumbs_up.jpg" height="20" border="0"/></a>';
	}
	echo '<td/>';
	echo '<td><a href="/talk/view/'.$v->ID.'">'.$v->talk_title.'</a> ('.$ccount.')</td></tr>';
}
echo '</table>';
?>
</td>
<td width="40%" valign="top" align="right">
	<div>
	<?php
	echo form_open('/user/login');
	echo '<table cellpadding="3" cellspcing="0" border="0">';
	echo '<tr><td colspan="2"><img src="/inc/img/login.gif"/></td></tr>';
	echo '<tr><td>User:</td><td>'.form_input('user').'</td></tr>';
	echo '<tr><td>Pass:</td><td>'.form_password('pass').'</td></tr>';
	echo '<tr><td colspan="2">'.form_submit('sub','login').'</td></tr>';
	echo '</table>';
	form_close();
	?>
	Don't have an account? <a href="/user/register">Signup now!</a>
	</div>
</td>
</tr>
</table>
<br/>
<center>
<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</center>
