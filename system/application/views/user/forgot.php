<h1>Forgot My Password</h1>
<div class="box">
	<?= form_open('user/forgot'); ?>
	
	<?php if (!empty($error)): ?>
        <?php $this->load->view('msg_error', array('msg' => $error)); ?>
    <?php endif; ?>

	<div class="row">
		<p>
			If you've forgotten your password, enter either the login name or email address associated with the 
			account below and hit "Send". A new password will be sent to the email address for that account.
		</p>
    	<div class="clear"></div>
	</div>

	<div class="row">
    	<label for="user">Username</label>
    	<?= form_input(array('name' => 'username', 'id' => 'username'), $this->validation->user); ?>
		<br/><b>or</b><br/><br/>
    	<label for="user">Email Address</label>
    	<?= form_input(array('name' => 'email', 'id' => 'email'), $this->validation->email); ?>
    
        <div class="clear"></div>
    </div>
	<div class="row row-buttons">
    	<?= form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Send'); ?>
    </div>
    
    <?= form_close(); ?>
</div>
