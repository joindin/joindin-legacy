<?php
if(!empty($this->validation->error_string)){
	echo $this->validation->error_string.'<br/>';
}
?>

<h1 class="title">Manage Your Account</h1>

<?php echo form_open('user/manage'); ?>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Full Name:</td>
	<td><?php 
		$p=array(
			'name'	=>'full_name',
			'id'	=>'full_name',
			'value'	=>$curr_data[0]->full_name
		);
		echo form_input($p,$this->validation->full_name); 
		?>
	</td>
</tr>
<tr>
	<td class="title">Email Address:</td>
	<td><?php 
		$p=array(
			'name'	=> 'email',
			'id'	=> 'email',
			'value'	=> $curr_data[0]->email
		);
		echo form_input($p,$this->validation->email); 
		?>
	</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td class="title">Password:</td>
	<td><?php echo form_password('pass',$this->validation->pass); ?></td>
</tr>
<tr>
	<td class="title">Confirm Password:</td>
	<td><?php echo form_password('pass_conf',$this->validation->pass_conf); ?></td>
</tr>
<tr>
	<td align="right" colspan="2"><?php echo form_submit('sub','Save Changes'); ?></td>
</tr>
</table>
<?php echo form_close(); ?>

<br/>
<a href="">Request API Access</a>