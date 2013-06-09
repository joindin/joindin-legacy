<h1>Login</h1>

<?php if (!empty($msg)): ?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
    <?php echo form_open('user/login', array('class' => 'form-login')); ?>

    <?php if (!empty($this->validation->error_string)): ?>
        <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <div class="row">
        <p>
            Please login below. If you do not have an account you can <a href="/user/register">create</a> a new one.
        </p>
        <div class="clear"></div>
    </div>

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
    
    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Login'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>

<div class="box">
    <h2>Wait, why am I here?</h2>
    <p>
    If you were just on another page and clicked a link or tried to access something else, 
    there's a good chance you'll need to log in to get there. Don't have an account? Well 
    <a href="/user/register">go ahead and make one</a> and see what you're missing!
    </p>
</div>
