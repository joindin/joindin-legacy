<h1><?php echo $details[0]->username; ?></h1>

<div class="box">
    <h2>Talks</h2>
    <?php
    foreach($talks as $k=>$v){
    	$this->load->view('talk/_talk-row', array('talk'=>$v));
    }
    ?>
</div>

<div class="box">
    <h2>Comments</h2>
    <?php foreach($comments as $k=>$v): ?>
    <div class="row">
    	<strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo htmlspecialchars($v->talk_title); ?></a></strong>
    	<div class="clear"></div>
    </div>
    <?php endforeach; ?>
</div>
