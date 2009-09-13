<?php //var_dump($user) ?>
<h1>Register a new account</h1>

<?php 
$msg=$this->session->flashdata('msg');
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">

    <p>
        Use the form below to register a new account for the site. 
        Username, password and email address fields are required.
    </p>
    
    <?= form_open('user/register', array('class' => 'form-register')); ?>
    
    <?php if(isset($error)): ?>
            <?php $this->load->view('msg_error', array('msg' => $error)); ?>
    <?php endif; ?>

	<div class="row">
    	<label for="user">Username</label>
    	<?= form_input(array('name' => 'username', 'id' => 'username'), $user->getUsername()); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="pass">Password</label>
    	<?= form_input(array('name' => 'password', 'id' => 'password', 'type' => 'password')); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="passc">Confirm Password</label>
    	<?= form_input(array('name' => 'password_confirm', 'id' => 'password_confirm', 'type' => 'password')); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="email">Email</label>
    	<?= form_input(array('name' => 'email', 'id' => 'email'), $user->getEmail()); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="full_name">Display Name</label>
    	<?= form_input(array('name' => 'display_name', 'id' => 'display_name'), $user->getDisplayName()); ?>
    
        <div class="clear"></div>
    </div>
	<div class="row row-buttons">
    	<?= form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Register'); ?>
    </div>
    
    <?= form_close(); ?>
</div>

