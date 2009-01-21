<?php
//echo '<pre>'; print_r($details); print_r($comments); echo '</pre>';
//echo '<pre>'; print_r($talks); echo '</pre>';

echo '<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09">'.$details[0]->username.'</h1><br/>';
echo '<h3 style="color:#5181C1">Talks</h3>';
foreach($talks as $k=>$v){
	echo '<a href="/talk/view/'.$v->tid.'">'.$v->talk_title.'</a><br/>';
	echo $v->talk_desc.'<br/><br/>';
}


echo '<h3 style="color:#5181C1">Comments</h3>';
foreach($comments as $k=>$v){
	echo '<a href="/talk/view/'.$v->talk_id.'#'.$v->ID.'">'.$v->talk_title.'</a><br/>';
}
?>

