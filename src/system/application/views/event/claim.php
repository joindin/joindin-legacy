<?php menu_pagetitle('Pending Claims for '.$event_detail[0]->event_name); ?>

<?php echo '<h2>Pending Claims</h2>'; ?>

<style>
#claims_table { width: 100%; }
#claims_table td { padding: 3px; }
#claims_table td.app_den { width: 30px; font-size: 9px; }
</style>

<p>
Below you'll find a list of claims visitors to the site have made on the sessions for this event. 
The "Speaker" field shows the speaker name(s) and the "Claiming User" is the <?php echo $this->config->item('site_name'); ?> user trying to
claim the session. You can then accept/deny based on any match between them.
</p>

<?php if (!empty($msg)) {
    $this->load->view('msg_info', array('msg' => $msg));
} ?>

<?php echo form_open('event/claim/'.$eventId); ?>
<div class="box">
    <a href="/event/view/<?php echo $eventId; ?>">Back to event</a>
    <div class="row">
    <table cellpadding="0" cellspacing="0" border="0" id="claims_table">
    <tr>
        <td class="app_den" align="center">APPROVE</td>
        <td class="app_den" align="center">DENY</td>
        <td><b>Session Name</b></td>
        <td><b>Speaker</b></td>
        <td><b>Claiming User</b></td>
    </tr>
    <?php foreach ($newClaims as $k=>$claim): ?>
        <tr>
            <td align="center"><?php echo form_radio(array(
                'name' 	=> 'claim['.$claim->pending_claim_id.']',
                'value' => 'approve',
                'id'	=> 'talkid-'.$claim->talk_id.'-'.$claim->ID.'-approve'
            )); ?></td>
            <td align="center"><?php echo form_radio(array(
                'name' 	=> 'claim['.$claim->pending_claim_id.']',
                'value' => 'deny',
                'id'	=> 'talkid-'.$claim->talk_id.'-'.$claim->ID.'-deny'
            )); ?></td>
            <td>
                <a href="/talk/view/<?php echo $claim->talk_id; ?>"><?php echo
                    escape($claim->talk_title); ?></a>
            </td>
            <td>
                <?php
                $speakers = array();
                foreach ($claim->claim_detail as $detail) {
                    $speakers[] = escape($detail->speaker_name);
                }
                echo implode(', ', $speakers);
                ?>
            </td>
            <td><?php echo '<a href="/user/view/'.$claim->speaker_id.'">'
                    .escape($claim->full_name).'</a>'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <div class="row">
        <?php echo form_submit('sub','Submit Updates'); ?>
    </div>
</div>
<?php echo form_close(); 
