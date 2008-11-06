<?php
//echo '<pre>'; print_r($details); print_r($comments); echo '</pre>';

echo '<h2>'.$details[0]->username.'</h2>';
echo '<b>Comments on:</b><br/>';

foreach($comments as $k=>$v){
	echo '<a href="/talk/view/'.$v->talk_id.'#'.$v->ID.'">'.$v->talk_title.'</a><br/>';
}
?>

