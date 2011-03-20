<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class EventModelTest extends PHPUnit_Framework_TestCase
{

	protected function setUp()
    {
        parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->model('talks_model');
		
    }
	protected function tearDown()
	{
		
	}
	
	//------------------
	
	/**
	 * Be sure that our claim information is returning the right speaker
	 */
	public function testHasValidTalkClaimDetail()
	{
		// Find a talk with at least one claim
		$result = $this->ci->db->get_where('talk_speaker',array(
			'speaker_id >='=>'1'
		))->result();
		$talkId = $result[0]->talk_id; // first talk
		$claims = $this->ci->talks_model->talkClaimDetail($talkId);

		$this->assertContains($result[0]->speaker_name,$claims[0]->speakers);
	}
	
	public function testTalkDataDuplicateFailure()
	{
		$result 	= $this->ci->db->get_where('talks',array('ID >'=>10))->result();
		$talkDetail = $result[0];
		
		$speakers = array('Nonesuch User');
		$this->assertTrue($this->ci->talks_model->isTalkDataUnique($talkDetail,$speakers));
	}
	
}

?>