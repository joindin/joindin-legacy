<?php
$this->load->view('user/_nav_sidebar', array(
        'pending_events' => $pending_events
     )
);
?>
<div class="menu">
    <ul>
        <li><a href="/user/main">Dashboard</a>
        <li class="active"><a href="/user/manage">Manage Account</a>
    <?php if (user_is_admin()): ?>
        <li><a href="/user/admin">User Admin</a>
        <li><a href="/event/pending">Pending Events</a>
    <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<?php 
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
    <?php echo form_open('user/manage', array('class' => 'form-user-manage')); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>
    
    <div class="row">
        <label for="full_name">Full Name</label>
        <?php echo form_input(array('name' => 'full_name', 'id' => 'full_name', 'value' => $curr_data[0]->full_name), $this->validation->full_name); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="email">Email Address</label>
        <?php echo form_input(array('name' => 'email', 'id' => 'email', 'value' => $curr_data[0]->email), $this->validation->email); ?>

        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="twitter">Twitter Username</label>
        <?php echo form_input(array('name' => 'twitter_username', 'id' => 'twitter_username', 'value' => $curr_data[0]->twitter_username), $this->validation->twitter_username); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="pass">Password</label>
        <?php echo form_input(array('type' => 'password', 'name' => 'pass', 'id' => 'pass')); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="pass_conf">Confirm Password</label>
        <?php echo form_input(array('type' => 'password', 'name' => 'pass_conf', 'id' => 'pass_conf')); ?>

        <div class="clear"></div>
    </div>
    
    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Save changes'); ?>
    </div>

    <?php echo form_close(); ?>
</div>
