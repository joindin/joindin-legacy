<?php echo form_open('event/claims'.(isset($id) ? "/$id" : "")); ?>
<div class="box">
    <div class="row">
    <table cellpadding="0" cellspacing="0" border="0" id="claims_table" width="100%">
    <tr>
        <td class="app_den" align="center">APPROVE</td>
        <td class="app_den" align="center">DENY</td>
        <td><b>Event Name</b></td>
        <td><b>Claiming User</b></td>
    </tr>
    <?php
        foreach ($claims as $k=>$claim): ?>
        <tr>
            <td align="center"><?php echo form_radio('claim['.$claim->ua_id.']','approve'); ?></td>
            <td align="center"><?php echo form_radio('claim['.$claim->ua_id.']','deny'); ?></td>
            <td>
                <?php echo '<a href="/event/view/'.$claim->eid.'">'.$claim->event_name.'</a>'; ?><br/>
            </td>
            <td><?php echo '<a href="/user/view/'.$claim->uid.'">'.$claim->claiming_name.'</a>'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <div class="row" align="right">
        <?php echo form_submit('sub','Submit Updates'); ?>
    </div>
</div>
<?php echo form_close(); ?>
