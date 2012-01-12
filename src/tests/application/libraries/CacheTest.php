<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class CacheTest extends PHPUnit_Framework_TestCase
{
	
	protected function setUp()
	{
		parent::setUp();

		$this->ci = &get_instance();
		$this->ci->load->library('cache');
	}
	protected function tearDown()
	{
		
	}
	
	//--------------------
	
	/**
	 * Test a sample caching of data
	 *
	 */
	public function testSetCacheData()
	{
		$data 		= array('foo'=>'bar');
		$cacheName 	= 'test-cache';
		
		$this->ci->cache->cacheData($cacheName,$data);
		$cacheData = $this->ci->cache->getData($cacheName);
		$this->assertEquals($cacheData,$data);
	}
	
}