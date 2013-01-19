<?php

require_once 'ApiTestBase.php';

class UserGetDetail extends ApiTestBase {
		public function assertExpectedFields($res) {
			$this->assertEquals(1, count($res), 'Only one matching user should be returned');

			foreach($res as $user) {
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $user);
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $user->username, "User name for user " . $user->ID . " should be a string");
				$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $user->full_name);
				$this->assertTrue(is_numeric((string)$user->ID));
				$this->assertTrue(is_numeric((string)$user->last_login));
			}
		}

		public function testGetDetailByUsernameJSON() {
			$response = self::makeApiRequest('user', 'getdetail', array('uid'=>'lornajane'), 'json');

			$res = $this->decode_response($response, 'json');
			$this->assertTrue( $res !== null, "Could not decode JSON response");
			$this->assertExpectedFields($res);
		}

		public function testGetDetailByUsernameXML() {
			$response = self::makeApiRequest('user', 'getdetail', array('uid'=>'lornajane'), 'xml');

			$res = $this->decode_response($response, 'xml');
			$this->assertTrue( $res !== null, "Could not decode XML response");
			$this->assertExpectedFields($res);
		}

		public function testGetDetailByUserIDXML() {
			$response = self::makeApiRequest('user', 'getdetail', array('uid'=>'110'), 'xml');

			$res = $this->decode_response($response, 'xml');
			$this->assertTrue( $res !== null, "Could not decode XML response");
			$this->assertExpectedFields($res);
		}

		public function testGetDetailByUserIDJSON() {
			$response = self::makeApiRequest('user', 'getdetail', array('uid'=>'110'), 'json');

			$res = $this->decode_response($response, 'json');
			$this->assertTrue( $res !== null, "Could not decode JSON response");
			$this->assertExpectedFields($res);
		}

}
