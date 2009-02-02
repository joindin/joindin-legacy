<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
//echo '<pre>'; print_r($events); echo '</pre>';
?>
<div class="box">
<h2 class="h1 icon-event">Upcoming Events</h2>
<?php
foreach($events as $k=>$v){
?>
<div class="row">
	<div class="img">
		<img src="/inc/img/_event.gif"/>
	</div>
	<div class="text">
    	<h3><a href="/event/view/<?php echo $v->ID; ?>"><?php echo htmlspecialchars($v->event_name); ?></a></h3>
    	<p class="info"><strong><?php echo date('M j, Y',$v->event_start); ?></strong> - <strong><?php echo date('M j, Y',$v->event_end); ?></strong> at <strong><?php echo htmlspecialchars($v->event_loc); ?></strong></p>
    	<p class="desc">
        <?php 
    	$p=explode(' ',$v->event_desc);
    	$str='';
    	for($i=0;$i<20;$i++){ if(isset($p[$i])){ $str.=$p[$i].' '; } } echo htmlspecialchars(trim($str)).'...';
        ?>
    	</p>
    	<p class="opts">
    		<a href="/event/view/<?php echo $v->ID; ?>#comments"><?php echo $v->num_comments; ?> comment<?php echo $v->num_comments == 1 ? '' : 's'?></a> |
    		<strong><?php echo $v->num_attend; ?> attending</strong> | 
    		<a href="" class="btn-small">Will you be there?</a>
    	</p>
	</div>
	<div class="clear"></div>
</div>
<?php
}
?>
</div>

<div class="box">
<h2 class="h1 icon-talk">Popular Talks</h2>
<?php 
foreach($talks as $k=>$v){
?>
<div class="row">
	<div class="img">
		<?php echo rating_image($v->tavg); ?>
	</div>
	<div class="text">
    	<h3><a href="/talk/view/<?php echo $v->ID; ?>"><?php echo htmlspecialchars($v->talk_title); ?></a></h3>
    	<p class="opts">
    		at <a href="/event/view/<?php echo $v->ID; ?>"><?php echo htmlspecialchars($v->event_name); ?></a> |
    		<a href="/event/view/<?php echo $v->ID; ?>#comments"><?php echo $v->ccount; ?> comment<?php echo $v->ccount == 1 ? '' : 's'?></a>
    	</p>
	</div>
	<div class="clear"></div>
</div>
<?php 
}
?>
</div>

<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
