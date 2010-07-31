<?php
	require_once 'PHPUnit/Framework.php';
	 
	class ApiTestBase extends PHPUnit_Framework_TestCase {
		
		public function testTest() {
			$this->assertTrue(true);
		}

		protected function makeApiRequest( $type, $action, $params, $format=null, $creds=null, $useCache=true ) {
			// TODO pull this from config
			$url = "http://lorna.rivendell.local/api/".urlencode($type);

			$useCache = false;

			// $creds === false means don't use credentials
			// $creds === null  means use default credentials
			// $creds === array($user, $pass) otherwise (where pass is md5)
			// TODO pull this from config or make default user
			if ($creds === null) {
				$creds = array('kevin', '6228bd57c9a858eb305e0fd0694890f7');
			}

			$req = new StdClass();
			$req->request = new StdClass();

			if (is_array($creds)) {
				$req->request->auth = new StdClass();
				$req->request->auth->user = $creds[0];
				$req->request->auth->pass = $creds[1];
			}

			$req->request->action->type = $action;
			if (is_array($params)) {
				$req->request->action->data = new StdClass();
				foreach( $params as $k=>$v ) {
					$req->request->action->data->$k = $v;
				}
			}
			$payload = $this->encode_request($req, $format);

			$cache_filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.'joindin-test-'.md5($url.$payload);

			if ($useCache) {
				// Check for reading from cache
				if (file_exists($cache_filename) && is_readable($cache_filename)) {
					$cache_data = json_decode(file_get_contents($cache_filename));
					if (time() < $cache_data->expires) {
						return $cache_data->payload;
					}
				}
			}

			$request = new HttpRequest($url, HttpRequest::METH_POST);
			$request->addRawPostData($payload);
			if($format == 'xml') {
				$request->setHeaders(array('Content-Type'=>'text/xml'));
			} else {
				// json is the default
				$request->setHeaders(array('Content-Type'=>'application/json'));
			}
			$response = $request->send();

			if ($useCache) {
				$cache_data = json_encode( array('payload'=>$response->getBody(), 'expires'=>(time()+3600)) );
				file_put_contents($cache_filename, $cache_data);
				// chmod( $cache_filename, 0777 );
			}

			return $response->getBody();
		}

		protected function encode_request($req, $format) {
			if($format == 'xml') {
				// assembling XML badly
				$retval = '<request>
	<action type="' . $req->request->action->type . '" output="xml">';
				if($req->request->action->data) {
					foreach($req->request->action->data as $key => $value) {
						$retval .= '
		<' . htmlentities($key, ENT_QUOTES) . '>' . htmlentities($value, ENT_QUOTES) . '</' . htmlentities($key, ENT_QUOTES) . '>';
					}
				}
				$retval .= '
	</action>';
				if(isset($req->request->auth)) {
					$retval .= '
	<auth>
		<user>' . $req->request->auth->user . '</user>
		<pass>' . $req->request->auth->pass . '</pass>
	</auth>';
				}
				$retval .= '
</request>';
				return $retval;
			} else {
				// json is the default
				return json_encode($req);
			}
			// shouldn't be here
			return false;
		}

		protected function decode_response($response, $format) {
			if($format == 'xml') {
				return simplexml_load_string($response);
			} else {
				// json is the default
				return json_decode($response);
			}
		}

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
				$this->assertTrue((isset($talk->comment_count) && is_numeric((string)$talk->comment_count)) 
						|| (isset($talk->ccount) && is_numeric((string)$talk->ccount)));
				$this->assertIsASessionType($this->optionallyConvertSimpleXML($talk->tcid), "Expected valid category for " . $talk->talk_title . " (" . $talk->ID . ")");
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

		/**
		 * assertLooksLikeAString: to handle the fact that SimpleXMLElements have all their
		 * child elements as SimpleXMLElements as well.  Just casting seems a bit silly, if
		 * we then test it is a string
		 * 
		 * @param mixed $value Variable to check type of
		 * @param string $message Error message
		 */
		protected function assertLooksLikeAString($value, $message=null) {
			if($value instanceOf SimpleXMLElement) {
				$this->assertEquals(1, $value->count(), $message);
			} else {
				$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $value, $message);
			}
		}

		protected function assertLooksLikeAStringOrNull($value, $message=null) {
			if ($value === null) {
				return;
			}
			$this->assertLooksLikeAString($value, $message);
		}

		protected function optionallyConvertSimpleXML($value) {
			if($value instanceOf SimpleXMLElement) {
				$retval = sprintf('%s', $value);
				if(strlen($retval) == '') {
					// WARNING: may go badly if the string should have existed and been empty?
					// FAIL: seems like we get here if we're false as well
					return null;
				}
				return $retval;
			} 
			return  $value;
		}

		protected function assertIsASessionType($type, $message = null) {
			$this->assertTrue($type === 'Talk' || $type === 'Workshop' || $type === 'Keynote' 
						|| $type === 'Social Event' || $type === 'Event Related', 
						$message
			);
		}

		/**
		 * Check speaker structure
		 * This will change as speaker functionality is improved
		 *
		 * @param StdClass $speaker The object containing the speaker info
		 * @param string   $message The error message to return (contains info about calling context)
		 */

		protected function assertIsASpeaker($speaker, $message = null) {
			$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, (string)$speaker->speaker_name, $message);
		}

		protected function assertIsATrack($track, $message = null) {
			$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, (string)$track->track_name, $message . ' - field: track_name');
			$this->assertTrue(is_numeric((string)$track->ID), $message . "(field: ID) " . (string)$track->ID);
			$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, (string)$track->track_desc, $message . ' - field: track_desc');
		}



	}
