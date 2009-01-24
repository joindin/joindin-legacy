<html>
	<title>Joind.in </title>
<head>
	<link rel="stylesheet" type="text/css" href="/inc/css/site.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
	<script language="JavaScript" src="/inc/js/jquery.js"></script>
	<script language="JavaScript" src="/inc/js/site.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php
	if(isset($feedurl)){
		echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$feedurl.'" />';
	}
	if(isset($reqkey)){ echo "\n\t<script>var reqk='".$reqkey."';</script>"; }
	if(isset($seckey)){ echo "\n\t<script>var seck='".$seckey."';</script>"; }
	?>
</head>
<body>

<center>
<table cellpadding="4" cellspacing="0" border="0" width="700" height="100%" id="layout">
<tr>
	<td rowspan="6" id="bg_left">&nbsp</td>
	<td style="padding-bottom:0px;height:80px"><a href="/"><img src="/inc/img/logo.gif" border="0"/></a></td>
	<td valign="bottom" align="right" id="search_cell">
		<?php 
		echo form_open('/search'); echo form_input('search_term'); 
		echo form_submit('sub','Search'); echo form_close();
		?>
	</td>
	<td rowspan="6" id="bg_right">&nbsp;</td>
</tr>
<tr><td colspan="2" style="background-color:#4282C4;height:2px;font-size:0px;padding:4px">&nbsp;</td></tr>
<tr>
	<td id="nav_cell">
		<ul>
		<li><a href="/event">events</a>
		<li><a href="/talk">talks</a>
		<li><a href="/search">search</a>
		<li><a href="/about">about</a>
		<li><a href="/blog">blog</a>
		<li>&nbsp;
		</ul>
	</td>
	<td id="nav_cell"><ul><li><?=$logged?></ul></td>
</tr>
<tr>
	<td colspan="2" align="center" style="background-color:#EEEEEE;padding:1px;height:20px">
		<a href="/event/submit" style="color:#B86F09;text-decoration:none;font-weight:bold">Submit your event today!</a>
	</td>
</tr>
<tr>
	<td colspan="2" valign="top" style="padding:8px;border:0px solid #C4C9A7"><?=$content?></td>
</tr>
<tr>
	<td colspan="2" id="footer">
	&copy; joind.in <?=date('Y')?>
	</td>
</tr>
</table>
</center>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-246789-3");
pageTracker._trackPageview();
</script>

</body>
</html>