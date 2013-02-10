<?php header("Content-type: text/html;charset=utf8"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">

<head>
    <?php
    $title = menu_pagetitle();
    $title[] = $this->config->item('site_name');
    ?>
    <title><?php echo implode(' - ', $title); ?></title>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/site.css"/>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/mobile.css"/>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/jquery-ui/jquery-ui-1.7.3.custom.css"/>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/jquery-ui/theme/ui.all.css"/>

    <?php if ($css) { ?>
    <link media="all" rel="stylesheet" type="text/css" href="<?php echo $css; ?>"/>
    <?php } ?>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <script type="text/javascript" src="/inc/js/jquery.js"></script>
    <script type="text/javascript" src="/inc/js/jquery.pause.js"></script>
    <script type="text/javascript" src="/inc/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/inc/js/site.js"></script>
    <script type="text/javascript" src="/inc/js/notifications.js" async></script>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <?php
    if (!empty($feedurl)) {
        echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$feedurl.'" />';
    }
    if (isset($reqkey)) { echo "\n\t" . '<script type="text/javascript">var reqk="'.$reqkey.'";</script>'; }
    if (isset($seckey)) { echo "\n\t" . '<script type="text/javascript">var seck="'.$seckey.'";</script>'; }
    ?>
</head>
<body id="page-<?php echo menu_get_current_area(); ?>">

<div id="hd">
    <div class="container_12 nav">
        <div class="container_12 top">
            <div class="grid_12">
                <div class="usr">
                    <div class="wrapper">
                        <?php if (user_is_auth()): ?>
                        Logged in as <strong><a href="/user/view/<?php echo user_get_id(); ?>"><?php echo escape(user_get_username()); ?></a></strong> |
                        <a href="/user/main">Account</a> |
                        <a href="/user/logout">Logout</a>
                        <?php else: ?>
                        <a href="/user/login">Login</a> or <a href="/user/register">Register</a>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="grid_12 logo">
            <a href="/"><img src="/inc/img/logo.png" border="0" alt="<?php echo $this->config->item('site_name'); ?> Logo" height="60" width="200" /></a>
            <div class="clear"></div>
        </div>
        <div class="grid_12 menu">
            <ul>
                <li id="menu-event"><a href="/event">Events</a>
                <li id="menu-talk"><a href="/talk">Talks</a>
                <li id="menu-about" class="sep"><a href="/about">About</a>
                <li id="menu-help"><a href="/help">Help</a>
                <li id="menu-blog"><a href="/blog">Blog</a>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div id="ctn">
    <div class="container_12 container">
        <div class="grid_12 search">
            <form id="top-search" method="post" action="/search">
                <label id="top-search-label" accesskey="2" for="top-search-input">Search <?php echo $this->config->item('site_name'); ?>...</label>
                <input type="text" value="" id="top-search-input" name="search_term"/>
                <input type="image" alt="Search" src="/inc/img/top-search-submit.png" id="top-search-submit"/>
            </form>
            <div class="clear"></div>
        </div>
        <div class="grid_12">
            <div class="main">
                <?php if (isset($info_block)) { echo $info_block; } ?>
                <?php echo $content?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div id="ftr">
    <div class="container_12">
        <div class="grid_12">
            <a href="/event">Events</a> |
            <a href="/talk">Talks</a> |
            <a href="/search">Search</a> |
            <a href="/about">About</a> |
            <a href="/help">Help</a> |
            <a href="/blog">Blog</a> |
            <a href="/api">API</a> |
            <a href="/about/contact">Contact</a>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div id="jQueryUImessageBox" style="display:none">
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
