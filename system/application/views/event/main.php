<?php

if (!isset($type)) {
	$type = 'all';
}

switch ($type) {
    case 'hot':
        $title = 'Hot Events';
        break;
    case 'upcoming':
        $title = 'Upcoming Events';
        break;
    case 'past':
        $title = 'Past Events';
        break;
    default:
        $title = 'Events';
        break;
}

if (!empty($year) && !empty($month)) {
    if (!empty($day)) {
        $title .= ' for ' . date('F j, Y', mktime(0, 0, 0, $month, $day, $year));
    } else {
        $title .= ' for ' . date('F Y', mktime(0, 0, 0, $month, 1, $year));
    }
}

menu_pagetitle($title);
menu_sidebar('Calendar', mycal_get_calendar($year, $month, $day));

?>
<h1>
	<span style="float:left">
    	<?= $title; ?>
	</span>
	<?php if(user_is_admin()){ ?>
	<a class="btn" style="float:right" href="/event/add">Add new event</a>
    <?php } ?>
    <div class="clear"></div>
</h1>

<p class="filter">
    <a href="/event/">All</a>&nbsp;|&nbsp;
    <a href="/event/hot">Hot</a>&nbsp;|&nbsp;
    <a href="/event/upcoming">Upcoming</a>&nbsp;|&nbsp;
    <a href="/event/past">Past</a>
	<?php if(user_is_administrator()) : ?>
	&nbsp;||&nbsp;
	<a href="/event/pending">Pending</a>
	<?php endif; ?>
</p>

<?php $this->load->view('message/flash') ?>

<?php
foreach($events as $event) {
	$this->load->view('event/_event-row', array('event' => $event));
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
