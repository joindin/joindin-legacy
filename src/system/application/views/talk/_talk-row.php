<div class="row row-talk">
    <div class="img">
        <?php if ($talk->ccount > 0): ?>
            <?php echo rating_image($talk->tavg); ?>
        <?php else: ?>
            <div style="width: 117px">&nbsp;</div>
        <?php endif; ?>
    </div>
    <div class="text">
        <h3><a href="/talk/view/<?php echo escape($talk->ID); ?>"><?php echo escape($talk->talk_title); ?></a></h3>
        <p class="opts">
            at <a href="/event/view/<?php echo escape($talk->eid); ?>"><?php echo escape($talk->event_name); ?></a> 
            <?php if (isset($talk->event_start)) { echo '('.date('d.M.Y', $talk->date_given).')'; } ?>
            |
            <a href="/talk/view/<?php echo escape($talk->ID); ?>#comments"><?php echo $talk->ccount; ?> comment<?php echo $talk->ccount == 1 ? '' : 's'?></a>
        |
        <?php
        $speaker_list = array();
        if (isset($talk->speaker) && !empty($talk->speaker)) {
            foreach ($talk->speaker as $speaker) {
                if (isset($speaker->speaker_id)) {
                    // see if we have an override
                    if (isset($override) && array_key_exists($speaker->speaker_id, $override)) {
                        $speaker->speaker_name = $override[$speaker->speaker_id];
                    }
                    $speaker_list[]='<a href="/user/view/'.$speaker->speaker_id
                        .'">'.escape($speaker->speaker_name).'</a>';
                } else {
                    $speaker_list[]=escape($speaker->speaker_name);
                }
            }
        }
        echo implode(', ', $speaker_list);
        ?>
        </p>
    </div>
    <div class="clear"></div>
</div>
