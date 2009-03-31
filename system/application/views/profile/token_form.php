<div class="menu">
	<ul>
		<li><a href="/user/main">Dashboard</a>
		<li><a href="/user/manage">Manage Account</a>
        <li class="active"><a href="/user/profile">Speaker profile</a>
	<?php if (user_is_admin()): ?>
		<li><a href="/user/admin">User Admin</a>
		<li><a href="/event/pending">Pending Events</a>
	<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<?php 
// Check flash messages
if(empty($msg)) {
	$msg = $this->session->flashdata('msg');
}

if(empty($msg_error)) {
	$msg_error = $this->session->flashdata('msg_error');
}

if(!empty($msg)) {
	$this->load->view('msg_info', array('msg' => $msg));
}

if(!empty($msg_error)) {
	$this->load->view('msg_error', array('msg' => $msg_error));
}

?>

<?php if(!isset($token)) : ?>

<div class="box">
	<h2>Token not found</h2>
	<p>
		The token was not found in your profile. <br />
		<a href="/user/profile/access">Back to profile access</a>
	</p>
</div>

<?php else : ?>

<div class="box">

	<?= form_open('/user/profile/token') ?>
	
	<div class="row">
        <label for="access_token">Token</label>
        <?= $token['access_token']?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="created">Created</label>
    	<?= date('m/d/Y g:i A', $token['created']) ?>
    	
    	<div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="account_url">Description</label>
        <?php echo form_input(array('name' => 'description', 'id' => 'description', 'value' => $token['description'])); ?>

        <div class="clear"></div>
    </div>
    
    <h2>Exposed fields</h2>
    
    <div class="inline-labels">
    	
    	<table cellpadding="0" cellspacing="0" style="width: 80%";>
    		<tr>
    			<td style="vertical-align: top;">
    				<fieldset>
    					<?= form_checkbox(array(
                				'id' => 'full_name', 
                				'name' => 'fields[]', 
                				'value' => 'full_name', 
                				'checked' => in_array('full_name', $fields)
                		)) ?>
                		<label for="full_name">Full Name</label>
                	</fieldset>
                	
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'contact_email', 
                				'name' => 'fields[]', 
                				'value' => 'contact_email', 
                				'checked' => in_array('contact_email', $fields)
                		)) ?>
                		<label for="contact_email">Contact Email</label>
                	</fieldset>
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'phone', 
                				'name' => 'fields[]', 
                				'value' => 'phone', 
                				'checked' => in_array('phone', $fields)
                		)) ?>
                		<label for="phone">Phone</label>
                	</fieldset>
                	
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'address', 
                				'name' => 'fields[]', 
                				'value' => 'address', 
                				'checked' => in_array('address', $fields)
                		)) ?>
                		<label for="address">Address</label>
                	</fieldset>
                
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'country', 
                				'name' => 'fields[]', 
                				'value' => 'country', 
                				'checked' => in_array('country', $fields)
                		)) ?>
                		<label for="country">Country</label>
                	</fieldset>
    			</td>
    			<td style="vertical-align: top;">
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'job_title', 
                				'name' => 'fields[]', 
                				'value' => 'job_title', 
                				'checked' => in_array('job_title', $fields)
                		)) ?>
                		<label for="job_title">Job Title</label>
                	</fieldset>
                	
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'bio', 
                				'name' => 'fields[]', 
                				'value' => 'bio', 
                				'checked' => in_array('bio', $fields)
                		)) ?>
                		<label for="bio">Bio</label>
                	</fieldset>
                	
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'resume', 
                				'name' => 'fields[]', 
                				'value' => 'resume', 
                				'checked' => in_array('resume', $fields)
                		)) ?>
                		<label for="resume">Resume</label>
                	</fieldset>
    			</td>
    			<td>
    				<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'website', 
                				'name' => 'fields[]', 
                				'value' => 'website', 
                				'checked' => in_array('website', $fields)
                		)) ?>
                		<label for="website">Website</label>
                	</fieldset>
                	
                	<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'blog', 
                				'name' => 'fields[]', 
                				'value' => 'blog', 
                				'checked' => in_array('blog', $fields)
                		)) ?>
                		<label for="blog">Blog</label>
                	</fieldset>
                	
    				<fieldset>
                		<?= form_checkbox(array(
                				'id' => 'picture', 
                				'name' => 'fields[]', 
                				'value' => 'picture', 
                				'checked' => in_array('picture', $fields)
                		)) ?>
                		<label for="picture">Picture</label>
                	</fieldset>
    			</td>
    		</tr>
    	</table>
    	
    </div>
    
    
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $token['id'], 'profile_id' => $token['profile_id'], 'access_token' => $token['access_token'], 'created' => $token['created'])) ?>
        <a href="/user/profile/access">Cancel</a> or <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save token') ?>
    	<?= form_close() ?>
    </p>

</div>

<?php endif; ?>