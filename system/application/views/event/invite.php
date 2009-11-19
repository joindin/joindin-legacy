<?php
// Manage invites for users on an event
?>
<style>
#invite_list { width: 100%; }
#invite_list tr.header td { font-weight: bold; }
#invite_list td { padding: 3px; }
#invite_list span.pending { color: #D60010; font-weight: bold; }
#invite_list span.accepted { color: #13D600; font-weight: bold; }
</style>

<h2>Event Invites</h2>
<p>Viewing invites for <a href="/event/view/<?php echo $evt_detail[0]->ID; ?>"><?php echo $evt_detail[0]->event_name; ?></a></p>

<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php echo form_open('event/invite/'.$eid); ?>
<div class="box">
    <div class="row">
    	<label for="event_name">Invite Username:</label>
		<?php echo form_input('user'); ?>
    </div>
	<div class="row">
		<?php echo form_submit('sub','Send Invite'); ?>
	</div>
</div>
<?php echo form_close(); ?>

<?php
// Display the list of private attendees
//var_dump($invites);
?>
<table cellpadding="0" cellspacing="0" border="0" id="invite_list">
<tr class="header">
	<td></td>
	<td>Full Name</td>
	<td>Username</td>
	<td>Invite Sent</td>
	<td>Status</td>
</tr>
<?php foreach($invites as $k=>$user): ?>
	<tr>
		<td></td>
		<td><?php echo $user->full_name; ?></td>
		<td><a href="/user/view/<?php echo $user->uid; ?>"><?php echo $user->username; ?></a></td>
		<td><?php echo date('m.d.Y H:i:s',$user->date_added); ?></td>
		<td><?php 
			$style=($user->accepted=='Y') ? 'accepted' : 'pending'; 
			echo '<span class="'.$style.'">'.$style.'</span>';
		?></td>
	</tr>
<?php endforeach; ?>
</table>