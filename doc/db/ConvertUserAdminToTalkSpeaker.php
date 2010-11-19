<?php

/**
 * This script will take the current claims in the user_admin table
 * and apply their relationship to the talk_speaker table (setting speaker_id)
 */
define('IS_CRON', true);
include('../index.php');

$ci = &get_instance();

$ret = $ci->db->get_where('talk_speaker',array('speaker_id'=>1));
$ret = $ret->result();
var_dump($ret);

?>