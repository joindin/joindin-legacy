<?php

?>
<div class="box">
	<h4>Event Admin</h4>
	<div class="ctn">
		<ul>
		<li><a href="/event/edit/<?php echo $eid; ?>">Edit Event</a>
		<li><a href="/talk/add/event/<?php echo $eid; ?>">Add a New Talk</a>
		<li><a href="/event/claim/<?php echo $eid; ?>">Pending Claims</a>
		<li><a href="/event/import/<?php echo $eid; ?>">Import Event Info</a>
		<li><a href="/event/delete/<?php echo $eid; ?>" style="color:#D3000E;font-weight:bold">Delete event</a>
		</ul>
	</div>
</div>
