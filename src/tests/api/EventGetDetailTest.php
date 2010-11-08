<?php

	require_once 'ApiEventTestBase.php';
	 
	class EventGetDetail extends ApiEventTestBase {

		public function testGetDetailJSON() {

			// get a list of events
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'), 'json');
			$res = $this->decode_response($response, 'json');
			// Pick a random event from the list
			$event = $res[ rand(0, (count($res)-1)) ];

			// Get the event detail
			$eventDetailStr = self::makeApiRequest('event', 'getdetail', array('event_id'=>$event->ID), 'json');
			$eventDetail = $this->decode_response($eventDetailStr, 'json');

			// check we only got one event
			$this->assertEquals(count($eventDetail), 1, 'event/getdetail should return one event only');

			// Check that the detail returned from the event list is the same as the event detail
			$this->assertExpectedEventFields($eventDetail);
		}

		public function testGetDetailXML() {

			// get a list of events
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'), 'xml');
			$res = $this->decode_response($response, 'xml');
			// Pick a random event from the list
			$event = $res[ rand(0, (count($res)-1)) ];

			// Get the event detail
			$eventDetailStr = self::makeApiRequest('event', 'getdetail', array('event_id'=>$event->ID), 'xml');
			$eventDetail = $this->decode_response($eventDetailStr, 'xml');

			// check we only got one event
			$this->assertEquals(count($eventDetail), 1, 'event/getdetail should return one event only');

			// Check that the detail returned from the event list is the same as the event detail
			$this->assertExpectedEventFields($eventDetail);
		}

	}
