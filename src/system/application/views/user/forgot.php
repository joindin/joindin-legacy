<h1>Forgot My Password</h1>

<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
    <?php echo form_open('user/forgot'); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
        <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <div class="row">
        <p>
            If you've forgotten your password, enter the username or email address associated with the 
            account below and hit "Request Password". Instructions on how to reset your password will be sent
            to the email address for that account if the information validates.
        </p>
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="user">Username</label>
        <?php echo form_input(array('name' => 'user', 'id' => 'user'), $this->validation->user); ?>
        <br/>
        <label for="user">Email Address</label>
        <?php echo form_input(array('name' => 'email', 'id' => 'email'), $this->validation->email); ?>
    
        <div class="clear"></div>
    </div>
    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Request Password'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>
