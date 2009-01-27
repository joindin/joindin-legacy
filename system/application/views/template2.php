<html>
	<title>Joind.in </title>
<head>
	<link media="all" rel="stylesheet" type="text/css" href="/inc/css/site.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
	<script type="text/javascript" src="/inc/js/jquery.js"></script>
	<script type="text/javascript" src="/inc/js/site.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<?php
	if(isset($feedurl)){
		echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$feedurl.'" />';
	}
	if(isset($reqkey)){ echo "\n\t<script>var reqk='".$reqkey."';</script>"; }
	if(isset($seckey)){ echo "\n\t<script>var seck='".$seckey."';</script>"; }
	?>
</head>
<body id="events2">

<div id="hd" class="clearfix">
    <div class="container_12 usr">
    	<div class="grid_12" class="clearfix">
    		<div class="bar"><?=$logged?></div>
    	</div>
    </div>
    <div class="container_12 nav">
    	<div class="grid_3 logo">
    		<a href="/"><img src="/inc/img/logo.gif" border="0" alt="joind.in Logo"/></a>
    	</div>
    	<div class="grid_7 menu">
    		<ul>
				<li id="menu-events"><a href="/event">Events</a>
				<li id="menu-talks"><a href="/talk">Talks</a>
				<li id="menu-search"><a href="/search">Search</a>
				<li id="menu-about" class="sep"><a href="/about">About</a>
				<li id="menu-blog"><a href="/blog">Blog</a>
    		</ul>
    	</div>
    	<div class="grid_2 search">
    	</div>
    </div>
</div>

<div id="ctn" class="clearfix">
    <div class="container_12 usr">
    	<div class="grid_8"><?=$content?></div>
		<div class="grid_4">&nbsp;</div>
	</div>
</div>

<div id="ftr" class="clearfix">
    <div class="container_12 usr">
    	<div class="grid_12">&copy; joind.in <?=date('Y')?></div>
	</div>
</div>

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