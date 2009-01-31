<h1 class="icon-about">Contact</h1>
<p>
Submit the contact form below to send us a note or ask a question.
</p>
<?php 
//$msg=$this->session->flashdata('msg');
if(isset($msg) && !empty($msg)){ echo '<div class="notice">'.$msg.'</div><br/>'; }

echo $this->validation->error_string;

echo form_open('about/contact'); 
?>
<table cellpadding="4" cellspacing="0" border="0">
<tr>
	<td class="title">Your Name:</td>
	<td><?php echo form_input('your_name',$this->validation->your_name); ?></td>
</tr>
<tr>
	<td class="title">Your Email:</td>
	<td><?php echo form_input('your_email',$this->validation->your_email); ?></td>
</tr>
<tr>
	<td class="title" valign="top">Comments:</td>
	<td><?php 
		$attr=array(
			'name'	=> 'your_com',
			'id'	=> 'your_com',
			'cols'	=> 40,
			'rows'	=> 5,
			'value'	=> $this->validation->your_com
		);
		echo form_textarea($attr); 
	?></td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub','Submit'); ?></td></tr>
</table>
<?php echo form_close(); ?>