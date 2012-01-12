<?php

	require_once 'ApiEventTestBase.php';
	 
	class EventGetList extends ApiEventTestBase {

		public function testGetListUpcomingWithAuth() {
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'));

			$res = $this->decode_response($response, 'json');
			$this->assertTrue( $res !== null, "Could not decode JSON response");
			$this->assertExpectedEventFields($res);
		}


		public function testGetListUpcomingWithAuthXML() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'), 'xml');

			$res = $this->decode_response($response, 'xml');

			$this->assertTrue( $res !== false, "Could not decode XML response");
			$this->assertExpectedEventFields($res);
		}

		public function testGetListHotWithoutAuthXML() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'hot'), 'xml', false);

			$res = $this->decode_response($response, 'xml');

			$this->assertTrue( $res !== false, "Could not decode XML response");
			$this->assertExpectedEventFields($res);
		}

		public function testGetListHotWithoutAuthJSON() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'hot'), 'json', false);

			$res = $this->decode_response($response, 'json');

			$this->assertTrue( $res !== false, "Could not decode JSON response");
			$this->assertExpectedEventFields($res);
		}

		public function testGetListUpcomingWithoutAuthJSON() {
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'upcoming'), false);

			$res = $this->decode_response($response, 'json');
			$this->assertTrue( $res !== null, "Could not decode JSON response");
			$this->assertExpectedEventFields($res);
		}

		public function testGetListPastWithoutAuthXML() {
			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'past'), 'xml', false);

			$res = $this->decode_response($response, 'xml');

			$this->assertTrue( $res !== false, "Could not decode XML response");
			$this->assertExpectedEventFields($res);
		}

		public function testGetListPastWithoutAuthJSON() {

			$response = self::makeApiRequest('event', 'getlist', array('event_type'=>'past'), 'json', false);

			$res = $this->decode_response($response, 'json');

			$this->assertTrue( $res !== false, "Could not decode JSON response");
			$this->assertExpectedEventFields($res);
//			exit;
		}

	}
