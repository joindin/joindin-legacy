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

<?php if(is_null($profile)) : ?>

<div class="box">
	<p style="text-align: center;">
	    You do not have a speaker profile yet. Go create one!<br />
	</p>
	<p style="text-align: center;">
	    <a class="btn-big btn-success" href="/user/profile/edit">Create speaker profile</a>
	</p>
	
	<h2>Speaker profile</h2>
	<p>
	    Some introductary text here ...
	</p>
</div>

<?php else : ?>

<div style="margin-bottom: 10px; text-align: center;">
	<a href="#personal">Personal</a>&nbsp;|&nbsp;
	<a href="#messaging">Instant messaging</a> | 
	<a href="#social">Social Networking</a>&nbsp;|&nbsp;
	<a href="/user/profile/access">Profile access</a>
</div>

<div class="box">    
    <?php if(!empty($profile['picture'])) :?>
    
    
    <div style="float: right; text-align: center;z-index: 100; width: 190px; height: 190px; margin: 10px; padding: 1px 0 10px 10px; background: #F0F4F8 url(/inc/img/bg-box.gif) repeat-x scroll left top;">
    		<h2>Picture</h2>
    		<img src="<?= $profile['picture'] ?>" />
    </div>
    <?php endif; ?>
    
    <a name="personal"></a>
    <div class="detail">
    
    	<h2>Full name</h2>
	    <p>
	        <?= $profile['full_name'] ?>
	    </p>
    
	    <h2>Contact E-mail</h2>
    	<p>
        	<?= $profile['contact_email'] ?>
    	</p>
    
	    <h2>Phone</h2>
	    <p>
	        <?= $profile['phone'] ?>
	    </p>
	    
	    <h2>Mailing address</h2>
	    <p>
	        <?= $profile['street'] ?>, <?= $profile['zip'] ?> <br />
	        <?= $profile['city'] ?>, <?= $profile['country'] ?>
	    </p>
	    
	</div>
    
    <div class="detail">
	    <h2>Bio</h2>
	    <p>
	        <?= nl2br($profile['bio']) ?>
	    </p>
	    
	    <h2>Resume</h2>
	    <p>
	        <a href=""><?= $profile['resume'] ?></a>
	    </p>
	</div>
	    
    <p style="margin-top: 30px; text-align: right;">
        <a class="btn" href="/user/profile/edit">Edit profile</a>
        &nbsp;or&nbsp;
        <?= delete_link('/user/profile/delete', 'Are you sure you want to delete your profile?')?>
    </p>
	
	<br />
	
    <div class="">
    	<a name="messaging"></a>
	    <h2>Instant Messaging</h2>
	    <table cellpadding="0" cellspacing="0" class="data-table">
	    <thead>
	    	<tr>
	    		<td>Network</td>
	    		<td>Account Name</td>
	    		<td>&nbsp;</td>
	    	</tr>
	    </thead>
	    <tbody>
	    <?php 
	    	$total = count($im_accounts);
	    	$current = 1;
	    	foreach($im_accounts as $account) :
	    ?>
	    	<tr class="<?= (($current % 2) == 0) ? 'alt-row' : '' ?>">
	    		<td style="width: 100px;"><?= $account['network_name'] ?></td>
	    		<td><?= $account['account_name'] ?></td>
	    		<td style="width: 100px;">
	    			<a class="btn-small" href="/user/profile/im/<?= $account['id'] ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/user/profile/im_delete/' . $account['id'], 
	    				'Are you sure you want to delete your ' . $account['network_name'] . ' account?') 
	    			?>
	    		</td>
	    	</tr>
	    <?php
	    	$current++; 
	    	endforeach; 
	    ?>
	    </tbody>
	    </table>
	    
	    <p style="text-align: right;">
	        <a class="btn btn-success" href="/user/profile/im">+ add account</a>
	    </p>
	</div>
	
	<div class="">
		<a name="social"></a>
	    <h2>Social Networking</h2>
	    <table cellpadding="0" cellspacing="0" class="data-table">
	    <thead>
	    	<tr>
	    		<td>Service</td>
	    		<td>Account URL</td>
	    		<td>&nbsp;</td>
	    	</tr>
	    </thead>
	    <tbody>
	    <?php 
	    	$total = count($sn_accounts);
	    	$current = 1;
	    	foreach($sn_accounts as $account) :
	    ?>
	    	<tr class="<?= (($current % 2) == 0) ? 'alt-row' : '' ?>">
	    		<td style="width: 100px;"><?= $account['service_name'] ?></td>
	    		<td><?= auto_link($account['account_url']) ?></td>
	    		<td style="width: 100px;">
	    			<a class="btn-small" href="/user/profile/social/<?= $account['id'] ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/user/profile/sn_delete/' . $account['id'], 
	    				'Are you sure you want to delete your ' . $account['service_name'] . ' account?') 
	    			?>
	    		</td>
	    	</tr>
	    <?php
	    	$current++; 
	    	endforeach; 
	    ?>
	    </tbody>
	    </table>
	    
	    <p style="text-align: right;">
	        <a class="btn btn-success" href="/user/profile/sn">+ add account</a>
	    </p>
	</div>
    
</div>
<?php endif; ?>
    

