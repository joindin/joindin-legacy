<?php
	require_once 'PHPUnit/Framework.php';
	 
	class ApiTestBase extends PHPUnit_Framework_TestCase {
		
		public function testTest() {
			$this->assertTrue(true);
		}

		protected function makeApiRequest( $type, $action, $params, $format=null, $creds=null, $useCache=true ) {
			// TODO pull this from config
			$url = "http://lorna.rivendell.local/api/".urlencode($type);

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
				if($req->request->auth) {
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

	}

