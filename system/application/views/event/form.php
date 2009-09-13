<?php menu_pagetitle($title); ?>

<?= form_open_multipart($action); ?>
<?php
if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error)); 
}
?>

<div class="row">
    <label for="title">Title</label>
    <?= form_input('title', $event->getTitle()); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="start_string">Start</label>
    <small>Start date for the event (mm/dd/yyyy).</small>
    <input type="text" id="start_string" name="start_string" class="datepicker" value="<?= ($event->getStart() != '') ? date('m/d/Y', $event->getStart()) : '' ?>" />
    <script type="text/javascript">
        $(document).ready(function(){
            $("#start_string").datepicker();
        });
    </script>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="end_string">End</label>
    <small>End date for the event (mm/dd/yyyy).</small>
    <input type="text" id="end_string" name="end_string" class="datepicker" value="<?= ($event->getEnd() != '') ? date('m/d/Y', $event->getEnd()) : '' ?>" />
    <script type="text/javascript">
        $(document).ready(function(){
            $("#end_string").datepicker();
        });
    </script>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="location">Location</label>
    <?= form_input('location', $event->getLocation()); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="timezone">Timezone</label>
    <select name="timezone">
    <?php foreach(range(-12, 12) as $offset) : ?>
        <option value="<?= $offset ?>">UTC <?= $offset ?> hour<?= ($offset != '-1' && $offset != '1') ? 's' : '' ?></value>
    <?php endforeach; ?>
    </select>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="description">Description</label>
    <?= form_textarea(array(
        'name'	=> 'description',
        'cols'	=> 45,
        'rows'	=> 12,
        'value'	=> $event->getDescription()
    )); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="icon">Icon</label>
    <input type="file" name="icon_file" size="20" />
    <div class="clear"></div>
</div>

<div class="row">
    <label for="links">Link(s)</label>
    <small>Multiple links can be seperated by a ','</small>
    <?= form_input('link', $event->getLink()); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="hashtags">Hashtag(s)</label>
    <small>Multiple hashtags can be seperated by a ','</small>
    <?= form_input('event_hashtag', $event->getHashtag()); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="active">Active</label>
    <?= form_checkbox('active', '1', $event->isActive()); ?>
    <div class="clear"></div>
</div>

<div class="row row-buttons">
    <?= form_submit(array('name' => 'sub', 'class' => 'btn'), 'Save event'); ?>
    &nbsp;or&nbsp;<a href="/event">cancel</a>
    <div class="clear"></div>
</div>


<?php echo form_close(); ?>
    
