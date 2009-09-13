<?php 
// Load some sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');

// Catch Flash message
$this->load->view('message/flash');

// Add message area
$this->load->view('message/area');
?>

<h1>My Account</h1>

<div class="box">
    <?= form_open('user/manage', array('class' => 'form-user-manage')); ?>
    
    <?php if(isset($error)) {
        $this->load->view('message/error', array('message' => $error));
    } ?>
    
    <div class="row">
    	<label for="display_name">Display Name</label>
    	<?= form_input(array('name' => 'display_name', 'id' => 'display_name', 'value' => $user->getDisplayName())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="email">Email Address</label>
    	<?= form_input(array('name' => 'email', 'id' => 'email', 'value' => $user->getEmail())); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="password">Password</label>
    	<?php echo form_input(array('type' => 'password', 'name' => 'password', 'id' => 'password')); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="password_confirm">Confirm Password</label>
    	<?php echo form_input(array('type' => 'password', 'name' => 'password_confirm', 'id' => 'password_confirm')); ?>

        <div class="clear"></div>
    </div>
	
	<div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save changes'); ?>
    </div>

    <?php echo form_close(); ?>
</div>

