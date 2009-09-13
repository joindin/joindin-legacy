<?php 
$this->load->view('sidebar/user-navigation.php');
$this->load->view('sidebar/claim-session.php');

$this->load->view('message/flash');
$this->load->view('message/area');
?>

<div class="box">
    
    <h2>Edit speaker profile</h2>
    <?= form_open_multipart('speaker/edit') ?>
    
    <div class="row">
        <label for="full_name">Full Name</label>
        <?php echo form_input(array('name' => 'full_name', 'id' => 'full_name', 'value' => $speaker->getFullName())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="contact_email">Contact email</label>
        <?php echo form_input(array('name' => 'contact_email', 'id' => 'contact_email', 'value' => $speaker->getContactEmail())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="website">Website</label>
        <?php echo form_input(array('name' => 'website', 'id' => 'website', 'value' => $speaker->getWebsite())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="blog">Blog</label>
        <?php echo form_input(array('name' => 'blog', 'id' => 'blog', 'value' => $speaker->getBlog())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="phone">Phone number</label>
        <?php echo form_input(array('name' => 'phone', 'id' => 'phone', 'value' => $speaker->getPhone())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="bio">Job Title</label>
        <?php echo form_input(array('name' => 'job_title', 'id' => 'job_title', 'value' => $speaker->getJobTitle())); ?>
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="bio">Bio</label>
        <?php echo form_textarea(array('name' => 'bio', 'id' => 'bio', 'value' => $speaker->getBio())); ?>
		<small>HTML is not allowed</small>
        <div class="clear"></div>
    </div>
    
    <h2>Mailing address</h2>
    <div class="row">
        <label for="street">Street</label>
        <?php echo form_input(array('name' => 'street', 'id' => 'street', 'value' => $speaker->getStreet())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="city">City</label>
        <?php echo form_input(array('name' => 'city', 'id' => 'city', 'value' => $speaker->getCity())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="zip">Postal code</label>
        <?php echo form_input(array('name' => 'zip', 'id' => 'zip', 'value' => $speaker->getZip())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="country">Country</label>
        <?= form_dropdown('country_id', $countries, $speaker->getCountryId()) ?>

        <div class="clear"></div>
    </div>
    
    <h2>Other</h2>
    <div class="row">
        <label for="resume">Resume</label>
        <?php echo form_input(array('name' => 'resume', 'id' => 'resume', 'value' => $speaker->getResume())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="picture">Picture</label>
        <div id="uploader-form-container">
            File uploading requires javascript enabled.
        </div>
		<input type="hidden" name="picture" id="picture" value="<?= $speaker->getPicture() ?>" />
		<input type="hidden" name="delete_picture" id="delete_picture" value="0" />
		<script type="text/javascript">
            /**
             * Sets a new value for the profile picture
             * @param string uri
             */
			function setPicture(uri) 
			{
				$('#picture').val(uri);
			}
			
			/**
			 * Returns the value of the picture field. Used as a callback from the 
			 * upload form.
			 * @return string
			 */
			function getPicture()
			{
			    return $('#picture').val();
			}
			
			/**
			 * Toggles the deletion of the picture from the iframe.
			 */
			function deletePicture(value)
			{
			    if(value == 1) {
			        $('#delete_picture').val('1');
			        //$('#picture').val('');
			    } else {
			        $('#delete_picture').val('0');
			    }
			}
			
			/**
			 * Creates a new iFrame. This is to prevent uploading when javascript 
			 * is disabled.
			 */
			function loadIframe()
			{
			    var frame = $('<iframe></iframe>').attr({
			        'id': 'uploader-form',
			        'name': 'uploader-form',
			        'src': '/speaker/profile/picture_form',
			        'style': 'width: 100%; height: 220px;'
			    });
                $('#uploader-form-container').html(frame);
			}
			//loadIframe();
        </script>
        <div class="clear"></div>
    </div>
    
    <div class="row row-buttons">
        <?php
            echo form_hidden('user_id', $speaker->getUserId());
            echo "<a href=\"/speaker/profile\">cancel</a> or ";
            echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save profile');
		?>
    </div>
    <?= form_close(); ?>
</div>
