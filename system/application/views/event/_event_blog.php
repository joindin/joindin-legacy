<?php
// Event blog links
?>
<div class="box">
	<h4>Latest Event Blog</h4>
	<div class="ctn">
		<?php 
		if(count($entries)==0){
			echo 'No blog posts yet!<br/><br/>';
		}
		foreach($entries as $k=>$e): ?>
			<a style="font-weight:bold;font-size:13px" href="/event/blog/view/<?php echo $eid.'#'.$e->ID; ?>"><?php echo $e->title; ?></a><br/>
			<?php
			$w=explode(' ',$e->content);
			$str='';
			for($i=0;$i<=10;$i++){ if(isset($w[$i])){ $str.=$w[$i].' '; } } 
			echo '<span style="color:#A1A4AA;font-size:11px">'.substr($str,0,strlen($str)-1).'...';
			echo '<br/><span style="font-size:11px">'.date('m.d.Y',$e->date_posted).'</span></span>';
			?>
		<?php endforeach; ?>
	</div><br/>
	<a href="/event/blog/view/<?php echo $eid; ?>" class="btn-small">View blog</a>
</div>
