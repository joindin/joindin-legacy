<?php
//$posts	= array();
$eid	= $evt_detail[0]->ID;
$sub	= ($action=='add') ? 'Add Post' : 'Edit';
switch($action){
	case 'add': 	$fact='add/'.$eid; 
		break;
	case 'edit': 	$fact='edit/'.$eid.'/'.$pid; 
		break;
	default:
		$fact='';
}

menu_pagetitle('Blog : ' .escape($evt_detail[0]->event_name));
?>
<h2>Blog : <?php echo $evt_detail[0]->event_name; ?></h2>
<a class="btn-small" href="/event/view/<?php echo $eid; ?>">Back to event</a>
<?php if($action!='view'): ?>
<a class="btn-small" href="/event/blog/view/<?php echo $eid; ?>">Back to blog</a>
<?php endif; ?>

<?php if($action=='add' || $action=='edit'): 
if (!empty($msg)){ $this->load->view('msg_info', array('msg' => $msg)); }
	
echo form_open('event/blog/'.$fact);
?>
<!-- Blog add/edit form -->
<div class="box">
    <div class="row">
    	<label for="event_name">Title:</label>
	<?php echo form_input('title',$this->validation->title); ?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_name">Content:</label>
	<?php echo form_textarea('content',$this->validation->content); ?>
    </div>
    <div class="clear"></div>
	<div class="row row-buttons">
    	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), ucwords($action).' Post'); ?>
    </div>
</div>
<?php 
echo form_close();

else: ?>
<?php if(user_is_admin() || user_is_admin_event($eid)): ?>
<a href="/event/blog/add/<?php echo $eid; ?>" class="btn-small">Add Entry</a>
<?php endif; ?>
<br/><br/>

<?php 
if(count($posts)==0){
	$this->load->view('msg_info', array('msg' => 'No posts have been made to this event\'s blog!'));
}

foreach($posts as $v): ?>
<div class="detail">
	<a name="<?php echo $v->ID; ?>"></a>
	<h1><?=$v->title?></h1>
	
	<p class="info">
		Written <strong><?php echo date('M j, Y',$v->date_posted); ?></strong> at <strong><?php echo date('H:i',$v->date_posted); ?></strong> (<?php echo $v->full_name; ?>)
	</p>

	<div class="desc">
		<?php echo auto_p(auto_link($v->content)); ?>
	</div>

	<?php if(user_is_admin() || user_is_admin_event($eid)): ?>
	<a href="/event/blog/edit/<?php echo $eid; ?>/<?php echo $v->ID; ?>" class="btn-small">Edit Entry</a>
	<? endif; ?>
</div>
<?php endforeach; endif; ?>