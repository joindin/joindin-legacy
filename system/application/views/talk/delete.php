<?php menu_pagetitle('Delete Talk'); ?>
<h1 class="title">Delete talk</h1>

<?= form_open('talk/delete/' . $talk->getId()); ?>
<p>
    Are you sure you want to delete the following talk:
</p>

<p>
    <strong><?= $talk->getTitle() ?></strong><br />
    <?= $talk->getDescription() ?>
</p>

<p style="color: #FF0000;">
    <?php $this->load->view('message/error', array('message' => 'The talk will be deleted permanently! No refunds.')); ?>
</p>

<p>
    <input class="btn-small" type="submit" value="Delete talk" name="answer"> 
    or <a href="/speaker/talks">cancel</a>
</p>
<?= form_hidden('talk_id', $talk->getId()) ?>
<?= form_close(); ?>

