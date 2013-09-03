<h1>Grant Access</h1>
<?php echo $status; ?>
<div class="box">
    <?php
    if ($status == 'invalid'):
    ?>
    <div class="row">
        <p> Access cannot be granted for this key.  </p>
        <div class="clear"></div>
    </div>
    <?php
    elseif ($status == 'keyfail'):
    ?>
    <div class="row">
        <p>The client identifier <code>api_key</code> was not recognised.</p>
        <div class="clear"></div>
    </div>
    <?php
    elseif ($status == 'callbackfail'):
    ?>
    <div class="row">
        <p>Callback URL <code>callback</code> is missing or incorrect.</p>
        <div class="clear"></div>
    </div>
    <?php
    elseif ($status == 'allow'):
    ?>
    <div class="row">
        <p>Access was granted, but we cannot return you to your application</p>
        <div class="clear"></div>
    </div>
    <?php
    elseif ($status == 'deny'):
    ?>
    <div class="row">
        <p>Access was not granted.  We hope you enjoy joind.in</p>
        <?php if (!empty($callback_url)): ?>
            <p>
                <a href="<?php echo $callback_url; ?>">Return to application</a>
            </p>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
    <?php else: ?>

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
    <?php endif; ?>

</div>

