
<h1 class="title">Manage Users</h1>
<?php
//echo '<pre>'; print_r($users); echo '</pre>';
?>

<table cellpadding="3" cellspacing="0" border="0" width="100%" id="user_admin_tbl">
<tr class="header">
	<td>Username</td>
	<td>Email</td>
	<td>Full Name</td>
	<td>Is Admin?</td>
	<td>Last Login</td>
	<td>Status</td>
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