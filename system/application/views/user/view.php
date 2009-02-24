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

<?php
//sort the events
$ev=array('attended'=>array(),'attending'=>array());
foreach($is_attending as $k=>$v){
	if($v->event_end<time()){
		$ev['attended'][]=$v; 
	}else{ $ev['attending'][]=$v; }
}
//minimize my attending
$my=array();
foreach($my_attend as $k=>$v){ $my[]=$v->ID; }
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<div class="box">
			<h2>Attending Events</h2>
		<?php if (count($ev['attending']) == 0): ?>
			<p>No events so far</p>
		<?php else: ?>
		    <?php foreach($ev['attending'] as $k=>$v): ?>
		    <div class="row">
		    	<strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
				<?php echo date('M d, Y',$v->event_start); ?>
				<?php if(in_array($v->ID,$my)){ echo "<br/><span style=\"color:#92C53E;font-size:11px\">you'll be there!</span>"; } ?>
		    	<div class="clear"></div>
		    </div>
		    <?php endforeach; ?>
		<?php endif; ?>
		</div>
	</td>
	<td>
		<div class="box">
			<h2>Attended Events</h2>
		<?php if (count($ev['attended']) == 0): ?>
			<p>No events so far</p>
		<?php else: ?>
		    <?php foreach($ev['attended'] as $k=>$v): ?>
		    <div class="row">
		    	<strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
				<?php echo date('M d, Y',$v->event_start); ?>
				<?php if(in_array($v->ID,$my)){ echo "<br/><span style=\"color:#92C53E\">you were there!</span>"; } ?>
		    	<div class="clear"></div>
		    </div>
		    <?php endforeach; ?>
		<?php endif; ?>
		</div>
	</td>
</tr>
</table>