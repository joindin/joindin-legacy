<?php

function buildCode($tid,$eid,$title,$speaker_name){
	$speaker_name=trim($speaker_name);
	$str='ec'.str_pad(substr($tid,0,2),2,0,STR_PAD_LEFT).str_pad($eid,2,0,STR_PAD_LEFT);
	$str.=substr(md5($title.$speaker_name),5,5);
	return $str;
}