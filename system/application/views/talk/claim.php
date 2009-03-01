<?php
//echo '<pre>'; print_r($claims); echo '</pre>';

echo form_open('talk/claim');
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td></td>
	<td><b>Talk Info</b></td>
	<td><b>Speaker</b></td>
	<td><b>Claim By</b></td>
</tr>
<?php
foreach($claims as $k=>$v){
	$name=(empty($v->claiming_name)) ? $v->claiming_user : $v->claiming_name;
	echo '<tr><td>'.form_checkbox('claim_'.$v->ua_id,1).'</td>';
	echo sprintf('
		<td><a href="/talk/view/%s">%s</a></td>
		<td>%s</td>
		<td><a href="/user/view/%s">%s</a></td>
	</tr>
	',$v->talk_id,$v->talk_title,$v->speaker,$v->uid,$name);
}
?>
<tr>
	<td colspan="4" align="right">
		<?php echo form_submit('sub','Approve'); ?>
	</td>
</tr>
</table>
<?php echo form_close(); ?>