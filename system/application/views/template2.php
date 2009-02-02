<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">

<head>
	<title>Joind.in </title>

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
<body id="<?= menu_get_current_area(); ?>">

<div id="hd">
    <div class="container_12 top">
    	<div class="grid_12">
    		<div class="usr">
    			<div class="wrapper">
        		<?php if (user_is_auth()): ?>
        			Logged in as <a href="/user/main"><?php echo htmlspecialchars(user_get_username()); ?></a> | <a href="/user/logout">Logout</a>
        		<?php else: ?>
        			<a href="/user/login">Login</a> or <a href="/user/register">Register</a>
        		<?php endif; ?>
    			</div>
    			<div class="clear"></div>
    		</div>
    	</div>
    	<div class="clear"></div>
    </div>

    <div class="container_12 nav">
    	<div class="grid_3 logo">
    		<a href="/"><img src="/inc/img/logo.gif" border="0" alt="joind.in Logo"/></a>
    	</div>
    	<div class="grid_6 menu">
    		<ul>
				<li id="menu-event"><a href="/event">Events</a>
				<li id="menu-talk"><a href="/talk">Talks</a>
				<li id="menu-search"><a href="/search">Search</a>
				<li id="menu-about" class="sep"><a href="/about">About</a>
				<li id="menu-blog"><a href="/blog">Blog</a>
    		</ul>
    		<div class="clear"></div>
    	</div>
    	<div class="grid_3 search">
    		<form id="top-search" method="get" action="/search">
    			<label id="top-search-label" accesskey="2" for="top-search-input">Search joind.in...</label>
    			<input type="text" value="" id="top-search-input" name="search_term"/>
    			<input type="image" alt="Search" src="/inc/img/top-search-submit.gif" id="top-search-submit"/>
    		</form>
    		<div class="clear"></div>
    	</div>
    	<div class="clear"></div>
    </div>
</div>

<?php if (menu_get_current_area() == 'home'): ?>

<div id="splash">
    <div class="container_12">
    	<div class="grid_12">
    		<a href="/user/register"><img src="/inc/img/splash.jpg" border="0" alt="Join joind.in now!"/></a>
    	</div>
    	<div class="clear"></div>
	</div>
</div>
<?php endif; ?>

<div id="ctn">
    <div class="container_12 container">
        <div class="grid_8">
			<div class="main">            
                <?=$content?>
            </div>
        </div>
    	<div class="grid_4">
        	<div class="sidebar">
        	<?php if (!user_is_auth()): ?>
            	<div class="box">
                	<h4>Sign in</h4>
                	<div>
    	<?php
    	echo form_open('/user/login');
    	echo '<table cellpadding="3" cellspcing="0" border="0">';
    	echo '<tr><td>User:</td><td>'.form_input('user').'</td></tr>';
    	echo '<tr><td>Pass:</td><td>'.form_password('pass').'</td></tr>';
    	echo '<tr><td align="right" colspan="2">'.form_submit('sub','login').'</td></tr>';
    	echo '</table>';
    	form_close();
    	?>
    	<small>Need an account? <a href="/user/register">Register now!</a></small>
            		</div>
            	</div>
            	<?php endif; ?>
            	<div class="box">
                	<h4>Submit your event</h4>
                	<p>
                		Know of an event happening? Let us know! We love to get the word out about events the community would be interested in and you can help us spread the word!
                	</p>
                	<p>
                		<a href="/event/submit" class="btn-big">Submit your event!</a>
                	</p>
            	</div>
            </div>
    	</div>
    	<div class="clear"></div>
	</div>
</div>

<div id="ftr">
    <div class="container_12">
    	<div class="grid_6">
        	<a href="/event">Events</a> | 
        	<a href="/talk">Talks</a> | 
        	<a href="/search">Search</a> | 
        	<a href="/about">About</a> | 
        	<a href="/blog">Blog</a> | 
        	<a href="/contact">Contact</a>
    	</div>
    	<div class="grid_6 rgt">
    		&copy; joind.in <?=date('Y')?>
    	</div>
    	<div class="clear"></div>
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