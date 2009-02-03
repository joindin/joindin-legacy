<?php
$det=$events[0]; //print_r($det);
$cl=array();
foreach($claimed as $k=>$v){ $cl[$v->rid]=$v->uid; }

//print_r($_COOKIE);
?>

<div class="detail">
	<div class="img">
		<div class="frame"><img src="/inc/img/_event<?php echo mt_rand(1,4); ?>.gif"/></div>
	</div>
	
	<h1><?=$det->event_name?></h1>

	<p class="info">
		<strong><?php echo date('M j, Y',$det->event_start); ?></strong> - <strong><?php echo date('M j, Y',$det->event_end); ?></strong>
		<br/> 
		<strong><?php echo htmlspecialchars($det->event_loc); ?></strong>
	</p>

	<p class="desc">
		<?=nl2br($det->event_desc)?>
	</p>
	
	<p class="opts">
	<?php 
	/*
	if its set, but the event was in the past, just show the text "I was there!"
	if its set, but the event is in the future, show a link for "I'll be there!"
	if its not set show the "I'll be there/I was there" based on time
	*/
	if($attend){
		if($det->event_end<time()){
			$link_txt="I was there!"; $showt=1;
		}else{ $link_txt="I'll be there!"; $showt=2; }
	}else{
		if($det->event_end<time()){
			$link_txt="Were you there?"; $showt=3; 
		}else{ $link_txt="Will you be there?"; $showt=4; }
	}
	?>
		<a class="btn<?php echo $attend ? ' btn-success' : ''; ?>" href="#" id="attend_link" onClick="markAttending(<?=$det->ID?>,<?=$showt?>);return false;"><?=$link_txt?></a>
	</p>
	<div class="clear"></div>
</div>

<?php if($admin): ?>
<p class="admin">
	<a class="btn-small" href="/event/delete/<?=$det->ID?>">Delete event</a>
	<a class="btn-small" href="/event/edit/<?=$det->ID?>">Edit event</a>
	<a class="btn-small" href="/talk/add">Add new talk</a>
	&nbsp;
	<a class="btn-small" href="/event/codes/<?=$det->ID?>">Get talk codes</a>
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

$by_day=array();
//echo '<pre>'; print_r($talks); echo '</pre>';
foreach($talks as $v){
	//echo '<a href="/talk/view/'.$v->ID.'">'.$v->talk_title.' ('.$v->speaker.')</a><br/>';
	$day=date('Y-m-d',$v->date_given);
	$by_day[$day][]=$v;
}
ksort($by_day);
$ct=0;

?>

<div id="event-tabs">
	<ul>
		<li><a href="#talks">Talks (<?=count($talks)?>)</a></li>
		<li><a href="#comments">Comments (<?=count($comments)?>)</a></li>
	</ul>
	<div id="talks">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
        <?php 
        foreach ($by_day as $k=>$v):
            $ct = 0;
        ?>
        	<tr>
        		<th colspan="4">
        			<h4 id="talks-<?php echo $k; ?>"><?php echo date('M j, Y', strtotime($k)); ?></h4>
        		</th>
        	</tr>
        	<?php foreach($v as $ik=>$iv): ?>
        	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        		<?php $sp=(array_key_exists((string)$iv->ID,$cl)) ? '<a href="/user/view/'.$cl[$iv->ID].'">'.$iv->speaker.'</a>' : $iv->speaker; ?>
        		<td>
        			<a href="/talk/view/'.$iv->ID.'"><?php echo $iv->talk_title; ?></a>
        		</td>
        		<td style="font-size:10px;font-weight:bold;color:#858585">
        			<?php echo strtoupper($iv->tcid); ?>
        		</td>
        		<td>
        			<img src="/inc/img/flags/<?php echo $iv->lang; ?>.gif"/>
        		</td>
        		<td nowrap="nowrap">
        			<?php echo $sp; ?>
        		</td>
        	<tr/>
        <?php
        	    $ct++;
            endforeach;
        endforeach;
        ?>
        </table>
	</div>
	<div id="comments">
	
    <?php
	if(isset($msg)){ echo '<div class="notice">'.$msg.'</div>'; }
	
	echo $this->validation->error_string;
	echo form_open('event/view/'.$det->ID.'#comments');
	
	$types=array(
		'Suggestion'		=> 'Suggestion',
		'General Comment'	=> 'General Comment',
		'Feedback'			=> 'Feedback'
	);
	$type=($det->event_start>time()) ? 'Suggestion':'Feedback';
	?>
	<table cellpadding="3" cellspacing="0" border="0">
	<?php
	if($user_id==0){
	?>
	<tr>
		<td>Name:</td>
		<td><?php echo form_input('cname',$this->validation->cname); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>Type:</td>
		<td><?=$type?></td>
	</tr>
	<tr>
		<td valign="top">Comment:</td>
		<td>
		<?php 
		$arr=array(
			'name'=>'event_comment',
			'value'=>$this->validation->event_comment,
			'cols'=>50,
			'rows'=>8
		);
		echo form_textarea($arr);
		?>
		</td>
	</tr>
	<tr><td colspan="2" align="right"><?php echo form_submit('sub','Submit'); ?></td></tr>
	</table>
	<?php 
	echo form_close(); 
	
	//print_r($comments);
	$ct=0;
	echo '<table cellpadding="0" cellspacing="2" border="0" width="100%" class="event_comments">';
	foreach($comments as $k=>$v){
		$class  = ($ct%2==0) ? 'row1' : 'row2';
		$name	= ($v->user_id!=0) ? '<a href="/user/view/'.$v->user_id.'">'.$v->cname.'</a>' : $v->cname;
		$type	= ($det->event_start>time()) ? 'Suggestion' : 'Feedback';
		
		echo '<tr class="'.$class.'"><td><span class="comment">'.$v->comment.'</span><br/>';
		echo '<span class="meta">'.$name.', '.date('m.d.Y H:i:s',$v->date_made).' ('.$type.')</td></tr>';
		$ct++;
	}
	echo '</table>';
	?>
	
	</div>
</div>

<script type="text/javascript">
$(function() { $('#event-tabs').tabs(); });
</script>

<script>
function switchCell(n){
	if(n=='talks'){
		document.getElementById('cell_comments').className='nselected';
		document.getElementById('cell_talks').className='selected';
		$('#talks_div').css('display','block');
		$('#comments_div').css('display','none');		
	}else{
		document.getElementById('cell_comments').className='selected';
		document.getElementById('cell_talks').className='nselected';
		$('#talks_div').css('display','none');
		$('#comments_div').css('display','block');
	}
	return false;
}
</script>

<script>
if(window.location.hash=='#comments'){
	switchCell('comments');
}else{ 
	var talk_num=<?=count($talks)?>;
	if(talk_num<=0){
		switchCell('comments'); 
	}else{ switchCell('talks'); }
}
</script>
	