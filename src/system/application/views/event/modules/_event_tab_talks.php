<div id="talks">
<?php if (count($by_day) == 0): ?>
    <?php $this->load->view('msg_info', array('msg' => 'No talks available at the moment.')); ?>
<?php else: 
    if (isset($track_filter)) {
        echo '<span style="font-size:13px">Sessions for track <b>'.$track_data->track_name.'</b></span>';
        echo ' <span style="font-size:11px"><a href="/event/view/'.$event_detail->ID.'">[show all sessions]</a></span>';
        echo '<br/><br/>';
    }
    ?>
    <table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
    <?php
    $total_comment_ct   = 0;
    $session_rate        = 0;
    foreach ($by_day as $talk_section_date=>$talk_section_talks): // was $k=>$v
        $ct = 0;
    ?>
        <tr>
            <th colspan="5">
                <h4 id="talks"><?php echo date('d.M.Y', $talk_section_date); ?></h4>
            </th>
        </tr>
        <?php foreach ($talk_section_talks as $ik=>$talk): 
//print_r($talk); echo '<br/><br/>';

        $session_rate+=$talk->rank;
        
        if (isset($track_filter)) {
            //Filter to the track ID
            if (empty($talk->tracks)) { 
                // If there's no track ID on the talk, don't show it
                continue; 
            } else {
                // There are tracks on the session, let's see if any match...
                $filter_pass=false;
                foreach ($talk->tracks as $talk_track) {
                    if ($talk_track->ID==$track_filter) { $filter_pass=true; }
                }
                if (!$filter_pass) { continue; }
            }
        }
    ?>
        <tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
            <td>
                <?php $type = !empty($talk->tcid) ? $talk->tcid : 'Talk'; ?>
                <span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>" title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
            </td>
            <td>
                <a href="/talk/view/<?php echo $talk->ID; ?>"><?php echo escape($talk->talk_title); ?></a>
                <?php
                    if ($talk->display_time != '00:00') {
                        echo "(";
                        echo $talk->display_time;
                        echo ")";
                    }
                ?>
                <div class="speakers">
                <?php
                $speaker_list = array();
                foreach ($talk->speaker as $speaker) {
                    if (isset($claimed[$talk->ID][$speaker->speaker_id])) {
                        $claim_data = $claimed[$talk->ID][$speaker->speaker_id];
                        $speaker_list[]='<a href="/user/view/'.$claim_data->speaker_id.'">'.
                            escape($claim_data->full_name).'</a>';
                    } else {
                        $speaker_list[]=escape($speaker->speaker_name);
                    }
                    
                }
                echo implode(', ', $speaker_list);
                ?>
                </div>
            </td>
            <td style="vertical-align:middle">
                <?php echo rating_image($talk->rank, "small"); ?>
            </td>
            <td>
                <?php if (! empty($talk->slides_link)) : ?>
                <a class="slides" target="new" href="<?php echo $talk->slides_link ?>">
                    <img style='vertical-align:text-top;' alt="Slides available" src="/inc/img/icon-slides.gif">
                </a>
                <?php endif; ?>
            </td>
            <td>
                <a class="comment-count" href="/talk/view/<?php echo $talk->ID; ?>/#comments"><?php echo $talk->comment_count; ?></a>
            </td>
        </tr>
    <?php
            $ct++;
        $total_comment_ct+=$talk->comment_count;
        endforeach;
    endforeach;
    ?>
    </table>
<?php endif; ?>
</div>
