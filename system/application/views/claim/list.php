<?php menu_pagetitle('Pending claims'); ?>

<h2>Pending claims</h2>

<?php if(count($claims) === 0) : ?>

<div class="box detail">
    Move along now, nothing to see here. <a href="/">homepage</a>
</div>

<?php else : foreach($claims as $claim) : ?>
    <div class="box detail">
        <h4><?= $claim->getSessionTitle() ?></h4>
        <p>
            by <i><?= $claim->getSession()->getSpeakerName() ?></i> at 
            <a href="/event/view/<?= $claim->getSession()->getEventId() ?>" target="_blank"><?= $claim->getSession()->getEventTitle() ?></a> on
            <i><?= date('M d, Y', $claim->getSession()->getDate()) ?></i>.
        </p>

        <p>
            Claimed by <strong><?= $claim->getSpeakerName() ?></strong> as 
            <?php if($claim->getTalk() == null) : ?>
            <strong>a new talk</strong>
            <?php else : ?>
            <strong><?= $claim->getTalkTitle() ?></strong> 
            <?php endif; ?>
            on <i><?= date('m/d/Y', $claim->getDate()) ?></i>.
        </p>
        <p>
            <a href="/claim/approve/<?= $claim->getId() ?>" class="btn btn-green">approve</a> 
            <a href="/claim/reject/<?= $claim->getId() ?>" class="btn">reject</a>
        </p>
    </div>
<?php endforeach; endif; ?>
