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
<div class="box" style="margin-bottom:18px">
    <div class="row">
        <label for="event_name">Invite Username:</label>
        <?php echo form_input('user'); ?>
        <?php echo form_submit('sub','Send Invite'); ?>
    </div>
</div>
<?php echo form_close(); ?>

<?php
// Display the list of private attendees
//var_dump($invites);
echo form_open('event/invite/'.$eid);
echo form_hidden('attend_list', count($invites));
?>
<table cellpadding="0" cellspacing="0" border="0" id="invite_list">
<tr class="header">
    <td></td>
    <td>Full Name</td>
    <td>Username</td>
    <td>Invite Sent</td>
    <td>Status</td>
</tr>
<?php foreach ($invites as $k=>$user): ?>
    <tr>
        <td><?php 
            if ($user->accepted=='A') {
                echo form_submit('approve_'.$user->uid,'approve');
                echo form_submit('decline_'.$user->uid,'decline');
            } else { echo form_submit('del_'.$user->uid,'delete'); }
        ?></td>
        <td><?php echo $user->full_name; ?></td>
        <td><a href="/user/view/<?php echo $user->uid; ?>"><?php echo $user->username; ?></a></td>
        <td><?php echo date('m.d.Y H:i:s', $user->date_added); ?></td>
        <td><?php 
            switch(strtolower($user->accepted)) {
                case 'y': 
                    $style='accepted'; 
                    break;
                case 'a': 
                    $style='pending approval'; 
                    break;
                default: 
                    $style='pending';
            }
            echo '<span class="'.$style.'">'.$style.'</span>';
        ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php echo form_close();
