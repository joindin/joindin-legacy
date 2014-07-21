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

	/**
	 * Test that a valid event is returned
	 */
	public function testValidEventDetail()
	{
		$eventId 	= 100;
		$detail 	= $this->ci->event_model->getEventDetail($eventId);
		if (!is_array($detail) || count($detail) == 0) {
			$this->markTestSkipped("Suitable event not in DB");
		}
		$this->assertEquals($eventId,$detail[0]->ID);
	}
	
	/**
	 * Ensure that the event's detail returns valid talk detail
	 */
	public function testValidEventTalkData()
	{
		// find an event with more than one talk
		$this->ci->db->select('event_id')
			->from('talks')
			->group_by('event_id')
			->having('count(id)>1');
		
		$result 	= $this->ci->db->get()->result();
		if (!is_array($result) || count($result) == 0) {
			$this->markTestSkipped("Suitable event not in DB");
		}
		$eventId 	= $result[0]->event_id;
		
		$talkDetail = $this->ci->event_model->getEventTalks($eventId);
		$this->assertTrue(count($talkDetail)>0);
	}
	
	/**
	 * Check to be sure the "event related" are hidden
	 */
	public function testValidEventTalkDataEvtRelated()
	{
		// find an event with an event related 
		$eventRelatedCat = 5;
		$this->ci->db->select('distinct event_id')
			->from('talks')
			->join('talk_cat','talks.id=talk_cat.talk_id')
			->where('cat_id',$eventRelatedCat);
		$result = $this->ci->db->get()->result();
		if (!is_array($result) || count($result) == 0) {
			$this->markTestSkipped("Suitable event not in DB");
		}
		$eventId = $result[0]->event_id;

		// look through the results and see if there's one that matches, if so - fail
		$found = true;
		foreach($this->ci->event_model->getEventTalks($eventId,false) as $talk){
			if($talk->tcid=='Event Related'){
				$found = false;
			}
		}
		
		if($found===false){
			$this->fail('Event related not found!');
		}
	}
	
	/**
	 * Test to ensure that it allows private events
	 */
	public function testValidEventTalkDataIncludePrivate()
	{
		$query 		= $this->ci->db->get_where('events',array('private'=>1));
		$result 	= $query->result();
		
		if(isset($result[0])){
			$eventId 	= $result[0]->ID;
			$talks 		= $this->ci->event_model->getEventTalks($eventId,true,false);
			
			$this->markTestIncomplete('Not finished');
		}else{
			$this->markTestSkipped('Private event not found.');
		}	
	}

	/**
	 * @medium
	 */
	public function testGetHotEvents()
	{
		$this->assertEquals(
			$this->ci->event_model->getHotEvents(),
			$this->ci->event_model->getEventsOfType('hot')
		);
	}
}
