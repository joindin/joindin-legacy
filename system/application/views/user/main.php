<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
?>
<h3>My Comments</h3>
<table cellpadding="3" cellspacing="0" border="0">
<?php
foreach($comments as $k=>$v){
	echo '<tr><td>'.date('m.d.Y',$v->date_made).'</td><td><a href="/talk/view/'.$v->talk_id.'#'.$v->ID.'">'.$v->talk_title.'</a></td><td>';
	for($i=1;$i<=$v->rating;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
	echo '</td></tr>';
}
//print_r($comments);
?>
</table>

<h3>My Talks</h3>
<table cellpadding="3" cellspacing="0" border="0">
<?php
if(!empty($talks)){
	foreach($talks as $k=>$v){
		echo '<tr>';
		echo '<td valign="top">';
		for($i=1;$i<=$v->tavg;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
		echo '</td>';
		echo '<td><a href="/event/view/'.$v->tid.'">'.$v->talk_title.'</a> <br/>('.$v->event_name.' - '.date('m.d.Y',$v->date_given).')</td>';
		echo '</tr>';
	}
}
?>
</table>

<br/>

<h3>Claim a Talk</h3>
<p>
Enter your talk code below to claim your talk and have access to private comments from visitors.
</p>
<?php
if(!empty($this->validation->error_string)){
	echo $this->validation->error_string.'<br/>';
}
echo form_open('user/main');
echo form_input('talk_code');
echo form_submit('sub','Submit');
form_close();
?>