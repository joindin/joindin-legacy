<?php
include_once(__DIR__.'/../Base_TestCase.php');

class Event_ModelTest extends Base_TestCase
{
	public function setup()
	{
		parent::setup();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Send the popular talks method a string, throws exception
	 *
	 * @expectedException Exception
	 */
	public function testPopularTalksInvalidCount()
	{
		$this->ci->load->model('talks_model','talksModel');

		$count = 'invalid';
		$this->talksModel->getPopularTalks($count);
	}

}

?>
