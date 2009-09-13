<h1 class="title">Delete event</h1>

<?= form_open('event/delete/' . $event->getId()); ?>
<p>
    Are you sure you want to delete the following event:
</p>

<p>
    <strong><?= $event->getTitle() ?></strong> at <?= date('M j, Y', $event->getStart()) ?>
</p>

<?php if($event->isPending()) : ?>
<p style="color: #FF0000;">
    <?php $this->load->view('message/error', array('message' => 'This event is still in pending state. It will be permanently deleted! No going back!')); ?>
</p>
<?php endif; ?>

<p>
    <input class="btn-small" type="submit" value="Delete Event" name="answer"> 
    or <a href="/event/view/<?= $event->getId() ?>">cancel</a>
</p>
<?= form_hidden('event_id', $event->getId()) ?>
<?= form_close(); ?>
