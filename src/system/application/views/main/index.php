<?php
menu_pagetitle('Start');
//echo '<pre><b>TALKS:</b>'; print_r($talks); echo '</pre>';
//echo '<pre>'; print_r($events); echo '</pre>';
//echo '<pre>'; print_r($latest_blog); echo '</pre>';

?>
<?php if (count($hot_events) > 0): ?>
<div class="box">
<h2 class="h1 icon-event">Hot Events <a class="more" href="/event/hot">More &raquo;</a></h2>
<?php
foreach ($hot_events as $k=>$v) {
    $this->load->view('event/_event-row', array('event'=>$v));
}
?>
</div>
<?php endif; ?>

<?php if (count($talks) > 0): ?>
<div class="box">
<h2 class="h1 icon-talk">Popular Talks</h2>
<?php
foreach ($talks as $k=>$v) {
    $this->load->view('talk/_talk-row', array('talk'=>$v));
}
?>
</div>
<?php endif; ?>

<?php $this->load->view('main/ads'); ?>
