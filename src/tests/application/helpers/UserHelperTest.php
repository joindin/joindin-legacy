<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class UserHelperTest extends PHPUnit_Framework_TestCase
{
	private $_username	= 'enygma';
	
	protected function setUp()
	{
		parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->helper('user_helper');
		$this->ci->load->library('session');
	}
	protected function tearDown()
	{
		
	}
	
	//--------------------
	
	public function testIsUserLoggedIn()
	{
		$this->ci->load->model('user_model');
		
		$userDetail = $this->ci->user_model->getUser($this->_username);
        $this->ci->session->set_userdata((array)$userDetail[0]);

		$this->assertTrue(user_is_auth());

		
	}
	
}