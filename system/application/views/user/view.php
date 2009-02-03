<h1><?php echo $details[0]->username; ?></h1>

<h2>Talks</h2>
<?php
foreach($talks as $k=>$v){
	$this->load->view('talk/_talk-row', array('talk'=>$v));
}
?>
<h2>Comments</h2>
<?php
foreach($comments as $k=>$v){
	echo '<a href="/talk/view/'.$v->talk_id.'#comment-'.$v->ID.'">'.$v->talk_title.'</a><br/>';
}
?>

