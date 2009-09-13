<?php 
// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li class="active"><a href="/speaker/profile">Speaker Profile</a></li>
		<li><a href="/speaker/access">Profile Access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<?php 
// Catch flash messages
//$this->load->view('message/flash');
// Add message area
$this->load->view('message/area');
?>

<?php if(is_null($profile)) : ?>

<div class="box">
	<p style="text-align: center;">
	    You do not have a speaker profile yet. Go create one!<br />
	</p>
	<p style="text-align: center;">
	    <a class="btn-big btn-success" href="/speaker/edit">Create speaker profile</a>
	</p>
	
	<h2>Speaker profile</h2>
	<p>
	    Some introduction text here ...
	</p>
</div>

<?php else : ?>

<div style="margin-bottom: 10px; text-align: center;">
	<a href="#personal">Personal</a>&nbsp;|&nbsp;
	<a href="#messaging">Instant messaging</a>&nbsp;|&nbsp;
	<a href="#web">Web presence</a>
</div>

<div class="box">
    <?php if($profile->getPicture() != '') :?>
    
    <div class="profile-picture">
    		<h2>Picture</h2>
    		<img src="<?= $profile->getPicture() ?>" />
    </div>
    <?php endif; ?>
    
    <a name="personal"></a>
    <div class="detail">
    
    	<h2>Full name</h2>
	    <p>
	        <?= $profile->getFullName() ?>
	    </p>
    
	    <h2>Contact E-mail</h2>
    	<p>
        	<?= $profile->getContactEmail() ?>
    	</p>
    	
    	<h2>Website</h2>
    	<p>
        	<?= auto_link($profile->getWebsite()) ?>
    	</p>
    	
    	<h2>Blog</h2>
    	<p>
        	<?= auto_link($profile->getBlog()) ?>
    	</p>
    	    
	    <h2>Phone</h2>
	    <p>
	        <?= $profile->getPhone() ?>
	    </p>
	    
	    <h2>Mailing address</h2>
	    <p>
	    	<?= formatProfileAddress($profile) ?>
	    </p>
	    
	</div>
    
    <div class="detail">
    	<h2>Job Title</h2>
    	<p>
    		<?= $profile->getJobTitle() ?>
    	</p>
	    <h2>Bio</h2>
	    <p>
	        <?= nl2br($profile->getBio()) ?>
	    </p>
	    
	    <h2>Resume</h2>
	    <p>
	        <a href=""><?= $profile->getResume() ?></a>
	    </p>
	</div>
	    
    <p style="margin-top: 30px; text-align: right;">
        <a class="btn" href="/speaker/edit">Edit profile</a>
        &nbsp;or&nbsp;
        <?= delete_link('/speaker/delete', 'Are you sure you want to delete your profile?')?>
    </p>
	
	<br />
	
    <div class="">
    	<a name="messaging"></a>
	    <h2>Instant Messaging</h2>
	    <table cellpadding="0" cellspacing="0" class="data-table">
	    <thead>
	    	<tr>
	    		<td style="width: 100px;">Protocol</td>
	    		<td>Account</td>
	    		<td style="width: 100px;">&nbsp;</td>
	    	</tr>
	    </thead>
	    <tbody>
	    <?php 
	        if($profile->hasMessagingServices()) :
	    	$total = $profile->getMessagingServicesCount();
	    	$current = 1;
	    	foreach($profile->getMessagingServices() as $service) :
	    ?>
	    	<tr<?= (($current % 2) == 0) ? ' class="alt-row"' : '' ?>>
	    		<td>
	    		    <?php if($service->providerHasUrl()) : ?>
	    		    <a href="<?= $service->getProviderUrl() ?>" target="_blank"><?= $service->getProviderName() ?></a>
	    		    <?php else : ?>
	    		    <?= $service->getProviderName() ?>
	    		    <?php endif; ?>
	    		</td>
	    		<td><?= $service->getAccountName() ?></td>
	    		<td>
	    			<a class="btn-small" href="/speaker/editim/<?= $service->getId() ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/speaker/delim/' . $service->getId(), 
	    				'Are you sure you want to delete your ' . $service->getProviderName() . ' account?') 
	    			?>
	    		</td>
	    	</tr>
	    <?php
	    	$current++; 
	    	endforeach; 
	    	
	    	else :
	    ?>
	    	<tr>
	    		<td colspan="3">No accounts found</td>
	    	</tr>
	    <?php 
	    	endif;
	    ?>
	    </tbody>
	    </table>
	    
	    <p style="text-align: right;">
	        <a class="btn btn-green" href="/speaker/editim">add account</a>
	    </p>
	</div>
	
	<div class="">
		<a name="web"></a>
	    <h2>Web Services</h2>
	    <table cellpadding="0" cellspacing="0" class="data-table">
	    <thead>
	    	<tr>
	    		<td style="width: 100px;">Provider</td>
	    		<td>URL</td>
	    		<td style="width: 100px;">&nbsp;</td>
	    	</tr>
	    </thead>
	    <tbody>
	    <?php 
	        if($profile->hasWebServices()) :
	    	$total = $profile->getWebServicesCount();
	    	$current = 1;
	    	foreach($profile->getWebServices() as $service) :
	    ?>
	    	<tr<?= (($current % 2) == 0) ? ' class="alt-row"' : '' ?>>
	    		<td>
	    		    <?php if($service->providerHasUrl()) : ?>
	    		    <a href="<?= $service->getProviderUrl() ?>" target="_blank"><?= $service->getProviderName() ?></a>
	    		    <?php else : ?>
	    		    <?= $service->getProviderName() ?>
	    		    <?php endif; ?>
	    		</td>
	    		<td><?= auto_link($service->getUrl()) ?></td>
	    		<td>
	    			<a class="btn-small" href="/speaker/editweb/<?= $service->getId() ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/speaker/delweb/' . $service->getId(), 
	    				'Are you sure you want to delete your ' . $service->getProviderName() . ' address?') 
	    			?>
	    		</td>
	    	</tr>
	    <?php
	    	$current++; 
	    	endforeach; 
	    	
	    	else :
	    ?>
	    	<tr>
	    		<td colspan="3">No accounts found</td>
	    	</tr>
	    <?php 
	    	endif;
	    ?>
	    </tbody>
	    </table>
	    
	    <p style="text-align: right;">
	        <a class="btn btn-green" href="/speaker/editweb">add service</a>
	    </p>
	</div>
    
</div>

<?php endif; ?>

