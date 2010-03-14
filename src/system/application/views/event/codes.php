<?php 
menu_pagetitle('Send Codes: ' . escape($details[0]->event_name));
?>
<?php
//echo '<pre>'; print_r($full_talks); echo '</pre>';

//echo '<pre>'; print_r($talks); print_r($codes); echo '</pre>';

//print_r($codes);

$cl=array();
foreach($claimed as $k=>$v){ 
	$cl[$v->code]=$v->email;
}
//echo '<pre>'; print_r($claimed); print_r($cl); /*print_r($full_talks);*/ echo '</pre>';
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

<h1 class="icon-event">Send Codes: <?=escape($details[0]->event_name)?></h1>
<p>
To claim their talks, speakers will need the codes below. To send the codes, put the speaker's email address in the field and check the box to signify you want to send to them. If there are multiple speakers for a talk, seperate the addresses with a comma and an email will be sent to both.
</p>
<?php
if(!empty($this->validation->error_string)){
	echo '<div class="err">'.$this->validation->error_string.'</div>';
}

echo form_open('event/codes/'.$details[0]->ID);
echo '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
echo '<tr class="tbl_header"><td>Talk/Speaker</td><td>Code:</td><td colspan="2">Email to:</d></tr>';
foreach($full_talks as $k=>$v){
	$email_id	= 'email_'.$v->ID;
	$email_chk	= 'email_chk_'.$v->ID;
	$chk_post	= $this->input->post($email_chk);
	
	if(array_key_exists((string)$v->code,$cl)){
		$this->validation->$email_id=$cl[$v->code];
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
			<td><a href="/talk/view/%s">%s</a><br/><span style="color:#888888">%s</span></td>
			<td><a href="/talk/view/%s/claim/%s">%s</a></td>
			<td>%s</td><td>%s</td>
		</tr>
	',$rs,$v->ID,escape($v->talk_title),escape($v->speaker),
		$v->ID,escape($codes[$k]),escape($codes[$k]),
		form_checkbox($chk),form_input($email_id,$this->validation->$email_id));
}

echo '<tr><td></td><td></td><td colspan="3">'.form_submit('sub','Send Emails').'</td></tr>';
echo '</table>';
echo form_close();
?>