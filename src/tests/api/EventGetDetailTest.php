<?php

	require_once 'ApiTestBase.php';
	 
	class EventGetDetail extends ApiTestBase {

		public function testGetDetail() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'));
			$res = json_decode($response);
			// Pick a random event
			$event = $res[ rand(0, (count($res)-1)) ];
			// Get the event detail
			$eventDetailStr = self::makeApiRequest('event', 'getdetail', array('event_id'=>$event->ID));
			$eventDetail = json_decode($eventDetailStr);

			// Check that the detail returned from the event list is the same as the event detail
			// NOTE that event/getdetail returns everything in a 1-element array
//			$this->assertEquals($eventDetail, array($event));
		}

	}
