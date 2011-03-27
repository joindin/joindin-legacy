<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class UserModelTest extends PHPUnit_Framework_TestCase
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
	
}

?>