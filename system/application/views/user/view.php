<h1><?php echo $details[0]->username; ?></h1>

<div class="box">
    <h2>Talks</h2>
<?php if (count($talks) == 0): ?>
	<p>No talks so far</p>
<?php else: ?>
    <?php
        foreach($talks as $k=>$v){
        	$this->load->view('talk/_talk-row', array('talk'=>$v));
        }
    ?>
<?php endif; ?>
</div>

<div class="box">
    <h2>Comments</h2>
<?php if (count($comments) == 0): ?>
	<p>No comments so far</p>
<?php else: ?>
    <?php foreach($comments as $k=>$v): ?>
    <div class="row">
    	<strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo htmlspecialchars($v->talk_title); ?></a></strong>
    	<div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
