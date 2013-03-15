<?php
namespace JoindinTest\Inc;

require_once __DIR__ . '/../../inc/Timezone.php';
class TimezoneTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Makes sure the UnixtimeForTimeInTimezone function is working correctly.
     *
     * @param string  $timezone Timezone to set
     * @param integer $year     Year to set
     * @param integer $month    Month to set
     * @param integer $day      Day to set
     * @param integer $hour     Hour to set
     * @param integer $minute   Minute to set
     * @param integer $second   Second to set
     * @param integer $expected Expected unix timestamp from other variables
     *
     * @return void
     *
     * @nottest
     * @dataProvider timeProvider
     */
    public function canGetUnixTimeForTimeInTimeZone(
        $timezone,
        $year,
        $month,
        $day,
        $hour,
        $minute,
        $second,
        $expected
    ) {
        $this->markTestSkipped(
            'Test is brittle and fails or passes based on timezone changes.'
        );
        $this->assertEquals(
            $expected,
            \Timezone::UnixtimeForTimeInTimezone($timezone, $year, $month, $day, $hour, $minute, $second)
        );
    }

    /**
     * Provides values for testing and an expected unix timestamp to get back out
     *
     * @return array
     */
    public function timeProvider()
    {
        return array(
            array('America/Denver', 2009, 2, 13, 16, 31, 30, 1234567890),
            array('GMT', 1970, 1, 1, 0, 0, 0, 0),
            array('America/Denver', 1969, 12, 31, 17, 0, 0, 0)
        );
    }

    /**
     * Ensures that a correct format will be returned from given timestamps for a particular timezone
     *
     * @param integer $timestamp Unix Timestamp
     * @param string  $timezone  Timezone
     * @param string  $format    Date format
     * @param string  $expected  Expected output
     *
     * @return void
     *
     * @nottest
     * @dataProvider formattedDateProvider
     */
    public function datesAreFormattedAsExpected($timestamp, $timezone, $format, $expected)
    {
        $this->markTestSkipped(
            'Test is brittle and fails or passes based on timezone changes.'
        );
        $this->assertEquals($expected, \Timezone::formattedEventDatetimeFromUnixtime($timestamp, $timezone, $format));
    }

    /**
     * Provides a series of formatted dates and the parameters needed to get them
     *
     * @return array
     */
    public function formattedDateProvider()
    {
        return array(
            array(1234567890, 'America/Denver', 'm/d/Y H:i:s', '02/13/2009 16:31:30'),
            array(0, 'GMT', 'm/d/Y h:i:s a', '01/01/1970 12:00:00 am'),
            array(0, 'America/Denver', DATE_RFC822, 'Wed, 31 Dec 69 17:00:00 -0700'),
        );
    }

    /**
     * Ensure that if the timezone is not specified then UTC is assumed
     *
     * @return void
     *
     * @test
     */
    public function ifTimezoneIsNotSpecifiedThenAssumedTimezoneIsUTC()
    {
        $datetime = \Timezone::getDatetimeFromUnixtime(0, '');

        $this->assertEquals(new \DateTimeZone('UTC'), $datetime->getTimezone());
    }
}

