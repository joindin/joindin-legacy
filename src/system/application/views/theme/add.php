
<?php
if (!empty($this->validation->error_string)) {
    $this->load->view('msg_info', array('msg' => $this->validation->error_string));
}
?>

<?php echo form_open_multipart('theme/add');?>
<div id="box">
    <a href="/theme">Back to Theme List</a><br/><br/>
    <div class="row">
    <label for="theme_title">Theme Name:</label>
    <?php echo form_input('theme_name', $this->validation->theme_name);?>
    <div class="clear"></div>
    </div>

    <div class="row">
    <label for="theme_title">Theme for Event:</label>
    <?php echo form_dropdown('theme_event', $this->user_events, $this->validation->theme_event); ?>
    <div class="clear"></div>
    </div>
    
    <div class="row">
    <label for="theme_desc">Theme Description:</label>
    <?php 
        $arr=array(
            'name'	=> 'theme_desc',
            'id'	=> 'theme_desc',
            'value'	=> $this->validation->theme_desc,
            'cols'	=> 50,
            'rows'	=> 10
        );
        echo form_textarea($arr);
    ?>
    <div class="clear"></div>
    </div>

    <div class="row">
    <label for="theme_title">Make theme active?</label>
    <?php echo form_checkbox('theme_active',1); ?> Yes
    <div class="clear"></div>
    </div>

    <div class="row">
    <label for="theme_title">Theme Style (CSS):</label>
    <?php echo form_upload('theme_style'); ?>
    <div class="clear"></div>
    </div>
    
    <div class="row">
    <?php echo form_submit('sub','Submit Theme');?>
    <div class="clear"></div>
    </div>
    
</div>
</form>
