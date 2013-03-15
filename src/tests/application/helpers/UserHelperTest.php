<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class UserHelperTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->helper('user_helper');
		$this->ci->load->library('session');
	}

	/**
	 * Check to ensure user logged in check works
	 */
	public function testIsUserLoggedIn()
	{
		$this->ci->load->model('user_model');
		
		$userDetail = $this->ci->user_model->getUserByID(1);
		$this->ci->session->set_userdata((array)$userDetail[0]);

		$this->assertTrue(user_is_auth());
	}


	/**
	 * Check the return of user_is_admin for a valid admin user
	 */
	public function testCheckUserIsAdmin()
	{
		$result = $this->ci->db->get_where('user',array('admin'=>1),1)->result();
		$this->ci->session->set_userdata((array)$result[0]);

		$this->assertTrue(user_is_admin());
	
	}
}
