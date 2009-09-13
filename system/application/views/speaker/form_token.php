<?php
menu_pagetitle('Speaker access');
// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li><a href="/speaker/profile">Speaker Profile</a></li>
		<li><a href="/speaker/access">Profile Access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<?php
// Catch flash messages
$this->load->view('message/flash');
// Add message area
$this->load->view('message/area');
?>

<div class="box">
    
	<?php
		if($token->isNew()) {
			echo form_open('/speaker/edittoken');
		}
		else {
			echo form_open('/speaker/edittoken/' . $token->getId());
		}
    ?>
    <div class="row">
        <label for="description">Description</label>
        <?php echo form_input(array('name' => 'description', 'id' => 'description', 'value' => $token->getDescription())); ?>

        <div class="clear"></div>
    </div>
    
    <h2>Exposed fields</h2>
    <div>
		
		<table class="fields-table">
			<tr>
				<td>
					<div>
						<?= form_checkbox(array('id' => 'full_name', 'name' => 'fields[]', 'value' => 'full_name', 'checked' => in_array('full_name', $token->getFields()))) ?>
						<label for="full_name">Full Name</label>
					</div>
					<div>
                		<?= form_checkbox(array('id' => 'contact_email', 'name' => 'fields[]', 'value' => 'contact_email', 'checked' => in_array('contact_email', $token->getFields()))) ?>
                		<label for="contact_email">Contact Email</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'phone', 'name' => 'fields[]', 'value' => 'phone', 'checked' => in_array('phone', $token->getFields()))) ?>
                		<label for="phone">Phone</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'address', 'name' => 'fields[]', 'value' => 'address', 'checked' => in_array('address', $token->getFields()))) ?>
                		<label for="address">Address</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'country', 'name' => 'fields[]', 'value' => 'country', 'checked' => in_array('country', $token->getFields()))) ?>
                		<label for="country">Country</label>
                	</div>
				</td>
				<td>
					<div>
                		<?= form_checkbox(array('id' => 'job_title', 'name' => 'fields[]', 'value' => 'job_title', 'checked' => in_array('job_title', $token->getFields()))) ?>
                		<label for="job_title">Job Title</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'bio', 'name' => 'fields[]', 'value' => 'bio', 'checked' => in_array('bio', $token->getFields()))) ?>
                		<label for="bio">Bio</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'resume', 'name' => 'fields[]', 'value' => 'resume', 'checked' => in_array('resume', $token->getFields()))) ?>
                		<label for="resume">Resume</label>
                	</div>
				</td>
				<td>
					<div>
                		<?= form_checkbox(array('id' => 'website', 'name' => 'fields[]', 'value' => 'website', 'checked' => in_array('website', $token->getFields()))) ?>
                		<label for="website">Website</label>
                	</div>
                	<div>
                		<?= form_checkbox(array('id' => 'blog', 'name' => 'fields[]', 'value' => 'blog', 'checked' => in_array('blog', $token->getFields()))) ?>
                		<label for="blog">Blog</label>
                	</div>
    				<div>
                		<?= form_checkbox(array('id' => 'picture', 'name' => 'fields[]', 'value' => 'picture', 'checked' => in_array('picture', $token->getFields()))) ?>
                		<label for="picture">Picture</label>
                	</div>
				</td>
			</tr>
		</table>
		
	</div>
    
    <div class="row row-buttons">
		<?= form_hidden(array('id' => $token->getId(), 'speaker_profile_id' => $speaker->getId())) ?>
        <a href="/speaker/access">cancel</a>
        &nbsp;or&nbsp;
        <input class="btn" type="submit" id="save" name="save" value="Save" />
        <div class="clear"></div>
    </div>
    
    <?= form_close() ?>
    
</div>


