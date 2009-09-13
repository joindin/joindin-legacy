<?php
menu_pagetitle('Speaker: Instant Messaging Account');

$this->load->view('sidebar/user-navigation.php');
$this->load->view('sidebar/claim-session.php');

$this->load->view('message/area');
?>

<div class="box">
    <?php
        if('' !== $service->getId()) {
            echo form_open('/speaker/editim/' . $service->getId());
        } else {
            echo form_open('/speaker/editim');
        }
    ?>
    <div class="row">
        <label for="im_network_id">Network Name</label>
        <?php echo form_dropdown('messaging_service_provider_id', $providers, $service->getProviderId()) ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="account_name">Account Name</label>
        <?php echo form_input(array('name' => 'account_name', 'id' => 'account_name', 'value' => $service->getAccountName())); ?>

        <div class="clear"></div>
    </div>
	
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $service->getId(), 'speaker_profile_id' => $speaker->getId())) ?>
        <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save account') ?>
        &nbsp;or&nbsp;<a href="/speaker/profile#messaging">cancel</a>
    	<?= form_close() ?>
    </p>
    <?= form_close() ?>
    
</div>