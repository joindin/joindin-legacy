<?php if($admin): ?>
<p class="admin">
	<!--<a class="btn-small" href="/event/codes/<?=$event_detail->ID?>">Get talk codes</a>-->
	<?php if(isset($event_detail->pending) && $event_detail->pending==1){
		echo '<a class="btn-small" href="/event/approve/'.$event_detail->ID.'">Approve Event</a>';
	} ?>
	<a class="btn-small" href="#" onClick="claimEvent(<?=$event_detail->ID?>);return false;">Claim event</a>
	<a class="btn-small" href="/event/import/<?php echo $event_detail->ID; ?>">Import Event Info</a>
</p>
<?php endif; ?>