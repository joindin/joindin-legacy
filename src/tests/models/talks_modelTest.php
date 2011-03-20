<?php
require_once 'PHPUnit/Framework.php';
class talks_modelTest extends PHPUnit_Framework_TestCase
{
    protected $_talks;
    
    protected function setUp()
    {
        parent::setUp();
        $this->_talks = new Talks_model();
    }
    protected function tearDown()
    {
        $this->_talks = null;
        parent::tearDown();
    }
    public function testGetTalksReturnsListOfTalks()
    {
        $result = $this->_talks->getTalks();
        var_dump($result);
        $this->assertNotNull($result);
    }
    public function testGetRecentTalksReturnsListOfTalks()
    {
        $result = $this->_talks->getRecentTalks();
        $this->assertNotNull($result);
    }
}