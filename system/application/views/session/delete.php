<h1 class="title">Delete session</h1>

<?= form_open('session/delete/' . $session->getId()); ?>
<p>
    Are you sure you want to delete the following session:
</p>

<p>
    <strong><?= $session->getTitle() ?></strong> at <a href="/event/view/<?= $session->getEventId() ?>" target="_blanc"><?= $session->getEventTitle() ?></a><br />
    By <?= $session->getSpeaker() ?> on <?= date('M j, Y', $session->getDate()) ?>
</p>

<p style="color: #FF0000;">
    <?php $this->load->view('message/error', array('message' => 'This will also delete any comments made on the session! It will be permanent! No crying afterwards.')); ?>
</p>

<p>
    <input class="btn-small" type="submit" value="Delete session" name="answer"> 
    or <a href="/session/view/<?= $session->getId() ?>">cancel</a>
</p>
<?= form_hidden('session_id', $session->getId()) ?>
<?= form_close(); ?>
