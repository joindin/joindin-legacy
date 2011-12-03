<?php
menu_pagetitle('Start');
?>

<?php
//display welcome block
if(!user_is_auth()) {
?>
<div class="box">
<p>Welcome to joind.in!  This is the site where event attendees can leave feedback on an event and its sessions.  Do you have an opinion?  Then <a href="/user/register">register an account</a> and share it!</p>
</div>
<?php
}
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

