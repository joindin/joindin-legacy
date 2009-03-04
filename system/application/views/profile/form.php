<?php 

ob_start();
?>
<?php if (!empty($this->validation->error_string)): ?>
	<?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>
<?php
		
		echo form_open('user/main');
		echo form_input(array('name' => 'talk_code', 'style' => 'width:95%'));
		echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit');
		echo form_close();
		?>
		<p>
		Enter your talk code above to claim your talk and have access to private comments from visitors. <a href="/about/contact">Contact Us</a> to have the code for your talk sent via email.
		</p>

<?php
menu_sidebar('Claim a talk', ob_get_clean());

?>
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

<div class="box">
    
    <h2>Edit speaker profile</h2>
    <?= form_open_multipart('user/profile/edit') ?>
    
    <div class="row">
        <label for="full_name">Full Name</label>
        <?php echo form_input(array('name' => 'full_name', 'id' => 'full_name', 'value' => $profile['full_name'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="contact_email">Contact email</label>
        <?php echo form_input(array('name' => 'contact_email', 'id' => 'contact_email', 'value' => $profile['contact_email'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="website">Website</label>
        <?php echo form_input(array('name' => 'website', 'id' => 'website', 'value' => $profile['website'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="blog">Blog</label>
        <?php echo form_input(array('name' => 'blog', 'id' => 'blog', 'value' => $profile['blog'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="phone">Phone number</label>
        <?php echo form_input(array('name' => 'phone', 'id' => 'phone', 'value' => $profile['phone'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="bio">Bio</label>
        <?php echo form_textarea(array('name' => 'bio', 'id' => 'bio', 'value' => $profile['bio'])); ?>
		<small>HTML is not allowed</small>
        <div class="clear"></div>
    </div>
    
    <h2>Mailing address</h2>
    <div class="row">
        <label for="street">Street</label>
        <?php echo form_input(array('name' => 'street', 'id' => 'street', 'value' => $profile['street'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="zip">Zip code</label>
        <?php echo form_input(array('name' => 'zip', 'id' => 'zip', 'value' => $profile['zip'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="city">City</label>
        <?php echo form_input(array('name' => 'city', 'id' => 'city', 'value' => $profile['city'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="country">Country</label>
        <?php echo form_input(array('name' => 'country', 'id' => 'country', 'value' => $profile['country'])); ?>

        <div class="clear"></div>
    </div>
    
    <h2>Other</h2>
    <div class="row">
        <label for="resume">Resume</label>
        <?php echo form_input(array('name' => 'resume', 'id' => 'resume', 'value' => $profile['resume'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="picture">Picture</label>
        
        <div style="vertical-align: middle;">
	        <?php if(!empty($profile['picture'])) : ?>
		        <div style="float: left; margin-right: 20px;">
		            <img src="<?= $profile['picture'] ?>" />
		        </div>
		        <?php if(!empty($profile['picture'])) : ?>
				<div style="float: left; clear: both; margin-top: 5px;">
					<input type="checkbox" name="delete_picture" value="1" style="float: left; margin-top: 2px;" /> delete picture. 
				</div>
				<?php endif; ?>
	        <?php endif; ?>
	        
	        <p>
		        Upload a new picture: <br />
		        <?php echo form_upload(array('name' => 'picture', 'id' => 'picture')); ?> <br />
		        <small>Allowed file types: gif, jpg, png</small><br />
		        <small>Dimesions: 150x150 pixels.</small><br />
				<small>Max. size: 250KB</small>
			</p>
			
	        <div style="clear: both;">&nbsp;</div>
	        
        </div>
        
        <div class="clear"></div>
    </div>
    <p style="margin-top: 30px; text-align: right;">
        <?php
            echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save profile');
    		echo form_close();
		?>
    </p>
    
</div>
