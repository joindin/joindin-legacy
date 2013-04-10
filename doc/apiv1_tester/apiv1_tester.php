<?php
/**
 * Testing script for V1 Api. Allows calls to the api to be made via CLI.
 *
 * @category CLI
 * @package  Utility
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

// Set these:
define(
    'URL',
    getenv('JIAPIV1_URL') ? getenv('JIAPIV1_URL') : 'http://joindin.localhost/api/'
);
define(
    'USERNAME',
    getenv('JIAPIV1_USERNAME') ? getenv('JIAPIV1_USERNAME') : 'imaadmin'
);
define(
    'PASSWORD',
    getenv('JIAPIV1_PASSWORD') ? getenv('JIAPIV1_PASSWORD') : 'password'
);

// Some settings:
define('OUTPUT', 'xml'); // json or xml
define('DISPLAY_RAW_RECEIVED_MSG', false); // true or false


// Do the work!
main($argc, $argv);
exit;

/**
 * Displays information on how to use the tool.
 *
 * @return void
 */
function help()
{
    $username = USERNAME;

    echo <<<EOT

Joind.in API v1 simple tester
Usage:
	php apiv1_tester.php {type} {command} [{options}]

types & commands available:

	* site status [{test string}]

	* user getdetail {uid} where {uid} is user id
	* user validate {uid} {password} where {uid} may be a username or user id
	* user getcomments {username}

	* event getdetail {event_id}
	* event gettalks {event_id}
	* event getlist upcoming
	* event getlist hot
	* event gettalkcomments {event_id}
	* event getcomments {event_id}
	* event addcomment {event_id} {comment}
	* event deletecomment {event_id} {comment_id}
	* event addtrack {event_id} {track_name} {track_desc}
	* event updatetrack {event_id} {track_id} {track_name} {track_desc} {track_color}
	* event deletetrack {event_id} {track_id}
	* event addadmin {event_id} {username}
	* event rmadmin {event_id} {username}
	* event attend {event_id}

	* talk getdetail {talk_id}
	* talk getcomments {talk_id}
	* talk addcomment {talk_id} {rating} {comment} [{private=0}] [{user_id=2}]
	* talk claim {talk_id}

	* comment isspam {comment_id} {talk_id} [{rtype=talk}]
	* comment getdetail {comment_id} [talk|event]

Current username: $username
(Set environment variables JIAPIV1_USERNAME and JIAPIV1_PASSWORD to change)


EOT;
}

/**
 * Handler for calls to the talk API
 *
 * @param string $command Talk command to run
 * @param array  $args    Arguments for command
 *
 * @return void
 */
function talk($command, $args)
{
    echo "*** Talk: $command ***\n";
    switch ($command) {
    case 'getdetail':
        $talkId = getarg($args, 0);
        call_api('talk', 'getdetail', array("talk_id" => $talkId));
        break;

    case 'getcomments':
        $talkId = getarg($args, 0);
        call_api('talk', 'getcomments', array("talk_id" => $talkId));
        break;

    case 'addcomment':
        $talkId  = getarg($args, 0);
        $rating  = getarg($args, 1);
        $comment = getarg($args, 2);
        $private = getarg($args, 3, 0);
        $userId  = getarg($args, 3, 2);

        if (!$comment) {
            echo "ERROR: No comment supplied\n";
            exit(2);
        }

        call_api(
            'talk',
            'addcomment',
            array(
                 "talk_id" => $talkId,
                 "rating"  => $rating,
                 "comment" => $comment,
                 "private" => $private,
                 'user_id' => $userId,
                 'source'  => 'apiv1_tester'
            )
        );
        break;

    case 'claim':
        $talkId = getarg($args, 0);
        call_api('talk', 'getcomments', array("talk_id" => $talkId));
        break;

    default:
        echo "ERROR: invalid command\n";

        return;
    }
}

/**
 * Handler for calls to the comment API
 *
 * @param string $command Comment command to run
 * @param array  $args    Arguments for command
 *
 * @return void
 */
function comment($command, $args)
{
    switch ($command) {
    case 'isspam':
        $commentId = getarg($args, 0);
        $talkId    = getarg($args, 1);
        $rtype     = getarg($args, 2, 'talk');
        call_api(
            'comment',
            'isspam',
            array(
                 "cid"   => $commentId,
                 "tid"   => $talkId,
                 "rtype" => $rtype
            )
        );
        break;

    case 'getdetail':
        $commentId = getarg($args, 0);
        $rtype     = getarg($args, 1);
        call_api(
            'comment',
            'getdetail',
            array(
                 "cid"   => $commentId,
                 "rtype" => $rtype
            )
        );
        break;

    default:
        echo "ERROR: invalid command\n";

        return;
    }
}

/**
 * Handler for calls to the event API
 *
 * @param string $command Event command to run
 * @param array  $args    Arguments for command
 *
 * @return void
 */
function event($command, $args)
{
    switch ($command) {
    case 'getdetail':
        $eventId = getarg($args, 0);
        call_api('event', 'getdetail', array("event_id" => $eventId));
        break;

    case 'gettalks':
        $eventId = getarg($args, 0);
        call_api('event', 'gettalks', array("event_id" => $eventId));
        break;

    case 'getlist':
        $listType = getarg($args, 0);
        call_api('event', 'getlist', array("event_type" => $listType));
        break;

    case 'gettalkcomments':
        $eventId = getarg($args, 0);
        call_api('event', 'gettalkcomments', array("event_id" => $eventId));
        break;

    case 'getcomments':
        $eventId = getarg($args, 0);
        call_api('event', 'getcomments', array("event_id" => $eventId));
        break;

    case 'addcomment':
        $eventId = getarg($args, 0);
        $comment = getarg($args, 1);

        if (!$comment) {
            echo "ERROR: No comment supplied\n";
            exit(2);
        }

        call_api(
            'event',
            'addcomment',
            array(
                 "event_id" => $eventId,
                 "comment"  => $comment
            )
        );
        break;

    case 'deletecomment':
        $eventId   = getarg($args, 0);
        $commentId = getarg($args, 1);

        if (!$commentId) {
            echo "ERROR: No comment id supplied\n";
            exit(2);
        }

        call_api(
            'event',
            'deletecomment',
            array(
                 "eid" => $eventId,
                 "cid" => $commentId
            )
        );
        break;

    case 'addtrack':
        $eventId   = getarg($args, 0);
        $trackName = getarg($args, 1);
        $trackDesc = getarg($args, 2);

        if (!$trackDesc) {
            echo "ERROR: No track desc supplied\n";
            exit(2);
        }

        call_api(
            'event',
            'addtrack',
            array(
                 "event_id"   => $eventId,
                 'track_name' => $trackName,
                 'track_desc' => $trackDesc
            )
        );
        break;

    case 'updatetrack':
        $eventId    = getarg($args, 0);
        $trackId    = getarg($args, 1);
        $trackName  = getarg($args, 2);
        $trackDesc  = getarg($args, 3);
        $trackColor = getarg($args, 4);

        if (!$trackDesc) {
            echo "ERROR: No track color supplied\n";
            exit(2);
        }

        call_api(
            'event',
            'updatetrack',
            array(
                 "event_id"    => $eventId,
                 'track_id'    => $trackId,
                 'track_name'  => $trackName,
                 'track_desc'  => $trackDesc,
                 'track_color' => $trackColor
            )
        );
        break;

    case 'deletetrack':
        $eventId = getarg($args, 0);
        $trackId = getarg($args, 1);

        if (!$trackId) {
            echo "ERROR: No track color supplied\n";
            exit(2);
        }

        $result = call_api(
            'event', 'deletetrack', array(
                                         "event_id" => $eventId,
                                         'track_id' => $trackId
                                    )
        );
        break;

    case 'addadmin':
        $eventId  = getarg($args, 0);
        $username = getarg($args, 1);

        if (!$username) {
            echo "ERROR: No username supplied\n";
            exit(2);
        }

        call_api(
            'event', 'addadmin', array(
                                      "eid"      => $eventId,
                                      'username' => $username
                                 )
        );
        break;

    case 'rmadmin':
        $eventId  = getarg($args, 0);
        $username = getarg($args, 1);

        if (!$username) {
            echo "ERROR: No username supplied\n";
            exit(2);
        }

        call_api(
            'event', 'rmadmin', array(
                                     "eid"      => $eventId,
                                     'username' => $username
                                )
        );
        break;

    case 'attend':
        $eventId = getarg($args, 0);

        if (!$eventId) {
            echo "ERROR: No event id supplied\n";
            exit(2);
        }

        call_api('event', 'attend', array("eid" => $eventId));
        break;

    default:
        echo "ERROR: invalid command\n";

        return;
    }
}

/**
 * Handler for calls to the user API
 *
 * @param string $command User command to run
 * @param array  $args    Arguments for command
 *
 * @return void
 */
function user($command, $args)
{
    switch ($command) {
    case 'getdetail':
        $userId = getarg($args, 0);
        call_api('user', 'getdetail', array("uid" => $userId));
        break;

    case 'validate':
        $userId   = getarg($args, 0);
        $password = md5(getarg($args, 1));
        $result   = call_api(
            'user', 'validate', array("uid" => $userId, "pass" => $password)
        );
        break;

    case 'getcomments':
        $username = getarg($args, 0);
        $password = md5(getarg($args, 1));
        $result   = call_api('user', 'getcomments', array("username" => $username));
        break;

    default:
        echo "ERROR: invalid command\n";

        return;
    }
}

/**
 * Handler for calls to the site API
 *
 * @param string $command Site command to run
 * @param array  $args    Arguments for command
 *
 * @return void
 */
function site($command, $args)
{
    echo "*** Site: $command ***\n";
    switch ($command) {

    case 'status':
        $string = getarg($args, 0, 'apiv1 test');
        call_api('site', 'status', array("test_string" => $string));
        break;

    default:
        echo "ERROR: invalid command\n";

        return;
    }
}

/**
 * Retrieves an argument from an array of arguments. If none is found at the
 * requested position, returns the default value
 *
 * @param array  $args    Argument array
 * @param string $key     Key to fetch
 * @param string $default Default to return if value is not found
 *
 * @return string
 */
function getarg($args, $key, $default = '')
{
    $value = $default;
    if (isset($args[$key])) {
        $value = $args[$key];
    }

    return $value;
}

/**
 * Gets the calls started by parsing out the values sent into the CLI script
 *
 * @param integer $argc Number of arguments
 * @param array   $argv Argument values
 *
 * @return void
 */
function main($argc, $argv)
{
    if ($argc <= 2) {
        help();
        exit;
    }
    $scriptname = array_shift($argv);
    $type       = array_shift($argv);
    $cmd        = array_shift($argv);
    $args       = $argv;

    if (function_exists($type)) {
        $type($cmd, $args);
        if ($result) {
            exit(0);
        }
    } else {
        echo "ERROR: Do not understand '$cmd'" . PHP_EOL;
    }
    exit(1);
}

/**
 * Creates the API call XML structure and sends it to the API endpoint
 *
 * @param string $type   Type of API call to make
 * @param string $action Specific action to call
 * @param array  $params Parameters to send to API
 *
 * @return void
 */
function call_api($type, $action, $params)
{
    $username = USERNAME;
    $password = md5(PASSWORD);
    $output   = OUTPUT;

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
    $ptxt    = '';
    foreach ($params as $k => $v) {
        $ptxt .= '<' . htmlspecialchars($k, ENT_QUOTES) . '>' . htmlspecialchars(
            $v, ENT_QUOTES
        ) . '</' . htmlspecialchars($k, ENT_QUOTES) . '>';
    }
    $payload = str_replace("PARAMS", $ptxt, $payload);

    echo "MESSAGE SENT:\n";
    echo $payload;
    echo "\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, URL . urlencode($type));
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
        if (substr($response, 0, 5) == "<?xml") {
            $result = json_decode(
                json_encode(
                    (array)simplexml_load_string($response)
                ),
                1
            );
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

