<?php
	class ApiTestBase /*extends PHPUnit_Framework_TestCase*/ {
		
		public function testTest() {
			$this->assertTrue(true);
		}

		protected function makeApiRequest( $type, $action, $params, $format=null, $creds=null, $useCache=true ) {
			$config = load_class('Config');
            $url = $config->config['base_url'] . 'api/' . urlencode($type);

			$useCache = false;

			// $creds === false means don't use credentials
			// $creds === null  means use default credentials
			// $creds === array($user, $pass) otherwise (where pass is md5)
			// TODO pull this from config or make default user
			if ($creds === null) {
			    // TODO find a better solution here !!!
			    if (!array_key_exists('api_creds_user', $config->config)) {
			        $config->config['api_creds_user'] = 'kevin';
			    }
			    if (!array_key_exists('api_creds_token', $config->config)) {
			        $config->config['api_creds_token'] = '6228bd57c9a858eb305e0fd0694890f7';
			    }
				$creds = array(
				    $config->config['api_creds_user'], 
					$config->config['api_creds_token'],
				);
			}

			$req = new StdClass();
			$req->request = new StdClass();
			$req->request->action = new StdClass();

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
			$request->setBody($payload);
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
				$xml = simplexml_load_string($response);
				$xml = $this->handleSimpleXML($xml);
				return $xml;
			} else {
				// json is the default
				$json = json_decode($response);
				return $json;
			}
		}

		protected function handleSimpleXML(SimpleXMLElement $xml) {
			$new_xml = new stdClass();
			if(count($xml->children()) > 0 ) {

				
				foreach($xml->children() as $key => $child) {
					/*
					if(isset($new_xml->$key)) {
						if(!is_array($new_xml->$key)) {
							$new_xml->$key = array($new_xml->$key);
						}
						$new_xml->{$key}[] = $this->handleSimpleXML($child);
					*/
					if($key == 'item') { // special case
						if(!is_array($new_xml)) {
							$new_xml = array();
						}
						$new_xml[] = $this->handleSimpleXML($child);
					} else {
						$new_xml->$key = $this->handleSimpleXML($child);
					}
				}
			} else {
				$new_xml = (string)$xml;
			}
			return $new_xml;

		}

		protected function assertExpectedTalkFields($talks) {

			if(!empty($talks)) {
				foreach($talks as $talk) {
					$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $talk->talk_title);
					$this->assertIsASpeaker($talk->speaker, "Expected valid speaker info for " . $talk->talk_title . " (" . $talk->ID . ")");
					$this->assertLooksLikeAStringOrNull($talk->slides_link);
					$this->assertTrue(is_numeric((string)$talk->date_given));
					$this->assertTrue(is_numeric((string)$talk->event_id));
					$this->assertTrue(is_numeric((string)$talk->ID));
					$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $talk->talk_desc);
					$this->assertLooksLikeAStringOrNull($talk->event_tz_cont);
					$this->assertLooksLikeAStringOrNull($talk->event_tz_place);
					$this->assertTrue(is_numeric((string)$talk->event_start));
					$this->assertTrue(is_numeric((string)$talk->event_end));
					$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $talk->lang);
					$this->assertTrue((isset($talk->comment_count) && is_numeric((string)$talk->comment_count)) 
							|| (isset($talk->ccount) && is_numeric((string)$talk->ccount)));
					$this->assertIsASessionType($talk->tcid, "Expected valid category for " . $talk->talk_title . " (" . $talk->ID . ")");
					if(!empty($talk->tracks)) {
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
		}

		/**
		 * Wrapper function for when a string is optional
		 *
		 * Needed because codeigniter sometimes returns nulls where there's an empty string
		 *
		 * @param $value   string The value to check for null or string type
		 * @param $message string The message to pass through to the PHPUnit assertion
		 */
		protected function assertLooksLikeAStringOrNull($value, $message=null) {
			if ($value === null) {
				return;
			}
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $value, $message);
		}

		/**
		 * Check for valid session types
		 *
		 * @param string $type    The type to check for validity
		 * @param string $message The message to pass through to the PHPUnit assertion
		 */
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
		 * @param string $speaker Speaker name
		 * @param string $message The error message to return (contains info about calling context)
		 */

		protected function assertIsASpeaker($speaker, $message = null) {
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $speaker, $message);
		}

		/**
		 * Check for valid track structure
		 *
		 * @param stdClass $track   The track structure to evaluate
		 * @param string   $message The message to pass through to the PHPUnit assertion
		 */
		protected function assertIsATrack($track, $message = null) {
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, (string)$track->track_name, $message . ' - field: track_name');
			$this->assertTrue(is_numeric((string)$track->ID), $message . "(field: ID) " . (string)$track->ID);
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, (string)$track->track_desc, $message . ' - field: track_desc');
		}

	}
