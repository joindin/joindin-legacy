<?php
//echo '<pre>'; print_r($detail); print_r($comments); echo '</pre>';
//print_r($claimed);

$det=$detail[0];

menu_pagetitle('Talk: ' . escape($det->talk_title));

$total	= 0;
$rstr	= '';
$anon	= array();
$anon_total = 0;

foreach($comments as $k=>$v){ 
	if($v->user_id==0 && strlen($v->user_id)>=1){
		$anon[]=$v;
		//unset($comments[$k]);
		$anon_total+=$v->rating; 
	}else{
		$total+=$v->rating; 
	}
}
$anon=array();

//--------------------
$gmt=mktime(
	gmdate('h'),gmdate('i'),gmdate('s'),
	//0,0,0,
	gmdate('m'),gmdate('d'),gmdate('Y')
);
//so now we know what time it is GMT
//lets use the offset of the event to figure out what time it is there
$time_at_event=$gmt+(3600*$det->event_tz);
//--------------------

if(!empty($claim_msg)){
	$class=($claim_status) ? 'notice' : 'err';
	if($claim_msg && !empty($claim_msg)){ echo '<div class="'.$class.'">'.escape($claim_msg).'</div><br/>'; }
}

//add the whole total from our anonymous comments
$total+=$anon_total;
$total_count=count($comments)+count($anon);
//$avg=(count($comments)>0) ? $total/$total_count : 0;
//$avg=($total_count>0) ? $total/$total_count : 0;
//$avg=$detail[0]->tavg;
//for($i=1;$i<=round($avg);$i++){ $rstr.='<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }

$rstr = rating_image($detail[0]->tavg);

//change up our string if this is a confirmed, clamed talk
if(!empty($claimed)){
	$speaker='<a href="/user/view/'.$claimed[0]->userid.'">'.escape($det->speaker).'</a>';
}else{ $speaker=escape($det->speaker); }

?>
<div class="detail">
	<h1><?=$det->talk_title?></h1>

	<p class="info">
		<strong><?php echo $speaker; ?></strong> (<?php echo date('M j, Y',$det->date_given); ?>)
		<br/> 
		<?php echo escape($det->tcid); ?> at <strong><a href="/event/view/<?php echo $det->event_id; ?>"><?php echo escape($det->event_name); ?></a></strong> (<?php echo escape($det->lang_name);?>)
	</p>
	
	<p class="rating">
		<?php echo $rstr; ?>
	</p>

	<div class="desc">
		<?=auto_p(auto_link(escape($det->talk_desc)));?>
	</div>
	
	<p class="quicklink">
		Quicklink: <strong><a href="http://joind.in/<?php echo $det->tid; ?>">http://joind.in/<?php echo $det->tid; ?></a></strong>
	</p>
	
	<?php if(isset($claimed[0]) && $this->session->userdata('ID')==$claimed[0]->userid): ?>
	<p class="opts">
		<a class="btn-small" href="/user/comemail/talk/<?php echo $det->tid; ?>">Email me my comments</a>
	</p>
	<?php endif; ?>
	<div class="clear"></div>
</div>

<?php if($admin):?>
<p class="admin">
	<a class="btn-small" href="/talk/delete/<?php echo $det->tid; ?>">Delete talk</a>	
	<a class="btn-small" href="/talk/edit/<?php echo $det->tid; ?>">Edit talk</a>
</p>
<?php endif; ?>

<p class="ad">
    <script type="text/javascript"><!--
    google_ad_client = "pub-2135094760032194";
    /* 468x60, created 11/5/08 */
    google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>


<?php
$msg=$this->session->flashdata('msg');
if (!empty($msg)): 
?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<div class="box">

<h2 id="comments">Comments</h2>

<?php

if (empty($comments)) {
?>
<?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
<?php
    
} else {

    foreach ($comments as $k => $v) {
        if ($v->private && !$admin){ 
            continue; 
        }
    
        if (isset($v->user_id) && $v->user_id != 0){ 
    		$uname = '<a href="/user/view/'.$v->user_id.'">'.escape($v->uname).'</a> ';
    	}else{ 
    		$uname = '<span class="anonymous">Anonymous</span>'; 
    	}

    	$class = '';

    	if ($v->user_id == 0) {
    	    $class .= ' row-talk-comment-anonymous';
    	}

        if ($v->private == 1) {
    	    $class .= ' row-talk-comment-private';
    	}

?>
<div id="comment-<?php echo $v->ID ?>" class="row row-talk-comment<?php echo $class?>">
	<div class="img">
		<?php echo rating_image($v->rating); ?>
	</div>
	<div class="text">
    	<p class="info">
    		<strong><?php echo date('M j, Y, H:i',$v->date_made); ?></strong> by <strong><?php echo $uname; ?></strong>
    	<?php if ($v->private == 1): ?>
    		<span class="private">Private</span>
    	<?php endif; ?>
    	</p>
    	<div class="desc">
    		<?php echo auto_p(escape($v->comment)); ?>
    	</div>
	</div>
	<div class="clear"></div>
</div>
<?php
    }
}
?>
</div>
<?php
//only show the form if the time for the talk has passed
//my code: if($det->date_given<=$time_at_event){

if (false && $det->date_given < $gmt) {
?>
<p class="info">Currently not open for comment.</p>
<?php
} else {
    if (false && !$auth) {
?>
<p class="info">Want to comment on this talk? <a href="/user/login">Log in</a> or <a href="/user/register">create a new account</a>.</p>
<?php 
    } else {
?>
<h3 id="comment-form">Write a comment</h3>
<?php echo form_open('talk/view/'.$det->tid . '#comment-form', array('class' => 'form-talk')); ?>

<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>

<div class="row">
	<label for="comment">Comment</label>
	<?php 
    $arr = array(
			'name'=>'comment',
            'id'=>'comment',
			'value'=>$this->validation->comment,
			'cols'=>40,
			'rows'=>10
    );
    echo form_textarea($arr);
    ?>
    <label class="checkbox">
        <?php echo form_checkbox('private','1'); ?>
        Mark as private?
    </label>
    <div class="clear"></div>
</div>
<div class="row">
	<label for="rating">Rating</label>
	<div class="rating">
	    <?php echo rating_form('rating', $this->validation->rating); ?>
	</div>
	<div class="clear"></div>
</div>
<div class="row row-buttons">
	<?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit Comment'); ?>
</div>
<?php 
        echo form_close(); 
        /* close if for date */
    }
}
?>
