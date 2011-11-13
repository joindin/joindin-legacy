<?php 
    header('Content-type: text/xml'); 
    $title = $this->config->item('site_name') . ((!empty($title)) ? ': '.$title : '');
?>
<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0">
    <channel>
        <title><?php echo $title; ?></title>
        <link><?php echo $this->config->site_url(); ?></link>
        <description><?php echo $this->config->item('site_name'); ?></description>
        <language>en-us</language>
        <pubDate><?php echo date('r')?></pubDate>
    </channel>
    <?php
    foreach ($items as $k=>$v) {
        echo sprintf('
            <item>
                <title>%s</title>
                <guid>%s</guid>
                <link>%s</link>
                <description>%s</description>
                <pubDate>%s</pubDate>
            </item>
        ', escape($v['title']), $v['guid'], $v['link'], escape($v['description']), $v['pubDate']);
    }
    ?>
</rss>
