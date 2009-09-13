<?php
menu_pagetitle('Speaker: Web Service Account');
    
$this->load->view('sidebar/user-navigation.php');
$this->load->view('sidebar/claim-session.php');

$this->load->view('message/area');
?>

<div class="box">
    <?php
        if('' !== $service->getId()) {
            echo form_open('/speaker/editweb/' . $service->getId());
        } else {
            echo form_open('/speaker/editweb');
        }
	?>
	<div class="row">
        <label for="web_type_id">Type</label>
        <?php echo form_dropdown('web_service_provider_id', $providers, $service->getWebServiceProviderId()) ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="account_url">Account URL</label>
        <?php echo form_input(array('name' => 'url', 'id' => 'url', 'value' => $service->getUrl())); ?>

        <div class="clear"></div>
    </div>
	
	<p style="margin-top: 30px; text-align: right;">
        <?= form_hidden(array('id' => $service->getId(), 'speaker_profile_id' => $speaker->getId())) ?>
        <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save') ?>
        &nbsp;or&nbsp;<a href="/speaker/profile#web">cancel</a>
    	<?= form_close() ?>
    </p>
</div>