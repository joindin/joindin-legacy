<h1 class="icon-about">Contact</h1>
<?php 
$msg=$this->session->flashdata('msg');
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">
	<p>
    Submit the contact form below to send us a note or ask a question.
    </p>
    
    <?php echo form_open('about/contact', array('class' => 'form-contact')); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>
    
    <div class="row">
    	<label for="your_name">Your Name</label>
    	<?php echo form_input(array('name' => 'your_name', 'id' => 'your_name'), $this->validation->your_name); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="your_email">Your Email</label>
    	<?php echo form_input(array('name' => 'your_email', 'id' => 'your_email'), $this->validation->your_email); ?>
    
        <div class="clear"></div>
    </div>
    
    <div class="row">
    	<label for="your_com">Comments</label>
    	<?php 
		$attr=array(
			'name'	=> 'your_com',
			'id'	=> 'your_com',
			'cols'	=> 40,
			'rows'	=> 5,
			'value'	=> $this->validation->your_com
		);
		echo form_textarea($attr); 
	    ?>
        <div class="clear"></div>
    </div>
	
	<div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit event'); ?>
    </div>

    <?php echo form_close(); ?>
</div>