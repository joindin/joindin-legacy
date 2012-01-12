<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class EventHelperTest extends PHPUnit_Framework_TestCase
{
	
	protected function setUp()
	{
		parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->helper('events_helper');
	}
	protected function tearDown()
	{
		
	}
	
	//--------------------
	
	/**
	 * Given an event with slides, test generation of 
	 * the correct format for $slides_list
	 *
	 */
	public function testValidSlidesList()
	{
		// find an event with slides
		$query = $this->ci->db->get_where('talks',array('slides_link !='=>''),1);
		$result = $query->result();
		
		$this->ci->load->model('event_model');
		$detail = $this->ci->event_model->getEventTalks($result[0]->event_id);
		
		$this->assertGreaterThan(0,buildSlidesList($detail));
	}
	
}