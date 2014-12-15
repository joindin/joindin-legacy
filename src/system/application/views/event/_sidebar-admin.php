<?php
// Sidebar admin
?>
<div class="box">
    <h4>Event Admin</h4>
    <div class="ctn">
        <ul>
        <li><a href="/event/edit/<?php echo $eid; ?>">Edit Event</a>
        <li><a href="/talk/add/event/<?php echo $eid; ?>">Add a New Talk</a>
        <li><a href="/event/claim/<?php echo $eid; ?>">Pending Claims</a> (<?php echo $claim_count; ?>)
        <li><a href="/event/tracks/<?php echo $eid; ?>">Event Tracks</a>
        <li><a href="/event/import/<?php echo $eid; ?>">Import Event Info</a>
        <?php
        if ($is_private=='Y') { echo '<br/><br/><li><a href="/event/invite/'.$eid.'">Invite list</a>'; }
        ?>
        <li style="padding-top:10px"><a href="/event/delete/<?php echo $eid; ?>" style="color:#D3000E;font-weight:bold">Delete event</a>
        </ul>
    </div>
    <h4>Hosts</h4>
    <div class="ctn">
        <ul id="evt_admin_list">
        <?php foreach ($evt_admin as $k=>$user) {
            echo '<li id="evt_admin_'.$user->ID.'"><a href="/user/view/'.$user->ID.'">'.$user->full_name.'</a> ';
            echo '[<a href="#" onclick="removeEventAdmin('.$eid.',\''.$user->username.'\','.$user->ID.')">X</a>]<br/>';
        }
        if (count($evt_admin)==0) { echo 'No event admins'; }
        ?>
        </ul>
        <b>Display Name:</b>
        <input type="text" name="add_admin_user" id="add_admin_user" />
        <input type="button" name="add_admin_btn" id="add_admin_btn" value="add" onClick="addEventAdmin(<?php echo $eid; ?>)" />
        <br/><br/>
    </div>
    <h4>Helpful Links</h4>
    <div class="ctn">
        <ul>
        <li><a href="/about/evt_admin">Event Admin Cheat Sheet</a>
        </ul>
    </div>
</div>
