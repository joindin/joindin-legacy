<?php /*header('Content-type: text/xml');*/ ?>
<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0">
	<channel>
		<title>Joind.in</title>
		<link>http://joind.in</link>
		<description>Joind.in</description>
		<language>en-us</language>
		<pubDate><?=date('r')?></pubDate>
	</channel>
	<?php
	foreach($items as $k=>$v){
		echo sprintf('
			<item>
				<title>%s</title>
				<guid>%s</guid>
				<link>%s</link>
				<description>%s</description>
				<pubDate>%s</pubDate>
			</item>
		',$v['title'],$v['guid'],$v['link'],htmlentities($v['description']),$v['pubDate']);
	}
	?>
</rss>