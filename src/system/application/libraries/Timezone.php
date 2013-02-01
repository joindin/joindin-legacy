<?php
/**
 * Timezone class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Timezone class for date/timezone stuff on joind.in
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Timezone
{

    protected $CI = null;

    /**
     * Instantiates class
     */
    public function __construct()
    {
        // Create an instance of our CI object
        $this->CI = & get_instance();
    }

    /**
     * Find the local time at the event...
     *
     * @param integer $evt_offset Event's offset from UTC
     *
     * @return integer
     */
    private function _getEvtTime($evt_offset)
    {
        $here    = new DateTimeZone(date_default_timezone_get());
        $hoffset = $here->getOffset(new DateTime("now", $here));
        $off     = (time() - $hoffset) + ($evt_offset * 3600);

        return $off;
    }

    /**
     * Determine if an event has started based on the start time(stamp)
     * and the timezone of the event
     *
     * @param integer $eid   Event ID
     * @param array   $edata [optional] Event Data
     *
     * @return boolean
     */
    public function hasEvtStarted($eid, $edata = null)
    {
        if (!$edata) {
            $this->CI->load->model('event_model', 'em');
            $edata = $this->CI->em->getEventDetail($eid);
        }
        $at_event = $this->_getEvtTime($edata[0]->event_tz);

        return ($at_event >= $edata[0]->event_start) ? true : false;
    }

    /**
     * Determine if a talk for an event has started based on the time(stamp)
     * of the talk and the timezone of the event it belongs to
     *
     * @param integer $tid   Talk ID
     * @param array   $tdata [optional] Talk Data
     *
     * @return boolean
     */
    public function talkEvtStarted($tid, $tdata = null)
    {
        if (!$tdata) {
            $this->CI->load->model('talks_model', 'tm');
            $tdata = $this->CI->tm->getTalks($tid);
        }

        return $this->hasEvtStarted($tdata[0]->event_id);
    }

    /**
     * Converts unix time to date time
     *
     * @param integer $unixtime Unix epoch time
     * @param string  $timezone Timezone
     *
     * @return DateTime
     */
    public function getDatetimeFromUnixtime($unixtime, $timezone)
    {
        $datetime = new DateTime("@$unixtime");

        // if a timezone is specified, adjust times
        if ($timezone != '' && $timezone != '/') {
            $tz = new DateTimeZone($timezone);
        } else {
            $tz = new DateTimeZone('UTC');
        }
        $datetime->setTimezone($tz);

        /* Commented out as it appears to be causing a DST bug
        - see JOINDIN-169 in Jira
        // How much wrong will ->format("U") be if I do it now, due to DST changes?
        // Only needed until PHP Bug #51051 delivers a better method
        $unix_offset1 = $tz->getOffset($datetime);
        $unix_offset2 = $tz->getOffset(new DateTime());
        $unix_correction = $unix_offset1 - $unix_offset2;
        // create datetime object corrected for DST offset
        $timestamp = $unixtime + $unix_correction;
        $datetime = new DateTime("@{$timestamp}");
        $datetime->setTimezone($tz);
        */

        return $datetime;
    }

    /**
     * Returns a formatted datetime string from a unix timestamp, timezone
     * and format specification
     *
     * @param integer $unixtime Unix timestamp
     * @param string  $timezone Timezone
     * @param string  $format   Time format
     *
     * @return string
     */
    public function formattedEventDatetimeFromUnixtime(
        $unixtime,
        $timezone,
        $format
    ) {
        $datetime = $this->getDatetimeFromUnixtime($unixtime, $timezone);
        $retval   = $datetime->format($format);

        return $retval;
    }

    /**
     * Gets a unix timestamp for a time in a given timezone
     *
     * @param string  $timezone Timezone
     * @param integer $year     Year
     * @param integer $month    Month
     * @param integer $day      Day
     * @param integer $hour     Hour
     * @param integer $minute   Minute
     * @param integer $second   Second
     *
     * @return string
     */
    public function UnixtimeForTimeInTimezone(
        $timezone,
        $year,
        $month,
        $day,
        $hour,
        $minute,
        $second
    ) {
        $tz = new DateTimeZone($timezone);

        // Get offset unix timestamp for start of event
        $dateObj = new DateTime();
        $dateObj->setTimezone($tz);
        $dateObj->setDate($year, $month, $day);
        $dateObj->setTime($hour, $minute, $second);

        if (!isset($unix_correction)) {
            $unix_correction = 0;
        }
        $unixTimestamp = $dateObj->format("U") - $unix_correction;

        /* Commented out as it appears to be causing a DST bug -
        //see JOINDIN-169 in Jira
        // How much wrong will ->format("U") be if I do it now, due to DST changes?
        // Only needed until PHP Bug #51051 delivers a better method
        $unix_offset1 = $tz->getOffset($dateObj);
        $unix_offset2 = $tz->getOffset(new DateTime());
        $unix_correction = $unix_offset1 - $unix_offset2;
        $unixTimestamp = $unix_timestamp - $unix_correction;
        */

        return $unixTimestamp;
    }

}

