<p class="admin">
<?php if($admin): ?>
	<!--<a class="btn-small" href="/event/codes/<?php echo $event_detail->ID?>">Get talk codes</a>-->
	<?php if(isset($event_detail->pending) && $event_detail->pending==1){
		echo '<a class="btn-small" href="/event/approve/'.$event_detail->ID.'">Approve Event</a>';
	} ?>
	<a class="btn-small" href="/event/import/<?php echo $event_detail->ID; ?>">Import Event Info</a>
<?php else: ?>
	<a class="btn-small" href="#" id="claim-event-btn">Claim event</a>
<?php endif; ?>
</p>