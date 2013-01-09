<?php
header('content-type: text/calendar');
header('content-disposition: attachment; filename="'.str_replace(' ', '_', $title).'.ics"');
?>
BEGIN:VCALENDAR
VERSION:2.0
<?php foreach($talks as $talk): ?>
BEGIN:VEVENT
DTSTAMP:<?php echo date('Ymd', $talk->date_given).'T'.date('His', $talk->date_given).'Z'."\n"; ?>
ORGANIZER;CN=<?php echo $talk->speaker[0]->speaker_name."\n"; ?>
DTSTART:<?php echo date('Ymd', $talk->date_given).'T'.date('His', $talk->date_given).'Z'."\n"; ?>
DTEND:<?php echo date('Ymd', $talk->date_given + 3600).'T'.date('His', $talk->date_given + 3600).'Z'."\n"; ?>
SUMMARY:<?php echo $talk->talk_title."\n"; ?>
END:VEVENT
<?php endforeach; ?>
END:VCALENDAR