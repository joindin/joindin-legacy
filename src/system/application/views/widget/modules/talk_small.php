<html>
<head>
	<style>
		body {
			background-color: #FFFFFF;
			color: #999999;
			font-size: 11px;
			margin: 2 2 2 2;
		}
		a {
			color: #999999;
			text-decoration: none;
		}
		a:hover { text-decoration: underline; }
		a.talk_title { 
			font-size: 11px; 
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

<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td valign="top">
		<a class="talk_title" href="#" onClick="goTo('http://<?php echo $site ?>/talk/view/<?php echo $talk->ID; ?>')"><?php echo $talk->talk_title; ?></a> <a style="font-size:10px" href="#" onClick="goTo('http://<?php echo $site ?>/event/view/<?php echo $talk->event_id; ?>')">@ <?php echo $talk->event_name; ?></a>
	</td>
	<td valign="top">
		<a class="talk_title" href="#" onClick="goTo('http://<?php echo $site ?>/talk/view/<?php echo $talk->ID; ?>')">
			<img src="http://<?php echo $site ?>/inc/img/rating-<?php echo $talk->tavg; ?>.gif" border="0"/>
		</a>
	</td>
</tr>
</table>

</body>
</html>