<h1>Register a new account</h1>

<?php 
$msg=$this->session->flashdata('msg');
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif;

$error_msg=$this->session->flashdata('error_msg');
if (!empty($error_msg)) :
?>
<?php $this->load->view('msg_error', array('msg' => $error_msg)); ?>
<?php endif; ?>

<div class="box">

    <p>
        Use the form below to register a new account for the site. 
        Username, password and email address fields are required.
    </p>
    
    <?php echo form_open('user/register', array('class' => 'form-register')); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <div class="row">
        <label for="user">Username</label>
        <?php echo form_input(array('name' => 'user', 'id' => 'user'), $this->validation->user); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="pass">Password</label>
        <?php echo form_input(array('name' => 'pass', 'id' => 'pass', 'type' => 'password')); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="passc">Confirm Password</label>
        <?php echo form_input(array('name' => 'passc', 'id' => 'passc', 'type' => 'password')); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="email">Email</label>
        <?php echo form_input(array('name' => 'email', 'id' => 'email'), $this->validation->email); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="full_name">Full Name</label>
        <?php echo form_input(array('name' => 'full_name', 'id' => 'full_name'), $this->validation->full_name); ?>
    
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="twitter">Twitter Username</label>
        <?php echo form_input(array('name' => 'twitter_username', 'id' => 'twitter_username'), $this->validation->twitter_username); ?>
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="cinput">Spambot check</label>
        <span>
          <?php echo form_input(array('name' => 'cinput', 'id' => 'cinput'), ""); ?>
          = <b><?php echo $captcha['text']; ?></b>
        </span>
        <div class="clear"></div>
    </div>


    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Register'); ?>
    </div>

    <?php echo form_close(); ?>
</div>

