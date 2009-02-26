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
        <label for="account_url">Description</label>
        <?php echo form_input(array('name' => 'description', 'id' => 'description', 'value' => $token['description'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="created">Created</label>
    	<?= date('m/d/Y g:i A', $token['created']) ?>
    	
    	<div class="clear"></div>
    </div>
	
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $token['id'], 'profile_id' => $token['profile_id'], 'access_token' => $token['access_token'], 'created' => $token['created'])) ?>
        <a href="/user/profile/access">Cancel</a> or <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save token') ?>
    	<?= form_close() ?>
    </p>

</div>

<?php endif; ?>