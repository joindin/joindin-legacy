<?php
$msg=$this->session->flashdata('msg');
if($msg && !empty($msg)){ echo '<div class="notice">'.$msg.'</div><br/>'; }
?>

<h1 class="title">Register a New Account</h1>
<?php 
if(!empty($this->validation->error_string)){
	echo '<div class="err">'.$this->validation->error_string.'</div>';
}
?>
<p>
Use the form below to register a new account for the site. 
Username, password and email address fields are required.
</p>

<?php
echo form_open('user/register');
?>

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
	<td class="title">Confirm Password:</td>
	<td><?php echo form_password('passc'); ?></td>
</tr>
<tr>
	<td class="title">Email:</td>
	<td><?php echo form_input('email'); ?></td>
</tr>
<tr>
	<td class="title">Full Name:</td>
	<td><?php echo form_input('full_name'); ?></td>
</tr>
<tr>
	<td class="title" valign="top">Type in the code:</td>
	<td>
	<input type="text" name="cinput" id="Captcha" value="" /><br/>
	<?php echo $captcha['image']; ?>
	</td>
</tr>
<tr>
	<td align="right" colspan="2">
		<?php echo form_submit('sub','Register'); ?>
	</td>
</tr>
</table>
<?php form_close(); ?>
