
<h2>Manage Themes</h2>
<?php
//print_r($themes);
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="stdTable">
<tr>
    <td><b>Theme Name</b></td>
    <td><b>Event Name</b></td>
    <td><b>Theme Status</b></td>
    <td><b>Theme Created</b></td>
    <td>&nbsp;</td>
</tr>
<?php foreach ($themes as $theme): 
$bg=($theme->active==1) ? '#E9E9E9' : '#FFFFFF';
?>
<tr style="background-color:<?php echo $bg; ?>">
    <td><?php echo escape($theme->theme_name); ?></td>
    <td><a href="/event/view/<?php echo $theme->event_id; ?>"><?php echo
            escape($theme->event_name); ?></a></td>
    <td>
        <?php echo ($theme->active==1) ? 'active' : 'inactive'; ?>
    </td>
    <td><?php echo date('m.d.Y', $theme->created_at); ?></td>
    <?php if ($theme->active!=1): ?>
        <td><a href="/theme/activate/<?php echo $theme->ID; ?>" class="btn-small">activate</a></td>
    <?php else: echo '<td>&nbsp;</td>'; endif; ?>
    <td>
        <a href="/theme/delete/<?php echo $theme->ID; ?>" class="btn-small">delete</a>
    </td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="5" align="right">
        <br/>
        <a href="/theme/add" class="btn-small">Add New Theme</a>
    </td>
</tr>
</table>
