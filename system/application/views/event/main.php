<?php
//ob_start();
//buildCal($mo,$day,$yr,$evt);
menu_sidebar('Calendar', mycal_get_calendar($year, $month, $day));

$title = '';
if (!empty($year) && !empty($month)) {
    if (!empty($day)) {
        $title .= ' for ' . date('F j, Y', mktime(0, 0, 0, $month, $day, $year));
    } else {
        $title .= ' for ' . date('F Y', mktime(0, 0, 0, $month, 1, $year));
    }
}
?>
<h1 class="icon-event">
	<?php if(user_is_admin()){ ?>
	<span style="float:left">
	<?php } ?>
	Events<?php echo $title; ?>
	<?php if(user_is_admin()){ ?>
	</span>
	<?php } ?>
	<?php if(user_is_admin()){ ?>
	<a class="btn" style="float:right" href="/event/add">Add new event</a>
	<div class="clear"></div>
    <?php } ?>
</h1>

<?php
foreach($events as $k=>$v){
	$this->load->view('event/_event-row', array('event'=>$v));
}

if (count($events) == 0) {
    if (!empty($year) && !empty($month)) {
        if (!empty($day)) {
            echo '<h2>No events found for this day!</h2>';
        } else {
            echo '<h2>No events found for this month!</h2>';
        }
    } else {
	    echo '<h2>No events found!</h2>';
    }
?>
<p>
	Know of an event happening this month? <a href="/event/submit">Let us know!</a>
	We love to get the word out about events the community would be interested in and
	you can help us spread the word!
</p>
<p>
	<a href="/event/submit/" class="btn-big">Submit your event!</a>
</p>
<?php
}
?>
