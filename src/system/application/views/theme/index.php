
<h2>Manage Themes</h2>
<?php
//print_r($themes);
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td><b>Theme Name</b></td>
	<td><b>Event Name</b></td>
	<td><b>Theme Status</b></td>
	<td><b>Theme Created</b></td>
	<td>&nbsp;</td>
</tr>
<?php foreach($themes as $theme): ?>
<tr>
	<td><?php echo $theme->theme_name; ?></td>
	<td><?php echo $theme->event_name; ?></td>
	<td>
		<?php echo ($theme->active==1) ? 'active' : 'inactive'; ?>
	</td>
	<td><?php echo date('m.d.Y',$theme->created_at); ?></td>
	<?php if($theme->active!=1): ?>
	<td><a href="/theme/activate/<?php echo $theme->ID; ?>" class="btn-small">activate</a></td>
	<?php else: echo ''; endif; ?>
</tr>
<?php endforeach; ?>
<tr>
	<td colspan="5" align="right">
		<br/>
		<a href="/theme/add" class="btn-big">Add New Theme</a>
	</td>
</tr>
</table>