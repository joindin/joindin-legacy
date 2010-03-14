<?php
//print_r($data);
$data=$data[0];

echo "BEGIN:VCALENDAR\n";
echo "VERSION:2.0\n";
echo "PROID:\n";
echo "BEGIN:VEVENT\n";
echo "DTSTART:".date('Ymd',$data->event_start)."T".date('His',$data->event_start)."Z\n";
echo "DTEND:".date('Ymd',$data->event_end)."T".date('His',$data->event_end)."Z\n";
echo "SUMMARY:".$data->event_name."\n";
echo "DESCRIPTION:".$data->event_desc."\n";
echo "END:VEVENT\n";
echo "END:VCALENDAR\n";
?>