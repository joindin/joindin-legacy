<?php

$chk=array('post_mo'=>'m','post_day'=>'d','post_yr'=>'Y','post_hr'=>'H','post_mi'=>'i');
foreach($chk as $k=>$v){
	if(empty($this->validation->$k)){
		$this->validation->$k=date($v);
	}
}
$sub='Submit New Post';

echo $this->validation->error_string;
?>

<h2>Add Blog Post</h2>
<?php echo form_open('blog/add'); ?>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Title:</td>
	<td>
	<?php
	$p=array(
		'name'	=>'title',
		'id'	=>'title',
		'size'	=>30
	);
	echo form_input($p);
	?>
	</td>
</tr>
<tr>
	<td valign="top" class="title">Story:</td>
	<td><?php 
		$p=array(
			'name'	=>'story',
			'id'	=>'story',
			'cols'	=>60,
			'rows'	=>15
		);
		echo form_textarea($p); 
	?></td>
</tr>
<tr>
	<td class="title">Post Date:</td>
	<td>
	<?php
	echo form_dropdown('post_mo',range(1,12),$this->validation->post_mo);
	echo form_dropdown('post_day',range(1,31),$this->validation->post_day);
	echo form_dropdown('post_yr',range(date('Y'),date('Y')+5),$this->validation->post_yr);
	echo '&nbsp;@&nbsp;';
	echo form_dropdown('post_hr',range(1,24),$this->validation->post_hr);
	echo form_dropdown('post_mi',range(1,59),$this->validation->post_mi);
	?>
	</td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub',$sub); ?></td></tr>
</table>
<?php echo form_close(); ?>
