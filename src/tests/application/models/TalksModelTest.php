<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class TalksModelTest extends PHPUnit_Framework_TestCase
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
		if (!is_array($result) || count($result) == 0) {
			$this->markTestSkipped("No talk with a claim in the DB");
		}
		$talkId = $result[0]->talk_id; // first talk
		$claims = $this->ci->talks_model->talkClaimDetail($talkId);

		if (!is_array($claims) || count($claims) == 0) {
			$this->markTestSkipped("No suitable claim in the DB");
		}
		$this->assertContains($result[0]->speaker_name,$claims[0]->speakers);
	}
	
}

?>
