<h1>Grant Access</h1>
<div class="box">
    <?php echo form_open('user/oauth_allow', array('class' => 'form-oauth')); ?>

    <?php if (!empty($this->validation->error_string)): ?>
        <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <div class="row">
    	<p>
    		You have arrived here because you asked another application to link to your joind.in account.  If you did not expect to see this page, choose "deny".
    	</p>
        <div class="clear"></div>
    </div>

    <div class="row">
    	<label for="access">Permit access to your account?</label>
    	Allow <?php echo form_radio('access', 'allow', false); ?>
    	Deny <?php echo form_radio('access', 'deny', true); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>

