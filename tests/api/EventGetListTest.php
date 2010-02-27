<?php

	require_once 'ApiTestBase.php';
	 
	class EventGetList extends ApiTestBase {

		public function testGetListUpcomingWithAuth() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'));

			$res = json_decode($response);
			$this->assertTrue( $res !== null, "Could not decode JSON response");
			foreach($res as $event) {
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $event);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_name);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_start);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_end);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->ID);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_loc);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->event_desc);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->active);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->num_attend);
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $event->num_comments);

				$this->assertTrue(is_string($event->stub) || $event->stub === null);
				$this->assertTrue(is_string($event->event_icon) || $event->event_icon === null);
				$this->assertTrue(is_string($event->event_cfp_start) || $event->event_cfp_start === null);
				$this->assertTrue(is_string($event->event_cfp_end) || $event->event_cfp_end === null);
				$this->assertTrue(is_string($event->event_hashtag) || $event->event_hashtag === null);
				$this->assertTrue(is_string($event->event_href) || $event->event_href === null);
				$this->assertTrue($event->tz === '0' || $event->tz === null, "Event timezone [".$event->tz."] unrecognized");

				$this->assertTrue(is_numeric($event->event_start));
				$this->assertTrue(is_numeric($event->event_end));
				$this->assertTrue($event->event_cfp_start === null || is_numeric($event->event_cfp_start));
				$this->assertTrue($event->event_cfp_end   === null || is_numeric($event->event_cfp_end));
				$this->assertTrue(is_numeric($event->num_attend));
				$this->assertTrue(is_numeric($event->num_comments));

				$this->assertTrue(
									$event->active === '1'
				);
				$this->assertTrue(
									$event->pending === '0'
				);
				$this->assertTrue(
									$event->event_voting === 'Y'
									|| $event->event_voting === '0'
									|| $event->event_voting === null
				);
				$this->assertTrue(
									$event->private === 'N'
									|| $event->private === null
				);
				$this->assertTrue(
									$event->allow_comments === '0'
				);
				$this->assertTrue(
									$event->user_attending === false
									|| $event->user_attending === true
				);
			}
		}

	}
