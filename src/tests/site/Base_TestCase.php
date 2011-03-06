<?php
require_once('loadCiEnv.php');

class Base_TestCase extends PHPUnit_Framework_TestCase
{
	public $ci = null;

	public function setup()
	{
		$this->ci = &get_instance();
	}

	public function tearDown()
	{
		$this->ci = null;
	}

}

?>
