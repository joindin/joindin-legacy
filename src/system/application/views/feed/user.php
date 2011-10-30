<?php header('Content-type: text/xml'); ?>
<?php echo "<?xml version=\"1.0\"?>"; ?>
<user>
    <detail>
        <username><?php echo $username?></username>
    </detail>
    <talks>
        <?php
        foreach ($talks as $k=>$v) {
        echo sprintf('
            <talk>
                <title>%s</title>
                <desc>%s</desc>
                <speaker>%s</speaker>
                <date>%s</date>
                <talk_id>%s</talk_id>
            </talk>
        ', $v['title'], $v['desc'], $v['speaker'], $v['date'], $v['tid']);
        }
        ?>
    </talks>
    <comments>
        <?php
        foreach ($comments as $k=>$v) {
        //talk/event
        echo sprintf('
            <content>%s</content>
            <date>%s</date>
            <type>%s</type>
            <event_id>%s</event_id>
        ', $v['content'], $v['date'], $v['type'], $v['event_id']);
        }
        ?>
    </comments>
</user>
