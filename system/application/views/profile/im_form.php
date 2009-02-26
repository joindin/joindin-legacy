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
		The instant messaging account was not found in your profile. <br />
		<a href="/user/profile">Back to profile</a>
	</p>
</div>

<?php else : ?>

<div class="box">

	<?= form_open('/user/profile/im') ?>
	
	<div class="row">
        <label for="network_name">Network Name</label>
        <?php echo form_input(array('name' => 'network_name', 'id' => 'network_name', 'value' => $account['network_name'])); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="account_name">Account Name</label>
        <?php echo form_input(array('name' => 'account_name', 'id' => 'account_name', 'value' => $account['account_name'])); ?>

        <div class="clear"></div>
    </div>
	
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $account['id'], 'profile_id' => $account['profile_id'])) ?>
        <a href="/user/profile">Cancel</a> or <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save account') ?>
    	<?= form_close() ?>
    </p>

</div>

<?php endif; ?>