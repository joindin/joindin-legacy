<div class="menu">
	<ul>
		<li><a href="/user/main">Dashboard</a>
		<li><a href="/user/manage">Manage Account</a>
	<?php if (user_is_admin()): ?>
		<li class="active"><a href="/user/admin">User Admin</a>
	<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<?php 
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<table summary="" class="list">
<tr class="header">
	<th>Username</th>
	<th>Email</th>
	<th>Full Name</th>
	<th>Is Admin?</th>
	<th>Last Login</th>
	<th>Status</th>
</tr>
<?php
$ct=0;
foreach($users as $k=>$v){
	$class 		= ($ct%2==0) ? 'row1' : 'row2';
	$is_admin	= ($v->admin==1) ? 'yes' : 'no';
	$last_log	= (!empty($v->last_login)) ? date('m.d.Y H:i:s',$v->last_login): '';
	$active		= (!empty($v->active) && $v->active==1) ? 'active' : 'inactive';
	echo sprintf('
		<tr class="%s">
			<td><a href="/user/view/%s">%s</a></td>
			<td><a href="mailto:%s">%s</a></td>
			<td>%s</td>
			<td align="center">%s</td>
			<td>%s</td>
			<td>%s</td>
		</tr>
	',$class,$v->ID,$v->username,$v->email,$v->email,$v->full_name,
	$is_admin,$last_log,$active);
	$ct++;
}
?>
</table>