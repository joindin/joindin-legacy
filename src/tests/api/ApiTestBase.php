<?php
	require_once 'PHPUnit/Framework.php';
	 
	class ApiTestBase extends PHPUnit_Framework_TestCase {
		
		public function testTest() {
			$this->assertTrue(true);
		}

		protected function makeApiRequest( $type, $action, $params, $creds=null, $useCache=true ) {
			// $creds === false means don't use credentials
			// $creds === null  means use default credentials
			// $creds === array($user, $pass) otherwise
			if ($creds === null) {
				$creds = array('kevin', '6228bd57c9a858eb305e0fd0694890f7');
			}

			$url = "http://lorna.rivendell.local/api/".urlencode($type);

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
			$payload = json_encode($req);

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
			$request->setHeaders(array('Content-Type'=>'application/json'));
			$response = $request->send();

			if ($useCache) {
				$cache_data = json_encode( array('payload'=>$response->getBody(), 'expires'=>(time()+3600)) );
				file_put_contents($cache_filename, $cache_data);
			}

			return $response->getBody();
		}

	}

