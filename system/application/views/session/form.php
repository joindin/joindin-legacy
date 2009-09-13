<?php menu_pagetitle($title); ?>

<div class="box">

<?= form_open($action); ?>

<?php
if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error));
}
?>

<div class="row">
    <label for="">Event</label>
    <a href="/event/view/<?= $session->getEventId() ?>" target="_blank"><?= escape($session->getEvent()->getTitle()) ?></a>&nbsp;
    <i>(<?= date('M dS, Y', $session->getEvent()->getStart()) ?> - <?= date('M dS, Y', $session->getEvent()->getEnd()) ?>)</i>
    <div class="clear"></div>
</div>

<?php if($session->isNew()) : ?>
<script type="text/javascript">
    function fetchTalk()
    {
        var token = $('#access_token').attr('value');
        var url = '/talk/json/' + token;
        
        $.ajax({
            type: "GET",
            url	: url,
            processData: false,
            success: function(data){
                response = eval('('+data+')');
                
                if(response.error) {
                    alert('An error occurred: ' + response.error.message);
                } else {
                    $('#title').attr('value', response.talk.title);
                    $('#description').attr('value', response.talk.description);
                    $('#speaker_name').attr('value', response.speaker.name);
                }
            }
		
    	});
    
    }
</script>
<div class="row">
    <label for="token">Access Token</label>
    <small>Use an access token to automatically fetch session data from the speakers talk.</small><br />
    <input type="text" id="access_token" name="access_token" value="" style="width: 150px; display: inline;"/>
    <a class="btn-small" href="#" onClick="fetchTalk(); return false;">Fetch data</a>
</div>
<?php endif; ?>

<div class="row">
    <label for="title">Title</label>
    <?= form_input(array('id' => 'title', 'name' => 'title', 'value' => $session->getTitle())) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="speaker_name">Speaker Name</label>
    <?= form_input(array('id' => 'speaker_name', 'name' => 'speaker_name', 'value' => $session->getSpeakerName())) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="date">Date</label>
    <small>Date of the session (mm/dd/yyyy).</small>
    <input type="text" id="date_string" name="date_string" class="datepicker" value="<?= ($session->getDate() != '') ? date('m/d/Y', $session->getDate()) : '' ?>" />
    <script type="text/javascript">
        $(document).ready(function(){
            $("#date_string").datepicker({
             minDate: new Date(<?= date('Y', $session->getEvent()->getStart()) ?>, <?= date('m', $session->getEvent()->getStart()) ?> - 1, <?= date('d', $session->getEvent()->getStart()) ?>),
             maxDate: new Date(<?= date('Y', $session->getEvent()->getEnd()) ?>, <?= date('m', $session->getEvent()->getEnd()) ?> - 1, <?= date('d', $session->getEvent()->getEnd()) ?>)
            });
        });
    </script>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="category_id">Category</label>
    <?= form_dropdown('category_id', $categories, $session->getCategoryId()) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="language_id">Language</label>
    <?= form_dropdown('language_id', $languages, $session->getLanguageId()) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="description">Description</label>
    <?= form_textarea(array('id' => 'description', 'name' => 'description', 'cols' => 20, 'rows' => 10, 'value' => $session->getDescription())) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="slides_link">Slides Link</label>
    <?= form_input(array('id' => 'slides_link', 'name' => 'slides_link', 'value' => $session->getSlidesLink())) ?>
    <div class="clear"></div>
</div>

<div class="row row-buttons">
    <?= form_submit(array('class' => 'btn', 'name' => 'save'), 'Save'); ?>&nbsp;
    or <a href="/session/view/<?= $session->getId() ?>">cancel</a>
</div>
<?= form_close(); ?>

</div>
