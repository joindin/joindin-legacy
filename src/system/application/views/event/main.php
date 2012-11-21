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

menu_pagetitle($title);

$subtitle = '';
if (!empty($year) && !empty($month)) {
    if (!empty($day)) {
        $subtitle .= ' for ' . date('F j, Y', mktime(0, 0, 0, $month, $day, $year));
    } else {
        $subtitle .= ' for ' . date('F Y', mktime(0, 0, 0, $month, 1, $year));
    }
}
?>
<h1 class="icon-event">
    <?php if (user_is_admin()) { ?>
    <span style="float:left">
    <?php } ?>
    <?php echo $title; ?><?php echo $subtitle; ?>
    <?php if (user_is_admin()) { ?>
    </span>
    <?php } ?>
    <?php if (user_is_admin()) { ?>
    <a class="btn" style="float:right" href="/event/add">Add new event</a>
    <div class="clear"></div>
    <?php } ?>
</h1>

<p class="filter">
    <a href="/event/all">All</a> |
    <a href="/event/hot">Hot</a> |
    <a href="/event/upcoming">Upcoming</a> |
    <a href="/event/past">Past</a>
</p>

<?php
foreach ($events as $k=>$v) {
    $this->load->view('event/_event-row', array('event'=>$v));
}

if ($current_page && $total_count) {
    $this->load->view('event/modules/_event-paginate', array(
        'current_page' => $current_page,
        'total_count'  => $total_count
    ));
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
