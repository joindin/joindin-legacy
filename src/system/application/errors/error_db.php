<?php
/**
 * Standard codeigniter error view for errors regarding the database
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
<title>Database Error</title>
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
<?php
if (isset($_SERVER['JOINDIN_DEBUG']) && $_SERVER['JOINDIN_DEBUG'] == 'on') {
    echo "<h1>$heading</h1>\n$message\n";
    
    echo '<table>';
    $cnt = 0;
    foreach (debug_backtrace() as $bt) {
        if (!empty($bt['function'])) {
            $fn = $bt['function'];
        }
        if (!empty($bt['class'])) {
            $fn = $bt['class'] . ($bt['type']) . $fn;
        }

        $file = (empty($bt['file']) ? '' : $bt['file']);
        $file = str_replace($_SERVER['DOCUMENT_ROOT'], '...', $file);
        $line = (empty($bt['line']) ? '' : $bt['line']);
        
        echo "<tr><td>#$cnt</td><td>$fn</td><td>$file</td><td>$line</td></tr>";
        $cnt++;
    }
    echo '</table>';
    
} else {
    echo "<h1>Ooops!</h1>\n
    We're very sorry, something went wrong!  Please try again or drop us a
    line at <a href=\"mailto:info@joind.in\">info@joind.in</a>, letting
    us know what you were trying to do - we'll do our best to help!";
}
?>
   </div>
</body>
</html>
