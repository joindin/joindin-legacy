<div id="claim_select_div" style="text-align:center;">
    Who are you?<br/>
    <form action="<?php echo '/talk/claim/'.$detail->tid ?>" method="POST">
    <?php
    $speaker_list = array();
    foreach ($speakers as $speaker) {
        if (empty($speaker->speaker_id)) {
            $speaker_list[$speaker->ID]=escape($speaker->speaker_name);
        }
    }
    echo form_dropdown('claim_name_select', $speaker_list, null,'id="claim_name_select"');
    ?>
    <input type="submit" value="claim" id="claim-btn-submit"/>
    <input type="button" value="cancel" id="claim-cancel-btn"/>
    </form>
</div>
<p class="admin">
<?php if ($admin):?>
    <a class="btn-small" href="/talk/delete/<?php echo $detail->tid; ?>">Delete talk</a>	
    <a class="btn-small" href="/talk/edit/<?php echo $detail->tid; ?>">Edit talk</a>
<?php endif; ?>
<?php
    if (!isset($user_id)) {
        $link 	= '/user/login';
        $class 	= '';
    } elseif (count($speakers)==1) {
        $link 	= '/talk/claim/'.$detail->tid.'/'.$speaker->ID;
        $class 	= 'single';
    } else {
        // multiple speakers, still show the dropdown
        $link 	= '';
        $class 	= 'multi';
    }
    ?>
    <a class="btn-small <?php echo $is_claimed ? 'disabled' : '' ?>" href="<?php echo !$is_claimed ? $link : 'javascript:;' ?>" id="claim_btn" name="<?php echo $class; ?>">Claim talk</a>
</p>

<div id="claim-dialog">
    <p>Talk claims have been moved to the new <a href="https://joind.in">Joind.in site</a>.</p>
    <p>Please login to the new site to claim your talk</p>    
</div>
<script type="text/javascript">
$('#claim_select_div').css('display','none');
$('#claim-dialog').css('display','none');
</script>
