<?php

/**
* Class library to work with data/time issues for events/talks
*/
class Timezone {
	
	/**
	 * Gets a DateTime object for the given Unix timestamp $unixtime and timezone offset $timezone, taking
	 * into account that DST may have changed between now and then
	 */
	public static function getDatetimeFromUnixtime($unixtime, $timezone) {
		$datetime = new DateTime("@$unixtime");

		// if a timezone is specified, adjust times
		if($timezone != '' && $timezone != '/') {
			$tz = new DateTimeZone($timezone);
		} else {
			$tz = new DateTimeZone('UTC');
		}
		$datetime->setTimezone($tz);

		// How much wrong will ->format("U") be if I do it now, due to DST changes?
		// Only needed until PHP Bug #51051 delivers a better method
		$unix_offset1 = $tz->getOffset($datetime);
		$unix_offset2 = $tz->getOffset(new DateTime());
		$unix_correction = $unix_offset1 - $unix_offset2;

		// create datetime object corrected for DST offset
		$timestamp = $unixtime + $unix_correction;

		$datetime = new DateTime("@{$timestamp}");
		$datetime->setTimezone($tz);
		return $datetime;
	}

	/**
	 * Get the current time at the event, where the event has a timezone offset
	 * of $evt_offset hours
	 */
	public static function getEventTime($evt_offset){
		$here	= new DateTimeZone(date_default_timezone_get());
		$hoffset= $here->getOffset(new DateTime("now",$here));
		$off	= (time()-$hoffset)+($evt_offset*3600); 
		return $off;
	}

	/** 
	 * Returns a formatted version of getDatetimeFromUnixtime.
	 */
	public static function formattedEventDatetimeFromUnixtime($unixtime, $timezone, $format) {
		$datetime = static::getDatetimeFromUnixtime($unixtime, $timezone);
		$retval = $datetime->format($format);
		return $retval;
	}

	/**
	 * Returns a Unix timestamp for the given specific time in the given timezone
	 */
	public static function UnixtimeForTimeInTimezone($timezone, $year, $month, $day, $hour, $minute, $second) {

		$tz = new DateTimeZone($timezone);

		// Get offset unix timestamp for start of event
		$dateObj = new DateTime();
		$dateObj->setTimezone($tz);
		$dateObj->setDate($year, $month, $day);
		$dateObj->setTime($hour, $minute, $second);

		// How much wrong will ->format("U") be if I do it now, due to DST changes?
		// Only needed until PHP Bug #51051 delivers a better method
		$unix_offset1 = $tz->getOffset($dateObj);
		$unix_offset2 = $tz->getOffset(new DateTime());
		$unix_correction = $unix_offset1 - $unix_offset2;

		$unixTimestamp = $dateObj->format("U") - $unix_correction;

		return $unixTimestamp;
	}
	
}

