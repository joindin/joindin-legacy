
<?php echo '<h2>Pending Claims</h2>'; ?>

<style>
#claims_table { width: 100%; }
#claims_table td { padding: 3px; }
#claims_table td.app_den { width: 30px; font-size: 9px; }
</style>

<p>
Below you'll find a list of claims visitors to the site have made on the sessions for this event. 
The "Speaker" field shows the speaker name(s) and the "Claiming User" is the Joind.in user trying to
claim the session. You can then accept/deny based on any match between them.
</p>

<?php echo form_open('event/claim/'.$eid); ?>
<div class="box">
	<a href="/event/view/<?php echo $eid; ?>">Back to event</a>
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
			<td>
				<?php echo '<a href="/talk/view/'.$claim->rid.'">'.$claim->talk_title.'</a>'; ?><br/>
				<span style="font-size:9px">@<?php echo $claim->event_name; ?></span>
			</td>
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