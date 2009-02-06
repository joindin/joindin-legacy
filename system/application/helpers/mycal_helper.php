<?php

function mycal_get_calendar($year, $month, $day = null)
{
    $CI =& get_instance();
    $CI->load->model('event_model');
    
    if (null === $year) {
        $year = date('Y');
    }
    
    if (null === $month) {
        $month = date('m');
    }
    
    $events = $CI->event_model->getDayEventCounts($year, $month);

    return mycal_build_calendar($year, $month, $day, $events);
}

function mycal_build_calendar($year, $month, $day, $events = array()){
    $prevnext = array(
    	'&laquo;' => '/event/calendar/' . _mycal_format_date($year, $month - 1),
    	'&raquo;' => '/event/calendar/' . _mycal_format_date($year, $month + 1),
    );

    $first_day = 0; // First day is Sunday
    $first_of_month = gmmktime(0, 0, 0, $month, 1, $year);

    $day_names = array();
    // January 4, 1970 was a Sunday
    for($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t += 86400) {
        $day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name
    }

    list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));

    $weekday = ($weekday + 7 - $first_day) % 7; // Adjust for $first_day
    $title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;

    @list($p, $pl) = each($prevnext); 
    @list($n, $nl) = each($prevnext);

    $p = '<a class="calendar-prev" href="'.htmlspecialchars($pl).'">'.$p.'</a>';
    $n = '<a class="calendar-next" href="'.htmlspecialchars($nl).'">'.$n.'</a>';

    $calendar  = '<table class="calendar">' . "\n";
    $calendar .= '<caption class="calendar-month">'. $p . '<a href="/event/calendar/' . _mycal_format_date($year, $month) . '" class="calendar-title">' . $title . '</a>' . $n."</caption>\n<tr>\n";

    foreach ($day_names as $d) {
        $calendar .= '<th abbr="'.htmlentities($d).'">' . htmlentities(substr($d, 0, 3)) .' </th>';
    }

    $calendar .= "</tr>\n<tr>";

    // Initial "empty" days
    if ($weekday > 0) {
        for ($i = 0; $i < $weekday; $i++) {
            $calendar .= '<td class="calendar-empty">&nbsp;</td>'; 
        }
    }

    for ($d = 1, $days_in_month = gmdate('t', $first_of_month); $d <= $days_in_month; $d++, $weekday++) {
        if ($weekday == 7){
            // Start a new week
            $weekday   = 0;
            $calendar .= "</tr>\n<tr>";
        }

        $class = 'calendar-day';
        $curr  = _mycal_format_date($year, $month, $d);

        if (isset($events[$curr])) {
            $class .= ' calendar-day-events';
            $content = '<a href="/event/calendar/' . $curr . '">' . $d . '</a>';
        } else {
            $content = $d;
        }
        
        if (null !== $day && $d == $day) {
            $class .= ' calendar-day-selected';
        }

        $calendar .= '<td class="' . $class . '">' . $content . '</td>';
    }

    // Remaining "empty" days
    if ($weekday != 7) {
        for ($i = 0; $i < (7-$weekday); $i++) {
            $calendar .= '<td class="calendar-empty">&nbsp;</td>'; #initial 'empty' days
        }
    }

    return $calendar."</tr>\n</table>\n";
}

function _mycal_format_date($year, $month, $day = null)
{
    if (null !== $day) {
        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
    }
    
    return date('Y-m', mktime(0, 0, 0, $month, 1, $year));
}

//make our calendar
function buildCal($mo,$day,$yr,$events){
	$events=makeDays($events);
	//echo '<pre>'; print_r($events); echo '</pre>';
	
	$mo_start	= mktime(0,0,0,$mo,1,$yr);
	$start_dow 	= date('N',$mo_start);
	$mo_end		= mktime(0,0,0,$mo,date('t'),$yr);
	$end_dow 	= date('N',$mo_end);
	$days_mo	= date('t',$mo_start);
	$day_abbr	= array('S','M','T','W','R','F','S');
	
	//now we add on the right amount of days to complete the weeks
	$total_days=$days_mo+(7-$start_dow)+(7-$end_dow);
	
	//build the prev and next months
	$prev_mo=date('m',$mo_start); $prev_yr=$yr;
	$next_mo=date('m',$mo_start); $next_yr=$yr;
	$prev_mo-=1; if($prev_mo<1){ $prev_mo=12-$prev_mo; $prev_yr--; }
	$next_mo+=1; if($next_mo>12){ $next_mo=$next_mo-12; $next_yr++; }
	$curr_dt=date('m',$mo_start).'_'.$day.'_'.date('Y',$mo_start);
	
	$day_ct=0;
	echo '<table cellpadding="0" cellspacing="0" border="0" class="cal_tbl">'."\n";
	echo '<tr>'; foreach($day_abbr as $v){ echo '<td class="cal_day_abbr">'.$v.'</td>'; } echo '</tr>';
	echo '<tr><td class="cal_nav" colspan="2"><a href="/event/calendar/'.$prev_yr.'-'.$prev_mo.'"><<</a></td>';
	echo '<td class="cal_nav" colspan="3" align="center">'.date('F Y',$mo_start).'</td>';
	echo '<td class="cal_nav" colspan="2" align="right"><a href="/event/calendar/'.$next_yr.'-'.$next_mo.'">>></a></td></tr>';
	echo '<tr>';
	for($i=1;$i<=$total_days;$i++){
		if($i>=$start_dow && $day_ct<=$days_mo){ $day_ct++; }
		if($day_ct!=0 && $day_ct<=$days_mo){ 
			//this is a valid day of the month, check for an event
			$day_link=date('m',$mo_start).'_'.$day_ct.'_'.date('Y',$mo_start);
			$style=($day_link==$curr_dt) ? 'cal_sel_day' : '';
			$cont=(array_key_exists($day_ct,$events)) ? '<a href="/event/calendar/'.$day_link.'">'.$day_ct.'</a>' : $day_ct;
			echo '<td class="cal_day '.$style.'">'.$cont."</td>\n"; 
			
		}else{ echo "<td class=\"cal_nday\">&nbsp;</td>\n"; }
		if($i%7==0){ echo "</tr>\n<tr>"; }
	}
	echo '</tr>';
	echo '</table>';
}
//------------------
function makeDays($events){ 
	//echo '<pre>'; print_r($events); echo '</pre>';
	$days=array();
	foreach($events as $k=>$v){
		if(isset($v['day_end'])){
			for($i=$v['day_start'];$i<=$v['day_end'];$i++){
				$days[$i][]=$v;
			}
		}else{ $days[$v['day_start']][]=$v; }
	}
	return $days;
}

?>