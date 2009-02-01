<?php

//echo '<pre>'; print_r($talks); print_r($codes); echo '</pre>';
$cl=array();
foreach($claimed as $k=>$v){ $cl[$v->rid]=$v->email; }
?>
<style>
tr.tbl_header {
	background-color: #C5C8A8;
}
tr.tbl_header td {
	font-weight: bold;
}
tr.claimed { background-color: #DEDEDE; }
</style>

<h1 class="icon-event">Send Codes: <?=$details[0]->event_name?></h1>
<p>
To claim their talks, speakers will need the codes below. To send the codes, put the speaker's email address in the field and check the box to signify you want to send to them. If there are multiple speakers for a talk, seperate the addresses with a comma and an email will be sent to both.
</p>
<?php
if(!empty($this->validation->error_string)){
	echo '<div class="err">'.$this->validation->error_string.'</div>';
}

echo form_open('event/codes/'.$details[0]->ID);
echo '<table cellpadding="3" cellspacing="0" border="0">';
echo '<tr class="tbl_header"><td>Talk/Speaker</td><td>Code:</td><td colspan="2">Email to:</d></tr>';
foreach($talks as $k=>$v){
	$email_id	= 'email_'.$v->ID;
	$email_chk	= 'email_chk_'.$v->ID;
	$chk_post	= $this->input->post($email_chk);
	if(array_key_exists((string)$v->ID,$cl)){
		$this->validation->$email_id=$cl[$v->ID];
		$rs='class="claimed"';
	}else{ $rs=''; }
	$chk=array(
		'name'	=> $email_chk,
		'id'	=> $email_chk,
		'value'	=> 1,
		'checked'=>(!empty($chk_post) && $chk_post==1) ? true : false
	);
	echo sprintf('
		<tr %s>
			<td>%s<br/<span style="color:#888888">%s</span></td>
			<td>%s</td>
			<td>%s</td><td>%s</td>
		</tr>
	',$rs,$v->talk_title,$v->speaker,$codes[$k],form_checkbox($chk),
	form_input($email_id,$this->validation->$email_id));
}
echo '<tr><td></td><td></td><td colspan="3">'.form_submit('sub','Send Emails').'</td></tr>';
echo '</table>';
echo form_close();
?>