<p class="admin">
<?php if ($admin): ?>
    <?php if (isset($event_detail->pending) && $event_detail->pending==1) {
        echo '<a class="btn-small" href="/event/approve/'.$event_detail->ID.'">Approve Event</a>';
    } ?>
<?php else: ?>
    <?php if (user_is_auth()) { ?>
        <a class="btn-small" href="#" id="claim-event-btn">Claim event</a>
    <?php } ?>
<?php endif; ?>
</p>
