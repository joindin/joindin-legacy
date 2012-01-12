<?php
define('IS_CRON', true);
$_GET['external/twitter_process_feedback'] = null;
$_SERVER['REQUEST_URI'] = '/external/twitter_process_feedback';

include('../index.php');
?>
