<?php if(count($events) === 0) : ?>
<p>No events pending approval.</p>
<?php else: foreach($events as $event): ?>

<div class="row row-event">

    <?php $this->load->view('event/_event-icon',array('event' => $event, 'showLink' => true)); ?>
    <div class="text">
        <h3><a href="/event/view/<?= $event->getId() ?>"><?= escape($event->getTitle()) ?></a></h3>

        <p class="info">
    	    <strong><?= date('M j, Y', $event->getStart()); ?></strong> - 
    	    <strong><?= date('M j, Y', $event->getEnd()) ?></strong> at 
    	    <strong><?= escape($event->getLocation()) ?></strong>
        </p>

        <p>
			Event contact: <?= ($event->getContactName() != '') ? $event->getContactName() . ' - ' : '' ?><?= auto_link(escape($event->getContactEmail())); ?><br />
            <?php
                $managers = $event->getManagers();
                if(count($managers) > 0) {
                    $manager = array_shift($managers);
                    echo 'Registered by <a href="/user/view/' . $manager->getId() . '">' . $manager->getName() .' (' . $manager->getUsername() .')</a>';
                    if(count($managers) >= 2) {
                        echo '(+' . (count($managers) - 1) . ')';
                    }
                } 
            ?>
        </p>
        <p>
            <a class="btn-small btn-green" href="/event/approve/<?= $event->getId() ?>">Approve Event</a>
            &nbsp;
            <a class="btn-small" href="/event/delete/<?= $event->getId() ?>">Delete Event</a>
        </p>


    </div>
    <div class="clear"></div>
</div>

<?php endforeach; endif; ?>
