<?php

	require_once 'ApiTestBase.php';
	 
	class TalkGetDetail extends ApiTestBase {

		public function testGetDetailJSON() {
			$talks_response = self::makeApiRequest('talk', 'getdetail', array('talk_id'=>2337), 'json');
			$talks = $this->decode_response($talks_response, 'json');

			$this->assertExpectedTalkFields($talks);
		}
		
		public function testGetDetailXML() {
			$talks_response = self::makeApiRequest('talk', 'getdetail', array('talk_id'=>2337), 'xml');
			$talks = $this->decode_response($talks_response, 'xml');

			$this->assertExpectedTalkFields($talks);
		}
		
	}
