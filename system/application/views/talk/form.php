<?php
if($talk->isNew()) {
    menu_pagetitle('Add Talk');
} else {
    menu_pagetitle('Edit Talk');
}

// Load some sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="box">

<?= form_open($action); ?>

<div class="row">
    <label for="title">Title</label>
    <?= form_input(array('id' => 'title', 'name' => 'title', 'value' => $talk->getTitle())) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="description">Description</label>
    <?= form_textarea(array('id' => 'description', 'name' => 'description', 'cols' => 20, 'rows' => 6, 'value' => $talk->getDescription())) ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="abstract">Abstract</label>
    <?= form_textarea(array('id' => 'abstract', 'name' => 'abstract', 'cols' => 20, 'rows' => 15, 'value' => $talk->getAbstract())) ?>
    <div class="clear"></div>
</div>

<div class="row row-buttons">
    <?= form_submit(array('class' => 'btn', 'name' => 'save'), 'Save'); ?>&nbsp;
    or <a href="/speaker/talks">cancel</a>
</div>
<?= form_close(); ?>

</div>
