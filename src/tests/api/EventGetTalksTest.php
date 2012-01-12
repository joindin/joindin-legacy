<?php

	require_once 'ApiEventTestBase.php';
	 
	class EventGetTalks extends ApiEventTestBase {

		public function testGetTalksJSON() {
            $this->markTestSkipped();
			// get a list of events
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'past'), 'json');
			$res = $this->decode_response($response, 'json');
			// Pick a random event from the list
			$event = $res[ rand(0, (count($res)-1)) ];

			// Get the event talks
			$talks_response = self::makeApiRequest('event', 'gettalks', array('event_id'=>$event->ID), 'json');
			$talks = $this->decode_response($talks_response, 'json');

			$this->assertExpectedTalkFields($talks);
		}
		
		public function testGetTalksXML() {
            $this->markTestSkipped();
			// get a list of events
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'past'), 'sml');
			$res = $this->decode_response($response, 'sml');
			// Pick a random event from the list
			$event = $res[ rand(0, (count($res)-1)) ];

			// Get the event talks
			$talks_response = self::makeApiRequest('event', 'gettalks', array('event_id'=>$event->ID), 'xml');
			$talks = $this->decode_response($talks_response, 'xml');

			$this->assertExpectedTalkFields($talks);
		}
		
	}
