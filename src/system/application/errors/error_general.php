<?php
/**
 * Standard codeigniter error view for general error conditions
 *
 * @category  Error
 * @package   View
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
?>
<html>
<head>
<title>Error</title>
<style type="text/css">

body {
background-color:	#fff;
margin:				40px;
font-family:		Lucida Grande, Verdana, Sans-serif;
font-size:			12px;
color:				#000;
}

#content  {
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

h1 {
font-weight:		normal;
font-size:			14px;
color:				#990000;
margin: 			0 0 4px 0;
}
</style>
</head>
<body>
    <div id="content">
        <h1><?php echo $heading; ?></h1>
        <?php echo $message; ?>
    </div>
</body>
</html>
