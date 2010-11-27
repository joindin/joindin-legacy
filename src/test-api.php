<?php
	function call_api($type, $action, $params) {
		$payload = <<<EOF
<request>
	<action type="ACTION" output="json">
		PARAMS
	</action>
</request>

EOF;
		$payload = str_replace("ACTION", $action, $payload);
                $ptxt = "";
		foreach($params as $k=>$v) {
			$ptxt .= '<'.htmlspecialchars($k, ENT_QUOTES).'>'.htmlspecialchars($v, ENT_QUOTES).'</'.htmlspecialchars($k, ENT_QUOTES).'>';
		}
		$payload = str_replace("PARAMS", $ptxt, $payload);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://joindin.local.nl/api/".urlencode($type));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
		$response = curl_exec($ch);
        // echo $response; return; // for XML
		$result = json_decode($response);
		if ($result === null) {
			echo "ERROR!\n$response\n";
			return false;
		} else {
			return $result;
		}
	}

    echo "*** check status\n";
    $ret = call_api('site','status', array('test_string' => 'my special string'));
    var_dump($ret);