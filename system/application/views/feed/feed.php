<?php 
	header('Content-type: text/xml'); 
	$title = 'Joind.in' . ((!empty($title)) ? ": {$title}" : '');
?>
<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0">
	<channel>
		<title><![CDATA[<?= $title; ?>]]></title>
		<link>http://joind.in</link>
		<description>Joind.in</description>
		<language>en-us</language>
		<pubDate><?= date('r') ?></pubDate>
	</channel>
	<?php foreach($items as $item) : ?>
	<item>
	    <title><![CDATA[<?= $item['title'] ?>]]></title>
	    <guid><?= $item['guid'] ?></guid>
	    <link><?= $item['link'] ?></link>
	    <description>
	    <![CDATA[<?= escape($item['description']) ?>]]>
	    </description>
	    <pubDate><?= $item['pubDate'] ?></pubDate>
	</item>
	<?php endforeach; ?>
</rss>
