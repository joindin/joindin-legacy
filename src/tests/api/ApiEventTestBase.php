<?php
	 
	require_once 'ApiTestBase.php';

	class ApiEventTestBase extends ApiTestBase {
		
		protected function assertExpectedEventFields($res) {
			foreach($res as $event) {
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $event);
				$this->assertLooksLikeAString($event->event_name);
				$this->assertLooksLikeAString($event->event_start);
				$this->assertLooksLikeAString($event->event_end);
				$this->assertLooksLikeAString($event->ID);
				$this->assertLooksLikeAString($event->event_loc);
				$this->assertLooksLikeAString($event->event_desc);
				$this->assertLooksLikeAString($event->active);
				$this->assertLooksLikeAString($event->num_attend);
				$this->assertLooksLikeAString($event->num_comments);

				$this->assertLooksLikeAStringOrNull($event->event_stub);
				$this->assertLooksLikeAStringOrNull($event->event_icon);
				$this->assertLooksLikeAStringOrNull($event->event_cfp_start);
				$this->assertLooksLikeAStringOrNull($event->event_cfp_end);
				$this->assertLooksLikeAStringOrNull($event->event_hashtag);
				$this->assertLooksLikeAStringOrNull($event->event_href);
				$this->assertLooksLikeAStringOrNull($event->event_tz_cont);
				$this->assertLooksLikeAStringOrNull($event->event_tz_place);
				$this->assertLooksLikeAStringOrNull($event->event_cfp_start);
				$this->assertLooksLikeAStringOrNull($event->event_cfp_end);
				if((string)$event->event_tz_cont && (string)$event->event_tz_place) {
					$tz = $event->event_tz_cont.'/'.$event->event_tz_place;
					$tzObj = new DateTimeZone($tz);
					$this->assertTrue($tzObj instanceOf DateTimeZone);
				}

				$this->assertTrue(is_numeric((string)$event->event_start));
				$this->assertTrue(is_numeric((string)$event->event_end));
				$this->assertTrue(empty($event->event_cfp_start) || is_numeric((string)$event->event_cfp_start));
				$this->assertTrue(empty($event->event_cfp_end) || is_numeric((string)$event->event_cfp_end));
				$this->assertTrue(is_numeric((string)$event->num_attend));
				$this->assertTrue(is_numeric((string)$event->num_comments));

				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->active) === '1', "Expected active to be 1 for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->pending) === '0'
									|| $this->optionallyConvertSimpleXML($event->pending) === null, 
									"Expected pending to be 0 or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->event_voting === 'Y')
									|| $this->optionallyConvertSimpleXML($event->event_voting === '0')
									|| empty($event->event_voting),
									"Expected event_voting to be Y, 0 or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->private) === 'N'
									|| $this->optionallyConvertSimpleXML($event->private) ==='0' 
									|| $this->optionallyConvertSimpleXML($event->private) === null,
									"Expected private to be zero, N or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->allow_comments) === '0'
									|| $this->optionallyConvertSimpleXML($event->allow_comments) === '1'
				);
				/*
				// this expects false rather than null
				print_r($event->ID);
				print_r($event->user_attending);
				var_dump($this->optionallyConvertSimpleXML($event->user_attending));
				$this->assertTrue(
									$this->optionallyConvertSimpleXML($event->user_attending) === false
									|| $this->optionallyConvertSimpleXML($event->user_attending) === true
				);
				*/
			}
		}

	}

