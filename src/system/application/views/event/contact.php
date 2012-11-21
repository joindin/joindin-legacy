<?php
// Contact event admins form
?>
<h2>Contact Event Admins</h2>

<p><b>Event:</b> <a href="/event/view/<?php echo $detail[0]->ID; ?>"><?php echo $detail[0]->event_name; ?></a><br/>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php echo form_open('event/contact/'.$detail[0]->ID); ?>
<div class="box">
    <div class="row">
        <label for="subject">Subject:</label>
        <?php echo form_input('subject'); ?>
    </div>
    <div class="clear"></div>
    <div class="row">
        <label for="subject">Comments:</label>
        <?php echo form_textarea('comments'); ?>
    </div>
    <div class="clear"></div>
    <div class="row" align="right">
        <?php echo form_submit('sub','Send Comments'); ?>
    </div>
</div>

<?php echo form_close();
