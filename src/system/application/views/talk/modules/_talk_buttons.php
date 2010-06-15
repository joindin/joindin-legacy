<p class="admin">
<?php if($admin):?>
	<a class="btn-small" href="/talk/delete/<?php echo $detail->tid; ?>">Delete talk</a>	
	<a class="btn-small" href="/talk/edit/<?php echo $detail->tid; ?>">Edit talk</a>
<?php endif; ?>
<?php
if(empty($claim_details) || count($claim_details)<count($speaker)): ?>
	<a class="btn-small" href="#" id="claim_btn" onClick="claimTalk(<?php echo $detail->tid; ?>)">Claim This Talk</a>	
<?php endif; ?>
</p>