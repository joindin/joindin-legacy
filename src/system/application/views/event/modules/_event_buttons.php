<p class="admin">
<?php if ($admin): ?>
    <?php if (isset($event_detail->pending) && $event_detail->pending==1) : ?>
        <a class="btn-small" href="/event/approve/<?php echo $event_detail->ID; ?>">Approve Event</a>
    <?php endif; ?>
<?php endif; ?>
</p>
