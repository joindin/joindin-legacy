<?php
// Set these:
define('URL', 'http://joindin.localhost/api/');
define('USERNAME', 'imaadmin');
define('PASSWORD', 'password');

// Some settings:
define('OUTPUT', 'xml'); // json or xml
define('DISPLAY_RAW_RECEIVED_MSG', false); // true or false


// Do the work!
main($argc, $argv);
exit;

// ===================================================================
function help()
{
		echo <<<EOT

Joind.in API v1 simple tester
Usage:
	php apiv1_tester.php {type} {command} [{options}]

types & commands available:

	* site status [{test string}]

	* user getdetail {uid}  where {uid} may be a username or user id
	* user validate {uid} {password} where {uid} may be a username or user id
	* user getcomments {username}

	* event getdetail {event_id}
	* event gettalks {event_id}
	* event getlist upcoming
	* event getlist hot
	* event gettalkcomments {event_id}
	* event addcomment {event_id} {comment}

	* talk getdetail {talk_id}
	* talk getcomments {talk_id}
	* talk addcomment {talk_id} {rating} {comment} [{private=0}] [{user_id=2}]
	* talk claim {talk_id}

	* comment isspam {commend_id} {talk_id} [{rtype=talk}]

EOT;
}

// ===================================================================
function talk($command, $args)
{
	echo "*** Talk: $command ***\n";
	switch ($command){
		case 'getdetail':
			$talkId = getarg($args, 0);
			$result = call_api('talk', 'getdetail', array("talk_id"=>$talkId));
			break;

		case 'getcomments':
			$talkId = getarg($args, 0);
			$result = call_api('talk', 'getcomments', array("talk_id"=>$talkId));
			break;

		case 'addcomment':
			$talkId = getarg($args, 0);
			$rating = getarg($args, 1);
			$comment = getarg($args, 2);
			$private = getarg($args, 3, 0);
			$userId = getarg($args, 3, 2);

			if (!$comment) {
				echo "ERROR: No comment supplied\n";
				exit(2);
			}

			$result = call_api('talk', 'addcomment', array(
					"talk_id"=>$talkId, "rating"=>$rating, "comment"=>$comment,
					"private"=>$private, 'user_id'=>$userId, 'source'=>'apiv1_tester'));
			break;

		case 'claim':
			$talkId = getarg($args, 0);
			$result = call_api('talk', 'getcomments', array("talk_id"=>$talkId));
			break;

		default:
			echo "ERROR: invalid command\n";
			return;
	}
}


// ===================================================================
function comment($command, $args)
{
	switch ($command){
		case 'isspam':
			$commentId = getarg($args, 0);
			$talkId = getarg($args, 1);
			$rtype = getarg($args, 2, 'talk');
			$result = call_api('comment', 'isspam', array(
				"cid"=>$commentId, "tid"=>$talkId, "rtype"=>$rtype));
			break;

		default:
			echo "ERROR: invalid command\n";
			return;
	}
}

// ===================================================================
function event($command, $args)
{
	switch ($command){
		case 'getdetail':
			$eventId = getarg($args, 0);
			$result = call_api('event', 'getdetail', array("event_id"=>$eventId));
			break;

		case 'gettalks':
			$eventId = getarg($args, 0);
			$result = call_api('event', 'gettalks', array("event_id"=>$eventId));
			break;

		case 'getlist':
			$listType = getarg($args, 0);
			$result = call_api('event', 'getlist', array("event_type"=>$listType));
			break;

		case 'gettalkcomments':
			$eventId = getarg($args, 0);
			$result = call_api('event', 'gettalkcomments', array("event_id"=>$eventId));
			break;

		case 'addcomment':
			$eventId = getarg($args, 0);
			$comment = getarg($args, 1);

			if (!$comment) {
				echo "ERROR: No comment supplied\n";
				exit(2);
			}

			$result = call_api('event', 'addcomment', array(
					"event_id"=>$eventId, "comment"=>$comment));
			break;


		default:
			echo "ERROR: invalid command\n";
			return;
	}
}

// ===================================================================
function user($command, $args)
{
	switch ($command){
		case 'getdetail':
			$userId = getarg($args, 0);
			$result = call_api('user', 'getdetail', array("uid"=>$userId));
			break;

		case 'validate':
			$userId = getarg($args, 0);
			$password = md5(getarg($args, 1));
			$result = call_api('user', 'validate', array("uid"=>$userId, "pass"=>$password));
			break;

		case 'getcomments':
			$username = getarg($args, 0);
			$password = md5(getarg($args, 1));
			$result = call_api('user', 'getcomments', array("username"=>$username));
			break;

		default:
			echo "ERROR: invalid command\n";
			return;
	}
}

// ===================================================================
function site($command, $args)
{
	echo "*** Site: $command ***\n";
	switch ($command){

		case 'status':
			$string = getarg($args, 0, 'apiv1 test');
			$result = call_api('site', 'status', array("test_string"=>$string));
			break;

		default:
			echo "ERROR: invalid command\n";
			return;
	}
}


// ===================================================================
// get an argument from the $args array. If not there, then use $default
function getarg($args, $key, $default='')
{
	$value = $default;
	if (isset($args[$key])) {
		$value = $args[$key];
	}
	return $value;
}

// ===================================================================
function main($argc, $argv)
{
	if ($argc <= 2) {
		help();
		exit;
	}
	$scriptname = array_shift($argv);
	$type = array_shift($argv);
	$cmd = array_shift($argv);
	$args = $argv;

	if (function_exists($type)) {
		$result = $type($cmd, $args);
		if ($result) {
			exit(0);
		}
	} else {
		echo "ERROR: Do not understand '$cmd'" . PHP_EOL;
	}
	exit(1);
}


// ===================================================================
function call_api($type, $action, $params)
{
	$username = USERNAME;
	$password = md5(PASSWORD);
	$output = OUTPUT;

	$payload = <<<EOF
<request>
	<action type="ACTION" output="$output">
		PARAMS
	</action>
	<auth>
		<user>$username</user>
		<pass>$password</pass>
	</auth>
</request>

EOF;

	$payload = str_replace("ACTION", $action, $payload);
    $ptxt = '';
	foreach($params as $k=>$v) {
		$ptxt .= '<'.htmlspecialchars($k, ENT_QUOTES).'>'.htmlspecialchars($v, ENT_QUOTES).'</'.htmlspecialchars($k, ENT_QUOTES).'>';
	}
	$payload = str_replace("PARAMS", $ptxt, $payload);

	echo "MESSAGE SENT:\n";
	echo $payload;
	echo "\n";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, URL.urlencode($type));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
	$response = curl_exec($ch);

    echo "MESSAGE RECEIVED:\n";
    if ($response === false) {
		echo "ERROR!\n$response\n";
		exit(1);
    }
    if (DISPLAY_RAW_RECEIVED_MSG) {
	    echo $response . "\n";
	    echo "DECODED MESSAGE:\n";
	}

	$result = null;
	switch ($output) {
		case 'json':
			$result = json_decode($response, 1);
			break;

		case 'xml':
			// Note: we may not get XML back...
			if(substr($response, 0, 5) == "<?xml") {
				$result = json_decode(json_encode((array) simplexml_load_string($response)), 1);
			}
			break;
	}

	if ($result === null) {
		// didn't work
		echo $response;
	} else {
		print_r($result);
	}
	echo "\n";

	return;
}

