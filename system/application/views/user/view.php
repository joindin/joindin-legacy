<h1><?php 
	echo (!empty($details[0]->full_name)) ? $details[0]->full_name.' ('.$details[0]->username.')': $details[0]->username;
?></h1>

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
    	<strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo escape($v->talk_title); ?></a></strong>
    	<div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<div class="box">
	<h2>Attending Events</h2>
<?php if (count($is_attending) == 0): ?>
	<p>No events so far'</p>
<?php else: ?>
    <?php foreach($is_attending as $k=>$v): ?>
    <div class="row">
    	<strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
		<?php echo date('m.d.Y',$v->event_start); ?>
    	<div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>