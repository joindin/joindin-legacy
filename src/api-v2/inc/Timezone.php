<?php
/**
 * Contains Timezone class for use in API v2
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2013 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Class library to work with data/time issues for events/talks
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2013 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
class Timezone
{
    /**
     * Gets a DateTime object for the given Unix timestamp $unixtime and timezone
     * offset $timezone, taking into account that DST may have changed between now
     * and then
     *
     * @param integer $unixtime Unix timestamp to get DateTime object from
     * @param string  $timezone Timezone for DateTime object - if blank, it will be
     *                          UTC
     *
     * @return DateTime
     */
    public static function getDatetimeFromUnixtime($unixtime, $timezone)
    {
        $datetime = new DateTime("@$unixtime");

        // if a timezone is specified, adjust times
        if ($timezone != '' && $timezone != '/') {
            $tz = new DateTimeZone($timezone);
        } else {
            $tz = new DateTimeZone('UTC');
        }
        $datetime->setTimezone($tz);

        $datetime = new DateTime("@{$unixtime}");
        $datetime->setTimezone($tz);
        return $datetime;
    }

    /**
     * Get the current time at the event, where the event has a timezone offset
     * of $evt_offset hours
     *
     * @param integer $evt_offset Offset for time
     *
     * @return integer
     */
    public static function getEventTime($evt_offset)
    {
        $here    = new DateTimeZone(date_default_timezone_get());
        $hoffset = $here->getOffset(new DateTime("now", $here));
        $off     = (time() - $hoffset) + ($evt_offset * 3600);
        return $off;
    }

    /**
     * Returns a formatted version of getDatetimeFromUnixtime.
     *
     * @param integer $unixtime Unix time to format
     * @param string  $timezone Timezone to set for timestamp
     * @param string  $format   Format to return
     *
     * @return string
     */
    public static function formattedEventDatetimeFromUnixtime(
        $unixtime,
        $timezone,
        $format
    ) {
        $datetime = static::getDatetimeFromUnixtime($unixtime, $timezone);
        $retval   = $datetime->format($format);

        return $retval;
    }

    /**
     * Returns a Unix timestamp for the given specific time in the given timezone
     *
     * @param string  $timezone Timezone to use
     * @param integer $year     Year to use
     * @param integer $month    Month to use
     * @param integer $day      Day to use
     * @param integer $hour     Hour to use
     * @param integer $minute   Minute to use
     * @param integer $second   Second to use
     *
     * @return integer
     */
    public static function UnixtimeForTimeInTimezone(
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

        $unixTimestamp = $dateObj->format("U");

        return $unixTimestamp;
    }
}

