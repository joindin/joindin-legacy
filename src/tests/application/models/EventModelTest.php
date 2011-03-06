<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class EventModelTest extends PHPUnit_Framework_TestCase
{
	private $_em = null;

    protected function setUp()
    {
        parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->model('event_model');
		
    }

    /** @test */
    public function testGetEventDetail()
    {
        $this->assertTrue(true);
    }

	public function testValidEventDetail()
	{
		$eventId 	= 100;
		$detail 	= $this->ci->event_model->getEventDetail($eventId);
		$this->assertEquals($eventId,$detail[0]->ID);
	}
}