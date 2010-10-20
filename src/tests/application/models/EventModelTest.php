<?php

require_once dirname(__FILE__) . '/../../bootstrap/CITestBase.php';

class EventModelTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $loader = load_class("Loader");
        $this->_event = $loader->model('Event_model');
    }

    /** @test */
    public function testGetEventDetail()
    {
        $this->assertTrue(false);
    }
}