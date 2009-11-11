
<?php echo '<h2>Pending Claims</h2>'; ?>

<style>
#claims_table { width: 100%; }
#claims_table td { padding: 3px; }
#claims_table td.app_den { width: 30px; font-size: 9px; }
</style>

<?php echo form_open('event/claim/'.$eid); ?>
<div class="box">
	<div class="row">
	<table cellpadding="0" cellspacing="0" border="0" id="claims_table">
	<tr>
		<td class="app_den" align="center">APPROVE</td>
		<td class="app_den" align="center">DENY</td>
		<td><b>Session Name</b></td>
		<td><b>Speaker</b></td>
		<td><b>Claiming User</b></td>
	</tr>
	<?php
		foreach($claims as $k=>$claim): ?>
		<tr>
			<td align="center"><?php echo form_radio('claim['.$k.'_'.$claim->uid.'_'.$claim->rid.']','approve'); ?></td>
			<td align="center"><?php echo form_radio('claim['.$k.'_'.$claim->uid.'_'.$claim->rid.']','deny'); ?></td>
			<td><?php echo '<a href="'.$claim->rid.'">'.$claim->talk_title.'</a>'; ?></td>
			<td><?php echo $claim->speaker; ?></td>
			<td><?php echo '<a href="/user/view/'.$claim->uid.'">'.$claim->claiming_name.'</a>'; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	</div>
	<div class="row">
		<?php echo form_submit('sub','Submit Updates'); ?>
	</div>
</div>
<?php echo form_close(); ?>