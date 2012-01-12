<?php
// Pending event
?>
<h2>This event is pending approval</h2>
<p>
The event <b>"<?php echo $detail[0]->event_name; ?>"</b> is pending approval by the <?php echo $this->config->item('site_name'); ?> admins. If
you are the organizer of the event and have questions about its approval, please <a href="/about/contact">
let us know</a>.
</p>
