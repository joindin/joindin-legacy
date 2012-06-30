<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class UserHelperTest extends PHPUnit_Framework_TestCase
{
	private $_username	= 'johndoe';
	
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
	
	/**
	 * Check to ensure user logged in check works
	 */
	public function testIsUserLoggedIn()
	{
		$this->ci->load->model('user_model');
		
		$userDetail = $this->ci->user_model->getUserByUsername($this->_username);
		$this->ci->session->set_userdata((array)$userDetail[0]);

		$this->assertTrue(user_is_auth());
	}

	/**
	 * Check the return value of the user_get_id()
	 */
	public function testGetValidUserId()
	{
		$this->ci->load->model('user_model');

		$userDetail = $this->ci->user_model->getUserByUsername($this->_username);
                $this->ci->session->set_userdata((array)$userDetail[0]);	

		$this->assertEquals($userDetail[0]->ID,user_get_id());
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

	public function testIsEventAdmin()
	{
		$this->markTestIncomplete('Half-written');
		$this->ci->db->select('*');
		$this->ci->db->from('user_admin');
		$this->ci->db->where(array('rcode !=','pending'));
		$query = $this->ci->db->get();

		print_r($query);

		//$eventUser = $this->ci->db->get_where('user_admin',array('rcode !=','pending'))->result();
		var_dump($eventUser);
	}
	
}
