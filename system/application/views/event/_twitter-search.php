<?php

?>
<style>
a.twitter_lnk { text-decoration: none; }
a.twitter_lnk:hover { text-decoration: underline; }
</style>
<div class="box">
	<h4><?php echo $title; ?></h4>
	<div class="ctn">
		<p>
		The people on Twitter are talking...
		</p>
		<ul>
    	<?php 
			//echo '<pre>'; print_r($results); echo '</pre>';
			foreach($results[0] as $k=>$v){
				echo '<li style="padding-top:5px"><a class="twitter_lnk" href="http://twitter.com/'.$v->from_user.'/statuses/'.$v->id.'">'.$v->text.'</a>';
			}
		?>
		</ul>
	</div>
</div>