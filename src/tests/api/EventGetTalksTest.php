<?php

	require_once 'ApiEventTestBase.php';
	 
	class EventGetTalks extends ApiEventTestBase {

		protected function assertExpectedTalkFields($talks) {

			foreach($talks as $talk) {
				$this->assertLooksLikeAString($talk->talk_title);
				if(count($talk->speaker) > 0) {
					foreach($talk->speaker as $speaker) {
						$this->assertIsASpeaker($speaker, "Expected valid speaker info for " . $talk->talk_title . " (" . $talk->ID . ")");
					}
				}
				$this->assertLooksLikeAStringOrNull($talk->slides_link);
				$this->assertTrue(is_numeric((string)$talk->date_given));
				$this->assertTrue(is_numeric((string)$talk->event_id));
				$this->assertTrue(is_numeric((string)$talk->ID));
				$this->assertLooksLikeAString($talk->talk_desc);
				$this->assertLooksLikeAStringOrNull($talk->event_tz_cont);
				$this->assertLooksLikeAStringOrNull($talk->event_tz_place);
				$this->assertTrue(is_numeric((string)$talk->event_start));
				$this->assertTrue(is_numeric((string)$talk->event_end));
				$this->assertLooksLikeAString($talk->lang);
				$this->assertLooksLikeAStringOrNull($talk->rank);
				$this->assertTrue(is_numeric((string)$talk->comment_count));
				$this->assertIsASessionType((string)$talk->tcid, "Expected valid category for " . $talk->talk_title . " (" . $talk->ID . ")");
				if(count($talk->tracks) > 0) {
					foreach($talk->tracks as $track) {
						if(isset($track->item)) {
							// it was XML, fiddle data
							$track = $track->item;
						}
						if(!empty($track)) {
							$this->assertIsATrack($track, "Expected valid track info for " . $talk->talk_title . " (" . $talk->ID . ")");
						}
					}
				}

			}
		}

		public function testGetTalksJSON() {
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
