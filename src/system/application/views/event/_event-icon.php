<div class="img">
    <div class="frame">
    <?php 
        $path=$_SERVER['DOCUMENT_ROOT'].'/inc/img/event_icons/';
        $img=(!empty($event->event_icon) && is_file($path.$event->event_icon)) ? escape($event->event_icon) : 'none.gif'; 
        ?>
        <?php if (!empty($showlink)): ?><a href="/event/view/<?php echo $event->ID; ?>"><?php endif; ?><img src="/inc/img/event_icons/<?php echo $img ?>" alt="<?php echo escape($event->event_name); ?>" height="90" width="90" /><?php if (!empty($showlink)): ?></a><?php endif; ?>
    </div>
</div>
