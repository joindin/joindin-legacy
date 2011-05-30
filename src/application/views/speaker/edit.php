<?php
$this->load->view('user/_nav_sidebar');

/* 
 * Create/Edit Speaker Profiles
 */
echo '<h2>Add/Edit Speaker Info</h2>';
if($profile_pic){ echo '<img src="'.$profile_pic.'"/><br/><br/>'; }
?>

<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php echo form_open_multipart('speaker/edit'); ?>
<div id="box">
    <div class="row">
	<label for="full_name">Full Name</label>
	<?php echo form_input('full_name',$this->input->post('full_name'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="email">Contact Email</label>
	<?php echo form_input('email',$this->input->post('email'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="website">Website</label>
	<?php echo form_input('website',$this->input->post('website'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="blog">Blog</label>
	<?php echo form_input('blog',$this->input->post('blog'));?>
	<div class="clear"></div>
    </div>

    <div class="row">
	<label for="phone">Phone Number</label>
	<?php echo form_input('phone',$this->input->post('phone'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="job_title">Job Title</label>
	<?php echo form_input('job_title',$this->input->post('job_title'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="bio">Bio</label>
	<?php echo form_textarea('bio',$this->input->post('bio'));?>
	<div class="clear"></div>
    </div>

<h2>Mailing Address</h2>
    
    <div class="row">
	<label for="street">Street</label>
	<?php echo form_input('street',$this->input->post('street'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="city">City</label>
	<?php echo form_input('city',$this->input->post('city'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="zip">Zip/Postal Code</label>
	<?php echo form_input('zip',$this->input->post('zip'));?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<label for="country">Country</label>
	<?php 
		$cid=(isset($this->validation->country_id)) ? $this->validation->country_id : null;
		echo form_dropdown('country_id',$countries,$cid);
	?>
	<div class="clear"></div>
    </div>

    <h2>Other</h2>
    <!--<div class="row">
	<label for="resume">Resume</label>
	<?php echo form_upload('resume'); ?>
	<div class="clear"></div>
    </div>-->
    <div class="row">
	<label for="picture">Picture</label>
	<?php echo form_upload('picture'); ?>
	<div class="clear"></div>
    </div>
    <div class="row">
	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit'); ?>
    </div>
</div>

<?php echo form_close(); ?>

