<?php
/* 
 * Define access to the elements of a speaker's profile
 */

menu_pagetitle('Manage Speaker Profile Access');

$this->load->view('user/_nav_sidebar');

?>
<div class="menu">
    <ul>
    <li><a href="/speaker/profile">Speaker Profile</a>
    <li class="active"><a href="/speaker/access">Profile Access</a>
    </ul>
    <div class="clear"></div>
</div>

<div class="box" style="margin-bottom:3px">
    <p style="text-align: center;">
        <a class="btn-big btn" href="/speaker/access/add">Add Profile Access</a>
    </p>
</div>

<h2>Current Access</h2>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
<tr>
    <td><b>Token Name</b></td>
    <td><b>Token Description</b></td>
    <td><b>Create Date</b></td>
    <td><b>Viewable</b></td>
    <td colspan="2">&nbsp;</td>
</tr>
<?php foreach ($access_data as $access): ?>
<tr>
    <td><?php echo $access->access_token; ?></td>
    <td><?php echo $access->description; ?></td>
    <td><?php echo date('m.d.Y H:i:s', $access->created); ?></td>
    <td align="center"><?php echo ($access->is_public=='Y') ? 'public' : ''?></td>
    <td width="40"><a href="/speaker/access/edit/<?php echo $access->ID; ?>" class="btn-small">edit</a></td>
    <td width="50"><a href="/speaker/access/delete/<?php echo $access->ID; ?>" class="btn-small">delete</a></td>
</tr>
<?php endforeach; ?>
</table>
