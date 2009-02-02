<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
//echo '<pre>'; print_r($events); echo '</pre>';
?>
<div class="box">
<h2 class="h1 icon-event">Upcoming Events</h2>
<?php
foreach($events as $k=>$v){
    $this->load->view('event/_event-row', array('event'=>$v));
}
?>
</div>

<div class="box">
<h2 class="h1 icon-talk">Popular Talks</h2>
<?php 
foreach($talks as $k=>$v){
    $this->load->view('talk/_talk-row', array('talk'=>$v));
}
?>
</div>

<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
