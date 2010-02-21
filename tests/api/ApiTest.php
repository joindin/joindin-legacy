<?php
	require_once 'PHPUnit/Framework.php';
	 
	class ApiTest extends PHPUnit_Framework_TestCase {
		
		protected function makeApiRequest( $type, $action, $params, $creds=null ) {
			// $creds === false means don't use credentials
			// $creds === null  means use default credentials
			// $creds === array($user, $pass) otherwise
			if ($creds === null) {
				$creds = array('kevin', '6228bd57c9a858eb305e0fd0694890f7');
			}

			$url = "http://joind.in/api/".urlencode($type);

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
			$request = new HttpRequest($url, HttpRequest::METH_POST);
			$request->addRawPostData($payload);
			$request->setHeaders(array('Content-Type'=>'application/json'));
			$response = $request->send();

			return $response;
		}

	}

