<html>
<head>
	<style>
		body {
			background-color: #FFFFFF;
			color: #999999;
			font-size: 11px;
		}
		a {
			color: #999999;
			text-decoration: none;
		}
		a:hover { text-decoration: underline; }
		a.talk_title { 
			font-size: 13px; 
			font-weight: bold;
		}
	</style>
	<script>
		function goTo(url){
			window.top.location.href=url;
		}
	</script>
</head>

<body>
	<a class="talk_title" href="#" onClick="goTo('http://joind.in/talk/view/<?php echo $talk->ID; ?>')"><?php echo $talk->talk_title; ?></a><br/>
	<?php foreach($talk->speaker as $speaker){ echo $speaker->speaker_name.' '; } ?> @ 
	<a href="#" onClick="goTo('http://joind.in/event/view/<?php echo $talk->event_id; ?>')"><?php echo $talk->event_name; ?></a>
	<a class="talk_title" href="#" onClick="goTo('http://joind.in/talk/view/<?php echo $talk->ID; ?>')">
		<img src="http://joind.in/inc/img/rating-<?php echo $talk->tavg; ?>.gif" border="0"/>
	</a>
</body>

</html>