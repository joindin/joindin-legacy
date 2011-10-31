<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">

<head>
<?php 
$title = menu_pagetitle();
$title[] = $this->config->item('site_name');
?>
    <title><?php echo implode(' - ', $title); ?></title>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/brand/brand1/site.css"/>
    <link media="all" rel="stylesheet" type="text/css" href="/inc/css/jquery-ui/theme/ui.all.css"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
    <script type="text/javascript" src="/inc/js/jquery.js"></script>
    <script type="text/javascript" src="/inc/js/jquery.pause.js"></script>
    <script type="text/javascript" src="/inc/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/inc/js/site.js"></script>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>
<div id="ctn">
    <div class="container_12 container">
        <div class="grid_8">
            <div class="main">
                <?php if (isset($info_block)) { echo $info_block; } ?>
                <?php echo $content?>
            </div>
        </div>
    </div>
</div>

</body>
    
</html>
