
<img src="/inc/img/login.gif"/>
<?php
if(!empty($this->validation->error_string)){
	echo '<div class="err">'.$this->validation->error_string.'</div>';
}

echo form_open('user/login');
?>
<p>
Please login below. If you do not have an account you can <a href="/user/register">create</a> a new one.
</p>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Username:</td>
	<td><?php echo form_input('user',$this->validation->user); ?></td>
</tr>
<tr>
	<td class="title">Password:</td>
	<td><?php echo form_password('pass'); ?></td>
</tr>
<tr>
	<td colspan="2" align="right">
		<?php echo form_submit('sub','login'); ?>
	</td>
</tr>
</table>
<?php echo form_close(); ?>