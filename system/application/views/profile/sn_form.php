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

<?php if(!isset($account)) : ?>

<div class="box">
	<h2>Account not found</h2>
	<p>
		The social network account was not found in your profile. <br />
		<a href="/user/profile">Back to profile</a>
	</p>
</div>

<?php else : ?>

<div class="box">

	<?= form_open('/user/profile/sn') ?>
	
	<div class="row">
        <label for="service_name">Service Name</label>
        <?php echo form_input(array('name' => 'service_name', 'id' => 'service_name', 'value' => $account['service_name'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="account_url">Account URL</label>
        <?php echo form_input(array('name' => 'account_url', 'id' => 'account_url', 'value' => $account['account_url'])); ?>

        <div class="clear"></div>
    </div>
	
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $account['id'], 'profile_id' => $account['profile_id'])) ?>
        <a href="/user/profile">Cancel</a> or <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save account') ?>
    	<?= form_close() ?>
    </p>

</div>

<?php endif; ?>