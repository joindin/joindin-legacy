<?php
/**
 * Event model test
 *
 * PHP version 5
 *
 * @category Model
 * @package  APIv2_Tests
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Event model test
 *
 * @category Model
 * @package  APIv2_Tests
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class Model_EventModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up
     * 
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test getEventList
     * 
     * @return void
     */
    public function testGetAllEventsReturnsAList()
    {
        $db = getDbAdapater();
        $resultsperpage = 20;
        $start = 0;
        $verbose = 0;
        $list = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
        
        $this->assertGreaterThanOrEqual(2, count($list));
        $this->assertEquals('1', $list[count($list)-1]['event_id']);
    }
    
    /**
     * Test getEventList's paging
     * 
     * @return void
     */
    public function testGetAllEventsPages1And2AreDifferent()
    {
        $db = getDbAdapater();
        $resultsperpage = 1;
        $start = 0;
        $verbose = 0;
        $page1 = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
        
        $start = 1;
        $page2 = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
        
        $this->assertEquals('1', count($page1));
        $this->assertNotEquals($page1[0], $page2[0]);
    }
    
    /**
     * Test getEventList's verbose setting works
     * 
     * @return void
     */
    public function testGetAllEventsVerbose()
    {
        $db = getDbAdapater();
        $resultsperpage = 1;
        $start = 0;
        $verbose = 0;
        $simple = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
       
        $verbose = 1;
        $verbose = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
        $this->assertTrue(array_key_exists('tz_continent', $verbose[0]));
        $this->assertNotEquals($simple[0], $verbose[0]);
    }

}
