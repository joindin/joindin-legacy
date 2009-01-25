<?php

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
	echo '<tr><td class="cal_nav" colspan="2"><a href="/event/calendar/'.$prev_mo.'_'.$prev_yr.'"><<</a></td>';
	echo '<td class="cal_nav" colspan="3" align="center">'.date('F Y',$mo_start).'</td>';
	echo '<td class="cal_nav" colspan="2" align="right"><a href="/event/calendar/'.$next_mo.'_'.$next_yr.'">>></a></td></tr>';
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