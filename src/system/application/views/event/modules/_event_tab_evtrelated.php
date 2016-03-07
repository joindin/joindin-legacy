<div id="evt_related">
    <?php $ct=0; ?>
    <table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
    <?php foreach ($evt_sessions as $ik=>$iv): ?>
        <tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        <td>
        <?php $type = !empty($iv->tcid) ? $iv->tcid : 'Talk'; $type='Social Event'; ?>
        <span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>"
          title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
        </td>
        <td>
        <a href="/talk/view/<?php echo $iv->ID; ?>"><?php echo escape($iv->talk_title); ?></a>
        </td>
        <td>
            <?php
            if (isset($iv->speaker) && is_array($iv->speaker)) {
                $speaker_list = array();
                foreach ($iv->speaker as $speaker) {
                    if (isset($claimed[$iv->ID][$speaker->speaker_id])) {
                        $claim_data = $claimed[$iv->ID][$speaker->speaker_id];
                        $speaker_list[]='<a href="/user/view/'.$claim_data->speaker_id.'">'.
                            escape($claim_data->full_name).'</a>';
                    } else {
                        $speaker_list[]=escape($speaker->speaker_name);
                    }
                    
                }
                echo implode(', ', $speaker_list);
            }
            ?>
        </td>
        <td>
        <a class="comment-count" href="/talk/view/<?php echo $iv->ID; ?>/#comments"><?php echo $iv->comment_count; ?></a>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
