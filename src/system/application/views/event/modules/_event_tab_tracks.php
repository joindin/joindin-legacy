<div id="tracks">
    <?php
    foreach ($tracks as $k=>$tr) {
        echo '<div style="padding:3px">';
        if ($tr->used>0) {
            echo '<a style="font-size:13px;font-weight:bold" href="/event/view/'.$event_detail->ID.'/track/'.$tr->ID.'">'.$tr->track_name.'</a><br/>';
        } else { echo '<span style="font-size:13px;font-weight:bold;">'.$tr->track_name.'</span><br/>'; }
        echo $tr->track_desc.'<br/>';
        echo $tr->used.' sessions';
        echo '</div>';
    }
    ?>
</div>
