<br/>
<img src="/inc/img/my_comments.gif"/><br/>
<?php
$fmsg=$this->session->flashdata('msg');
if(isset($msg) && !empty($msg)){ 
	echo '<div class="notice">'.$msg.'</div><br/>';
}elseif(!empty($fmsg)){
	echo '<div class="notice">'.$fmsg.'</div><br/>';	
}
?>
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
<br/>
<img src="/inc/img/my_talks.gif"/><br/>
<table cellpadding="3" cellspacing="0" border="0">
<?php
if(!empty($talks)){
	foreach($talks as $k=>$v){
		echo '<tr>';
		echo '<td valign="top">';
		for($i=1;$i<=$v->tavg;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
		echo '</td>';
		echo '<td><a href="/talk/view/'.$v->tid.'">'.$v->talk_title.'</a> <br/>('.$v->event_name.' - '.date('m.d.Y',$v->date_given).')</td>';
		echo '</tr>';
	}
}else{ echo 'No current talks...'; }
?>
</table>

<br/>

<img src="/inc/img/claim_talk.gif"/><br/>
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