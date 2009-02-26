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


<h2>Profile Access</h2>

<p>
	some introductary text here ...
</p>

<table cellpadding="0" cellspacing="0" class="data-table">
	<thead>
		<tr>
			<td>Token</td>
			<td>Description</td>
			<td>Created</td>
			<td>&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($tokens as $token) : ?>
		<tr>
			<td><?= $token['access_token'] ?></td>
			<td><?= $token['description'] ?></td>
			<td style="width: 135px;"><?= date('m/d/Y g:i A', $token['created']) ?></td>
			<td style="width: 100px;">
				<a class="btn-small" href="/user/profile/token/<?= $token['id'] ?>">edit</a>
    			&nbsp;or&nbsp;
    			<?= delete_link(
    				'/user/profile/token_delete/' . $token['id'], 
    				'Are you sure you want to delete token ' . $token['access_token'] . '?') 
    			?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<p style="text-align: right;">
	<a class="btn btn-success" href="/user/profile/token">Add token</a> or <a href="/user/profile">go back to speaker profile</a>
</p>

</div>