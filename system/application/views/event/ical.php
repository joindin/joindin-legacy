BEGIN:VCALENDAR
VERSION:2.0
PROID:
BEGIN:VEVENT
DTSTART:<?= date('Ymd', $event->getStart()) ?>T<?= date('His', $event->getStart()) ?>Z
DTEND:<?= date('Ymd', $event->getEnd()) ?>T<?= date('His', $event->getEnd()) ?>Z
SUMMARY:<?= $event->getTitle() ?>
DESCRIPTION:<?= $event->getDescription() ?>
END:VEVENT
END:VCALENDAR
