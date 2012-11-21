<?php
// Private event
?>
<h2>Private Event: <?php echo $detail[0]->event_name; ?></h2>
<p>
This event has been marked as private and is only available 
to those invited. If you would like more information on this event, 
please <a href="/about/contact">contact us</a>.
</p>
<?php 
// If we don't have any event admins, we don't know who to send it to
if ($is_auth && count($admins)>0): ?>
<p>
Please <a href="/event/invite/<?php echo $detail[0]->ID;?>/request">click here</a> to request an invite to this event.
</p>
<?php endif; 
