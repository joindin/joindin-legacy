<div class="menu">
	<ul>
		<li><a href="/user/main">Dashboard</a>
		<li><a href="/user/manage">Manage Account</a>
	<?php if (user_is_admin()): ?>
		<li class="active"><a href="/user/admin">User Admin</a>
		<li><a href="/event/pending">Pending Events</a>
	<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<?php 
echo form_open('user/admin');
echo form_input('user_search',$this->validation->user_search);
echo form_submit('sub','Search');
//echo form_button('clear','Clear','onClick="document.location.href=\'/user/admin\';"');
echo form_close(); 
?>

<b>Pages:</b>
<?php
for($i=1;$i<=$page_ct;$i++){
    $style=($i==$page) ? 'style="font-weight:bold"' : '';
    echo '<a '.$style.' href="/user/admin/'.$i.'">'.$i.'</a> ';
}
?><br/><br/>
<?php
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
else
{
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php
}
?>

<?php echo form_open('user/admin'); ?>
<table summary="" class="list" width="100%">
<tr class="header">
	<th></th>
	<th>Username</th>
	<th>Detail</th>
	<th>Is Admin?</th>
	<th>Last Login</th>
	<th>Status</th>
</tr>
<?php
$ct=0;
foreach($users as $k=>$v){
	$class 		= ($ct%2==0) ? 'row1' : 'row2';
	$is_admin	= ($v->admin==1) ? '<b style="color:#00E200">Y</b>' : '';
	$last_log	= (!empty($v->last_login)) ? date('m.d.Y H:i:s',$v->last_login): '';
	$active		= (!empty($v->active) && $v->active==1) ? 'act' : 'inact';
	echo sprintf('
		<tr class="%s">
			<td><input type="checkbox" name="sel[]" value="%s"/></td>
			<td><a href="/user/view/%s">%s</a></td>
			<td>
				%s<br/>
				<a href="mailto:%s">%s</a>
			</td>
			<td align="center">%s</td>
			<td>%s</td>
			<td align="right"><a href="#" id="status_link_%s" onClick="toggleUserStatus(%s)">%s</a></td>
		</tr>
	',$class,$v->ID,$v->ID,escape($v->username),escape($v->full_name),escape($v->email),escape($v->email),
	$is_admin,$last_log,$v->ID,$v->ID,$active);
	$ct++;
}
?>
<tr>
	<td colspan="6">
		<?php echo form_submit('um','Delete Selected'); ?>
	</td>
</tr>
</table>
<?php echo form_close(); ?>
