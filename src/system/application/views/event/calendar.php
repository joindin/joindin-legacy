
<h2>Events Calendar</h2>

<style>
table.calendar th { height: 30px; }
table.calendar td.calendar-day { height: 35px; }
</style>

<?php echo mycal_get_calendar($year, $month, $day); ?>
<br/>
<?php
foreach ($events as $event) {
    echo '<a style="font-size:14px;font-weight:bold" href="/event/view/'.$event->ID.'">'.$event->event_name.'</a><br/>';
    echo date('d.M.Y', $event->event_start);
    if ($event->event_start+86399 != $event->event_end) {
        echo ' - '.date('d.M.Y', $event->event_end);
    }
    echo ' <br/>';
    $split_by_space=explode(' ', $event->event_desc);
    echo implode(" ", array_slice($split_by_space,0,80)).'...';
    echo '<br/><br/>';
}
