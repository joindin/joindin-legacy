<?php
	 
	require_once 'ApiTestBase.php';

	class ApiEventTestBase extends ApiTestBase {
		
		protected function assertExpectedEventFields($res) {
			foreach($res as $event) {
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $event);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_name);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_start);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_end);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->ID);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_loc);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_desc);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->active);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->num_attend);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->num_comments);

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
									$event->active === '1', "Expected active to be 1 for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$event->pending === '0'
									|| empty($event->pending),
									"Expected pending to be 0 or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$event->event_voting === 'Y'
									|| $event->event_voting === '0'
									|| empty($event->event_voting),
									"Expected event_voting to be Y, 0 or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$event->private === 'N'
									|| $event->private ==='0' 
									|| empty($event->private),
									"Expected private to be zero, N or empty for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									$event->allow_comments === '0'
									|| $event->allow_comments === '1',
									"Expected allow_comments to be 1 or 0 for " . $event->event_name . "(" . $event->ID . ")"
				);
				$this->assertTrue(
									empty($event->user_attending)
									|| $event->user_attending === true,
									"Expected user_attending to be either true or false for " . $event->event_name . "(" . $event->ID . ")"
				);
			}
		}

	}

