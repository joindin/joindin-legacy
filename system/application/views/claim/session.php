<?php menu_pagetitle('Claim Session'); ?>

<?php $this->load->view('message/area'); ?>

<div class="box">

    <?= form_open("/claim/session/{$session->getId()}"); ?>
    <h2><?= escape($session->getTitle()) ?></h2>
    <p>
        on <?= date('M d, Y', $session->getDate()); ?> at <a href="/event/view/<?= $session->getEventId() ?>" target="_blank"><?= $session->getEventTitle() ?></a>&nbsp;
    </p>
    <p>
        You can claim this session and connect it to one of the talks in your speaker 
        profile, or you can create a new talk with the sessions data.
    </p>

    <div class="row">
        <label for="talk_id">Your talks</label>
        <select name="talk_id">
            <option value="">Create a new talk ...</option>
            <?php foreach($speaker->getTalks() as $talk) : ?>
            <option value="<?= $talk->getId() ?>"><?= escape($talk->getTitle()) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="clear"></div>
    </div>

    <div class="row row-buttons">
        <input class="btn" type="submit" name="claim" value="Claim" />&nbsp;
        or&nbsp;<a href="/session/view/<?= $session->getId() ?>">cancel</a>
        <div class="clear"></div>
    </div>

    <?= form_close() ?>

</div>
