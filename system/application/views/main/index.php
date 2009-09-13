<?php
menu_pagetitle('Start');
if(user_is_authenticated()) {
    $this->load->view('sidebar/claim-session');
}
?>




<?php $this->load->view('message/flash'); ?>

<?php if (count($hotEvents) > 0): ?>
<div class="box">
    <h2 class="h1 icon-event">Hot Events <a class="more" href="/event/hot">More &raquo;</a></h2>
    <?php 
    foreach($hotEvents as $event){
        $this->load->view('event/_event-row', array('event' => $event));
    }
    ?>
</div>
<?php endif; ?>

<?php if (count($upcomingEvents) > 0): ?>
<div class="box">
    <h2 class="h1 icon-event">Upcoming Events <a class="more" href="/event/upcoming">More &raquo;</a></h2>
    <?php
    foreach($upcomingEvents as $event){
        $this->load->view('event/_event-row', array('event'=> $event));
    }
    ?>
</div>
<?php endif; ?>

<?php if (count($popularSessions) > 0): ?>
<div class="box">
    <h2 class="h1 icon-talk">Popular Sessions</h2>
    <?php 
    foreach($popularSessions as $session){
        $this->load->view('session/_session-row', array('session' => $session));
    }
    ?>
</div>
<?php endif; ?>

<script type="text/javascript"><!--
google_ad_client = "pub-2135094760032194";
/* 468x60, created 11/5/08 */
google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
