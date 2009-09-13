<?php if ($event->getAttendanceCount() == 0): 
    $this->load->view('message/info', array('message' => 'No attendees so far.')); ?>
<?php else: ?>
<ul>
    <?php foreach($event->getAttendees() as $attendee): ?>
    <li>
        <a href="/user/view/<?= $attendee->getId() ?>"><?= escape($attendee->getName()) ?></a>
        <?php if($event->userIsSpeaker($attendee->getId())) : ?>
        &nbsp;(speaker)
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
