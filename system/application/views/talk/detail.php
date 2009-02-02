<h1 class="icon-talk">Talks</h1>
<?php
//echo '<pre>'; print_r($detail); print_r($comments); echo '</pre>';
//print_r($claimed);
$det=$detail[0];

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
	gmdate('m'),gmdate('d'),gmdate('Y')
);
$gmt+=(3600*$det->event_tz);
//echo '<br/> woo! gmt: '.date('m.d.Y H:i:s',$gmt).'<br/>';
//--------------------

//add the whole total from our anonymous comments
$total+=$anon_total;
$total_count=count($comments)+count($anon);
//$avg=(count($comments)>0) ? $total/$total_count : 0;
//$avg=($total_count>0) ? $total/$total_count : 0;
//$avg=$detail[0]->tavg;
//for($i=1;$i<=round($avg);$i++){ $rstr.='<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }

$avg=floor($detail[0]->tavg);
$rstr = '<img src="/inc/img/rating-' . $avg . '.gif" alt="Rating: ' . $avg . '"/>';

//change up our string if this is a confirmed, clamed talk
if(!empty($claimed)){
	$speaker='<a href="/user/view/'.$claimed[0]->userid.'">'.$det->speaker.'</a>';
}else{ $speaker=$det->speaker; }

echo '<div style="padding:10px;border:0px solid #B86F09;background-color:#E4F1E8">';
echo '<h2>'.$det->talk_title.'</h2>';
echo '<p>'.$speaker.' ('.date('m.d.Y',$det->date_given).')<br/>';
echo $det->tcid.' at <a href="/event/view/'.$det->event_id.'">'.$det->event_name.'</a> ('.$det->lang_name.')</p>'.$rstr;
echo '<p style="color:#37382F">'.nl2br($det->talk_desc).'</p>';
echo '<b style="color:#37382F">quicklink:</b> <a href="http://joind.in/'.$det->tid.'">http://joind.in/'.$det->tid.'</a>';
if($admin){
	echo '<div>';
	echo '<a href="/talk/delete/'.$det->tid.'"><img src="/inc/img/redx.png" border="0" alt="Delete talk"/></a>';	
	echo '<a href="/talk/edit/'.$det->tid.'"><img src="/inc/img/sticky.gif" border="0" alt="Edit talk"/></a>';
	echo '</div>';
}
if(isset($claimed[0]) && $this->session->userdata('ID')==$claimed[0]->userid){
	echo '<a href="/user/comemail/talk/'.$det->tid.'">email me my comments</a>';
}
echo '</div><br/>';
?>
<center>
<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</center>
<br/>
<?php
$msg=$this->session->flashdata('msg');
if($msg && !empty($msg)){ echo '<div class="notice">'.$msg.'</div><br/>'; }

echo '<table cellpadding="4" cellspacing="0" border="0" width="100%">';

foreach(array('mc'=>$comments,'an'=>$anon) as $mk=>$mv){
	if($mk=='an' && isset($mv[0])){ 
		//echo '<tr><td colspan="2" align="center"><a href="#" id="anonLink" onClick="toggleAnon('.$mv[0]->talk_id.');return false;">Click here to show '.count($mv).' anonymous comments</a></td></tr>';
		//$disp=';display:none';
		$disp=';display:block';
		$uname='';
	}elseif(isset($mv[0])){ 
		$disp=';display:block'; 
		$uname='<a href="/user/view/'.$v->user_id.'">'.$v->uname.'</a> ';
	}else{ 
		$disp=';display:block'; 
		$uname='';
	}
	foreach($mv as $k=>$v){
		if(isset($v->user_id) && $v->user_id!=0){ 
			$uname='<a href="/user/view/'.$v->user_id.'">'.$v->uname.'</a> ';
		}else{ $disp=';display:block'; $uname=''; }
		
		$an=($mk=='an') ? '_anon' : '';
		$rowid='com'.$an.'_'.$v->talk_id.'_'.$v->ID;
		
		if($v->private && !$admin){ continue; }
	
		if($mk=='an' || $v->user_id==0){
			$bg=($v->private==1) ? 'EEEEEE':'F8F8F8';
			$an='<span style="font-size:9px;font-weight:bold;color:#747474">ANONYMOUS</span><br/>';
		}else{ 
			$bg=($v->private==1) ? 'EEEEEE':'E0E7C8'; 
			$an='';
		}
		echo '<tr id="'.$rowid.'" style="background-color:#'.$bg.''.$disp.'">';
		echo '<td width="110" valign="top" align="right" style="padding-top:5px;">';
		echo '<a name="'.$v->ID.'"></a>';
		//for($i=1;$i<=$v->rating;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
        echo '<img src="/inc/img/rating-' . $v->rating . '.gif" alt="Rating: ' . $v->rating . '"/>';
	
		echo '<td><p style="font-size:12px;color:#37382F">'.$an.nl2br($v->comment).'</p>';
		echo '<span style="font-size:10px;color:#A1A58A">'.$uname.' '.date('m.d.Y H:i:s',$v->date_made).'</span></td>';
		echo '</tr>'."\n".'<tr><td colspan="2"></td></tr>';
	}
}
echo '</table><br/>';

if(true || $auth){
echo $this->validation->error_string;
echo form_open('talk/view/'.$det->tid);

//only show the form if the time for the talk has passed
//if($det->date_given<=time()){
if($det->date_given>=$gmt){
?>

<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td valign="top" class="title">Comment:</td>
	<td>
		<?php 
		$arr=array(
			'name'=>'comment',
			'value'=>$this->validation->comment,
			'cols'=>40,
			'rows'=>10
		);
		echo form_textarea($arr);
		?>
	</td>
</tr>
<tr>
	<td class="title">Rating:</td>
	<td>
		<?php
		for($i=1;$i<=5;$i++){
			echo '<a href="#" onClick="setVote('.$i.');return false;"><img id="rate_'.$i.'" src="/inc/img/thumbs_up.jpg" height="20" border="0"/></a>';
		}
		echo form_hidden('rating',$this->validation->rating);
		?>
	</td>
</tr>
<tr>
	<td class="title">Mark as private?</td>
	<td><?php echo form_checkbox('private','1'); ?></td>
</tr>
<?php if(!$this->auth){ ?>
<tr>
	<td class="title" valign="top">Type in the code:</td>
	<td>
	<input type="text" name="cinput" id="Captcha" value="" /><br/>
	<?php echo $captcha['image']; ?>
	</td>
</tr>
<? } ?>
<tr>
	<td align="right" colspan="2"><?php echo form_submit('Comment','Comment'); ?></td>
</tr>
</table>
<?php 
form_close(); 
/* close if for date */
}else{
	echo '<center><span style="font-size:13px;font-weight:bold;color:#4282C4">Currently not open for comment.</span></center>';
}

}else{
	echo '<center>Want to comment on this talk? <a href="/user/login">Log in</a> or <a href="/user/register">create a new account</a>.</center>';
}
?>
