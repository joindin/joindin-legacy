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
if(empty($message)) {
    $message = $this->session->flashdata('message');
}
if(!empty($message)) { 
    $this->load->view('message/info', array('message' => $message));
} 
?>

<table summary="" class="list">
    <tr class="header">
	    <th>Username</th>
	    <th>Email</th>
	    <th>Display Name</th>
	    <th>Is Admin?</th>
	    <th>Last Login</th>
	    <th>Status</th>
    </tr>
<?php
$count = 0;
foreach($users as $user) {
?>	
	<tr class="<?= ($count %2 === 0) ? 'row1' : 'row2' ?>">
	    <td>
	        <a href="/user/view/<?= $user->getId() ?>"><?= escape($user->getUsername()) ?></a>
        </td>
	    <td>
	        <a href="mailto:<?= escape($user->getEmail()) ?>"><?= escape($user->getEmail()) ?></a>
        </td>
	    <td><?= escape($user->getDisplayName()) ?></td>
	    <td style="text-align: center; font-size: 90%;"><?= ($user->isAdmin()) ? 'yes' : 'no' ?></td>
	    <td>
	        <?php if($user->getLastLogin() !== null) :
	            echo date('m.d.Y H:i:s',$user->getLastLogin());
	        endif; ?>
	    </td>
	    <td style="text-align: center; font-size: 90%;"><?= ($user->isActive()) ? 'active' : 'inactive' ?></td>
	</tr>
<?php
$count++;	
}	
?>
</table>
