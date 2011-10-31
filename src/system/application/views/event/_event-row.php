<?php
$this->load->helper('text');
$this->load->library('timezone');
?>
<div class="row row-event">
    <?php $this->load->view('event/_event-icon', array('event'=>$event, 'showlink' => true)); ?>
    <div class="text">
        <h3><a href="/event/view/<?php echo $event->ID; ?>"><?php echo escape($event->event_name); ?></a></h3>
        <p class="info"><strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($event->event_start, $event->event_tz_cont.'/'.$event->event_tz_place, 'd.M.Y'); ?></strong>
        <?php if ($event->event_start+86399 != $event->event_end) { ?>
        - <strong><?php echo $this->timezone->formattedEventDatetimeFromUnixtime($event->event_end, $event->event_tz_cont.'/'.$event->event_tz_place, 'd.M.Y'); ?></strong> at <strong><?php echo escape($event->event_loc); ?></strong>
        <?php } ?>
        </p>
        <div class="desc">
        <?php echo auto_p(escape(word_limiter($event->event_desc, 20))); ?>
        <?php
        if (isset($event->eventTags) && !empty($event->eventTags)) {
            $tags = array();
            foreach ($event->eventTags as $tag) {
                $tag = escape($tag->tag_value);
                $tags[] = '<a href="/event/tag/'.$tag.'">'.$tag.'</a>';
            }
            echo '<b>tagged</b> '.implode(', ', $tags);
        }
        ?>
        </div>
        <p class="opts">
            <a href="/event/view/<?php echo $event->ID; ?>#comments"><?php echo $event->num_comments; ?> comment<?php echo $event->num_comments == 1 ? '' : 's'?></a> |
            <strong><span class="event-attend-count-<?php echo $event->ID; ?>"><?php echo $event->num_attend; ?></span> <?php echo ($event->event_end<time()) ? 'attended' : 'attending' ?></strong> | 

            <!--<input type="checkbox" name="attend" value="1"/> Attending?-->

    <?php 
        if ($event->event_end<time()) {
            $link_txt="I attended";
        } else { $link_txt="I'm attending"; }
    ?>
            <?php if ($event->private!='Y') { ?><a class="btn-small<?php echo $event->user_attending ? ' btn-success' : ''; ?>" href="#" onclick="markAttending(this,<?php echo $event->ID?>,<?php echo $event->event_end<time() ? 'true' : 'false'; ?>);return false;"><?php echo $link_txt?></a> <?php } ?>

        </p>
        <div class="desc" style="padding-top:6px">
        <?php if (isset($event->is_cfp) && $event->is_cfp): ?>
            Call for papers ends <b><?php echo date('d.M.Y', $event->event_cfp_end); ?></b>
            <?php if (time() <= $event->event_cfp_end && $event->event_cfp_end <= strtotime('+1 week')): ?>
                &nbsp;&nbsp;&nbsp;<span class="ends_soon">ending soon!</span>
            <?php endif; ?>
        <?php endif ?>
        </div>
        <?php if (isset($view_type) && $view_type=='pending'): ?>
        <a style="color:#00C934;text-decoration:none;font-weight:bold;font-size:11px" href="/event/approve/<?php echo $event->ID ?>">APPROVE</a> -
        <a style="color:#D6000E;text-decoration:none;font-weight:bold;font-size:11px" href="/event/delete/<?php echo $event->ID ?>">DENY</a>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>
