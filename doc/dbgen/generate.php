<?php
    // In case you run into memory limit issues, here's your fix, you WILL need it :)
    ini_set('memory_limit', '1024M');

// ================================================================================================
    // These are global defines. It's not pretty, but it allows easy modifications of the generator. They should
    // be added to the generator data.

    // Light: bare minimum. Not really useful for anything I guess
//    define("COUNT_USERS",            5);
//    define("COUNT_EVENTS",           10);
//    define("COUNT_EVENT_COMMENTS",   50);
//    define("COUNT_TRACKS",           25);
//    define("COUNT_TALKS",            20);
//    define("COUNT_TALK_COMMENTS",    50);

//    // Medium: enough for development purposes without stressing out your DB
    define("COUNT_USERS",            25);
    define("COUNT_EVENTS",           50);
    define("COUNT_EVENT_COMMENTS",  200);
    define("COUNT_TRACKS",           75);
    define("COUNT_TALKS",           200);
    define("COUNT_TALK_COMMENTS",   500);

//    // Heavy: simulates current production values I think
//    define("COUNT_USERS",            200);
//    define("COUNT_EVENTS",           500);
//    define("COUNT_EVENT_COMMENTS",  1000);
//    define("COUNT_TRACKS",           750);
//    define("COUNT_TALKS",           2000);
//    define("COUNT_TALK_COMMENTS",  10000);

    // Massive: simulates stressed out database and application. Nice to check the bottlenecks.
    // Don't run these settings without +512MB of php-mem, a fast system and a crucifix.
//    define("COUNT_USERS",            5000);
//    define("COUNT_EVENTS",          12500);
//    define("COUNT_EVENT_COMMENTS",  25000);
//    define("COUNT_TRACKS",          16000);
//    define("COUNT_TALKS",           50000);
//    define("COUNT_TALK_COMMENTS",  250000);



    // Comma separated: 1 day event, 2 day event, 3 day event, 5 day event.
    define("EVENT_DURATION", "50,75,90,100");

    // Does the event have a CFP?
    define("EVENT_HAS_CFP", 50);

    // Does the event take place in the future or in the past?
    define("EVENT_IN_FUTURE", 50);

    // Should we add the year to the title (eh: PyCon11 or PyCon2011 instead of PyCon?)
    define("EVENT_ADD_YEAR_TO_TITLE", 10);

    // 50% chance if the additional year added to the name is a full year (2011) instead of short year (11)
    define("EVENT_ADD_FULLYEAR_TO_TITLE", 50);

    // Percentage of anonymous comments on events
    define("EVENT_COMMENT_IS_ANONYMOUS", 15);

    // Percentage of anonymous comments on talks
    define("TALK_COMMENT_IS_ANONYMOUS", 15);

    // 5% of the comments are marked private
    define("COMMENT_IS_PRIVATE", 5);

    // Percentage of talks that is claimed
    define("TALK_IS_CLAIMED",75);

    // Does the talk have multiple speakers
    define("TALK_HAS_MULTIPLE_SPEAKERS", 20);

    // Percentage of the talks that have slides associated with them
    define("TALK_HAS_SLIDES", 35);

    // Amount of claims that are still pending
    define("TALK_SPEAKER_PENDING", 10);

    // Percentage of talks that is claimed by a user that has registered through joind.in
    define("TALK_IS_CLAIMED_BY_REGISTERED_USER", 75);

    // Only 1 percent of the users is an admin
    define("USER_IS_ADMIN", 1);

    // 25% has a twitter account
    define("USER_HAS_TWITTER", 25);


// ====================== NOTHING TO EDIT BELOW THIS POINT, MOVE ALONG ======================

    require_once "generator_data.interface.php";
    require_once "generator_data.class.php";
    require_once "generator.class.php";

    $gen = new Generator(new Generator_Data());
    echo $gen->generate();
    exit;


//
//
//// Generate $count talks for random events
//function generate_talks($count) {
//    global $_data;
//    global $categories;
//    global $languages;
//
//    $sql = "TRUNCATE talks;\n";
//    $sql .= "INSERT INTO talks (talk_title, speaker, slides_link, date_given, event_id, ID, talk_desc, active, owner_id, lang) VALUES \n";
//    $sqllines = array();
//
//    $_data['talks'] = array();
//
//    for ($id=1; $id!=$count; $id++) {
//        $event_idx = array_rand($_data['events']);
//
//        // Add a dampning for this talk. This is a 0-5 value (0 being the most used) that will give an overall
//        // view of the presentation. When a presentation wasn't good, it should reflect on the comments as well.
//        $dampning = floor(log(rand(1,256) / log(2)));
//        $_data['talks'][] = array('id' => $id, 'comment_damping' => $dampning);
//
//
//        $talk_title = genTalkTitle();
//        if (chance(TALK_HAS_SLIDES)) {
//            $slides_link = "http://slideshare.net/slidefromuser";
//        } else {
//            $slides_link = "";
//        }
//
//        $date_given = time() - rand(1000000, 10000000);
//
//        $event_id = $_data['events'][$event_idx]['id'];
//
//        $talk_desc = genLorum(50);
//
//        $lang_id = array_rand($languages);
//
//        $sqllines[] = sprintf ("('%s', NULL, '%s', %d, %d, %d, '%s', 1, NULL, %d)",
//                               $talk_title, $slides_link, $date_given, $event_id, $id, $talk_desc, $lang_id);
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    $sql .= "\n\n";
//
//
//
//    // Add categories to the talks
//    $sql .= "TRUNCATE talk_cat;\n";
//    $sql .= "INSERT INTO talk_cat (talk_id, cat_id, ID) VALUES \n";
//    $sqllines = array();
//
//    foreach($_data['talks'] as $talk) {
//        $cat_id = array_rand($categories);
//        $sqllines[] = sprintf("(%d, %d, NULL)", $talk['id'], $cat_id);
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    $sql .= "\n\n";
//
//
//    // Add speakers to the talks
//    $sql .= "TRUNCATE talk_speaker;\n";
//    $sql .= "INSERT INTO talk_speaker (talk_id, speaker_name, ID, speaker_id, status) VALUES \n";
//    $sqllines = array();
//
//    foreach($_data['talks'] as $talk) {
//        $talk_id = $talk['id'];
//
//        // Check if we need multiple speakers or not
//        if (chance(TALK_HAS_MULTIPLE_SPEAKERS)) {
//            $speaker_count = rand(2, 4);
//        } else {
//            $speaker_count = 1;
//        }
//
//        for ($i=0; $i!=$speaker_count; $i++) {
//            if (chance(TALK_IS_CLAIMED_BY_REGISTERED_USER)) {
//                $user_idx = array_rand($_data['users']);
//                $speaker_name = $_data['users'][$user_idx]['fullname'];
//                if (chance(TALK_IS_CLAIMED)) {
//                    $speaker_id = $_data['users'][$user_idx]['id'];
//                } else {
//                    $speaker_id = "NULL";
//                }
//            } else {
//                $user = genUser();
//                $speaker_name = $user['fullname'];
//                $speaker_id = "NULL";
//            }
//            $status = chance(TALK_SPEAKER_PENDING) ? "pending" : "";
//
//            $sqllines[] = sprintf("(%d, '%s', NULL, %s, '%s')", $talk_id, $speaker_name, $speaker_id, $status);
//        }
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    $sql .= "\n\n";
//
//    return $sql."\n\n";
//}
//
//// Generate $count tracks for random events
//function generate_tracks($count) {
//    global $_data;
//    global $track_colors;
//
//    $sql = "TRUNCATE event_track;\n";
//    $sql .= "INSERT INTO event_track (event_id, track_name, track_desc, ID, track_color) VALUES \n";
//    $sqllines = array();
//
//    for ($id=1; $id!=$count; $id++) {
//        $event_idx = array_rand($_data['events']);
//
//        if (! isset($_data['events'][$event_idx]['track_count'])) {
//            $track_id = 1;
//        } else {
//            $track_id = $_data['events'][$event_idx]['track_count'] + 1;
//        }
//        $_data['events'][$event_idx]['track_count'] = $track_id;
//
//        $track_name = "Track ".$track_id;
//        $track_desc = genLorum(5);
//        $track_color = $track_colors[array_rand($track_colors)];
//
//        $sqllines[] = sprintf ("(%d, '%s', '%s', %d, '%s')",
//                               $_data['events'][$event_idx]['id'], $track_name, $track_desc, $id, $track_color);
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
//// Generate $count comments for talks
//function generate_talk_comments($count) {
//    global $_data;
//    global $event_comments;
//
//    $sql = "TRUNCATE talk_comments;\n";
//    $sql .= "INSERT INTO talk_comments (talk_id, rating, comment, date_made, ID, private, active, user_id, comment_type, source) VALUES \n";
//    $sqllines = array();
//
//    for ($id=1; $id!=$count; $id++) {
//        $talk = $_data['talks'][array_rand($_data['talks'])];
//        $comment = genLorum();
//
//        // Exponential randomness, 0 will be the least given, 5 the most. Will take the dampnening factor of the talk
//        // into account
//        $rating = floor(log(rand(1, 1 << (3+$talk['comment_damping'])) / log(2)));
//
//        $private = chance("COMMENT_IS_PRIVATE") ? 1 : 0;
//        $source = $event_comments['source'][array_rand($event_comments['source'])];
//        $sqllines[] = sprintf ("(%d, %d, '%s', %d, %d, %d, %d, %d, NULL, '%s')",
//                               $talk['id'], $rating, $comment, (time()-rand(0,10000000)), $id, $private, 1, $id, $source);
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
//// Generate $count comments on events
//function generate_event_comments($count) {
//    global $_data;
//    global $event_comments;
//
//    $sql = "TRUNCATE event_comments;\n";
//    $sql .= "INSERT INTO event_comments (event_id, comment, date_made, user_id, active, ID, cname, comment_type, source) VALUES \n";
//    $sqllines = array();
//
//    for ($id=1; $id!=$count; $id++) {
//        $event = $_data['events'][array_rand($_data['events'])];
//        $comment = genLorum();
//        if (chance(EVENT_COMMENT_IS_ANONYMOUS)) {
//            $user = "NULL";
//            $cname = "NULL";
//        } else {
//            $user = $_data['users'][array_rand($_data['users'])];
//            $cname = "'".$user['fullname']."'";
//            $user = $user['id'];
//        }
//
//        $source = $event_comments['source'][array_rand($event_comments['source'])];
//        $sqllines[] = sprintf ("(%d, '%s', %d, %s, %d, %d, %s, NULL, '%s')",
//                               $event['id'], $comment, (time()-rand(0,10000000)), $user, 1, $id, $cname, $source);
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
//// Let randomly attend users to events
//function attend_users_to_events() {
//    global $_data;
//
//    // @data['users'] = all users (IDs)
//    // @data['events'] = all events (IDs)
//
//    $sql = "TRUNCATE user_attend;\n";
//    $sql .= "INSERT INTO user_attend (uid, eid) VALUES \n";
//    $sqllines = array();
//
//    foreach ($_data['users'] as $user) {
//        $attended_events = rand(0, count($_data['events']) / 2);
//        for ($i=0; $i!=$attended_events; $i++) {
//            $event = $_data['events'][array_rand($_data['events'])];
//            // @TODO: make sure we don't have the same UID at an EID
//            $sqllines[] = sprintf ("(%d, %d)", $user['id'], $event['id']);
//        }
//    }
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
//// Generate $count events
//function generate_events($count) {
//    global $events;
//    global $cities;
//    global $_data;
//
//    $sql = "TRUNCATE events;\n";
//    $sql .= "INSERT INTO `events` (`event_name`, `event_start`, `event_end`, `event_lat`, `event_long`, `ID`, `event_loc`, `event_desc`,
//    `active`, `event_stub`, `event_icon`, `pending`, `event_hashtag`, `event_href`, `event_cfp_start`, `event_cfp_end`,
//    `event_voting`, `private`, `event_tz_cont`, `event_tz_place`, `event_contact_name`, `event_contact_email`, `event_cfp_url`) VALUES\n";
//
//
//    $_data['events'] = array();
//
//    $sqllines = array();
//    for ($id=1; $id!=$count; $id++) {
//
//        $_data['events'][] = array('id' => $id);
//
//        $eventname =  $events['first'][array_rand($events['first'])] . $events['last'][array_rand($events['last'])];
//        if (chance(ADD_YEAR)) {
//            if (chance(ADD_YEAR_FULL)) {
//                $eventname .= date("y");
//            } else {
//                $eventname .= date("Y");
//            }
//        }
//
//        $config['event_duration'] = array(50, 75, 90, 100);
//        $duration = rand(0, 100);
//        if ($duration < $config['event_duration'][0]) {
//            // one day event
//            $duration_end = "+1 day";
//        } elseif ($duration < $config['event_duration'][1]) {
//            // 2 day event
//            $duration_end = "+2 days";
//        } elseif ($duration < $config['event_duration'][1]) {
//            // 3 day event
//            $duration_end = "+3 days";
//        } else {
//            // 5 day event
//            $duration_end = "+5 days";
//        }
//
//        $future = chance(FUTURE_EVENT);
//        if ($future) {
//            // Future event, so + time
//            $start = time() + rand(100000, 10000000);
//            $end = strtotime($duration_end, $start);
//        } else {
//            // Future event, so - time
//            $start = time() - rand(100000, 10000000);
//            $end = strtotime($duration_end, $start);
//        }
//
//
//        // Get random city
//        $city = $cities[array_rand($cities)];
//        $location = $city[0];
//        $lat = $city[1];
//        $long = $city[2];
//
//        $stub = soundex($eventname);
//        $url = str_replace(" ", "", "http://".strtolower($eventname).".example.org");
//        $hash = "#".$stub;
//        if (! $future && chance(HAS_CFP)) {
//            // @TODO: Add CFP
//            $cfp_start = 0;
//            $cfp_end = 0;
//        } else {
//            $cfp_start = 0;
//            $cfp_end = 0;
//        }
//
//        $desc = genLorum();
//        $icon = "";
//
//        $userdata = genUser();
//        $sqllines[] = sprintf ("('%s', %d, %d, %f, %f, %d, '%s', '%s',
//                            %d, '%s', '%s', %d, '%s', '%s', %d, %d,
//                            %d, %d, '%s', '%s', '%s', '%s', '%s')",
//                         $eventname, $start, $end, $lat, $long, $id, $location, $desc,
//                         1, $stub, $icon, 0, $hash, $url, $cfp_start, $cfp_end,
//                         0, 0, "Europe", "Amsterdam", $userdata['fullname'], $userdata['email'], $url."/cfp");
//    }
//
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
//// Generate $count users
//function generate_users($count) {
//    global $_data;
//    $sqllines = array();
//
//    $sql = "TRUNCATE user;\n";
//    $sql .= "INSERT INTO `user` (`username`, `password`, `email`, `last_login`, `ID`, `admin`, `full_name`, `active`, `twitter_username`, `request_code`) VALUES\n";
//
//    $_data['users'] = array();
//
//    for ($id=1; $id!=$count; $id++) {
//        $user = genUser();
//
//        $_data['users'][] = array('id' => $id, 'fullname' => $user['fullname']);
//
//        $password = md5($user['username']."pass");
//        $last_login = time() - rand(0, 1000000);
//        $admin = (chance(IS_ADMIN)) ? 1 : 0;
//        $active = 1;
//        $twitter = chance(HAS_TWITTER) ? "@".$user['username'] : "";
//
//        $sqllines[] = sprintf ("('%s', '%s', '%s', %d, %d, %d, '%s', %d, '%s', NULL)",
//                               $user['username'], $password, $user['email'], $last_login, $id, $admin, $user['fullname'], $active, $twitter);
//    }
//
//    $sql .= join(",\n", $sqllines);
//    $sql .= ";";
//    return $sql."\n\n";
//}
//
///**
// * Generate a user-object with randomized user info. Needed for multiple occasions, so moved away to a separate function
// *
// * Takes already generated users in account so they are not generated again
// *
// * @return array
// */
//function genUser() {
//    global $_users;
//    global $users;
//
//
//    $first = ucfirst(strtolower($users['first'][array_rand($users['first'])]));
//    $last = ucfirst(strtolower($users['last'][array_rand($users['last'])]));
//
//    // Set username, use <name>1, <name>2, <name>3 etc if the name already is inside the system
//    $postfix=0;
//    $user = array();
//    $user['username'] = strtolower($first[0].$last);
//    while (isset($_users[$user['username']])) {
//        $postfix++;
//        $user['username'] = $first[0].$last.$postfix;
//    }
//
//    $user['fullname'] = $first." ".$last;
//    $user['email'] = strtolower($first).".".strtolower($last)."@example.org";
//    return $user;
//}
//
//// Generate a talk title
//function genTalkTitle() {
//    global $talk_generator_data;
//
//    $talk = "";
//    $talk .= $talk_generator_data['a'][array_rand($talk_generator_data['a'])]. " ";
//    $talk .= $talk_generator_data['b'][array_rand($talk_generator_data['b'])]. " ";
//    $talk .= $talk_generator_data['c'][array_rand($talk_generator_data['c'])]. " ";
//    $talk .= $talk_generator_data['d'][array_rand($talk_generator_data['d'])]. " ";
//    $talk .= $talk_generator_data['e'][array_rand($talk_generator_data['e'])]. " ";
//
//    return $talk;
//}
//
///**
// * Returns true of $percentage percent of the time. For instance, when $percentage is 50, this will return true half
// * the time.
// *
// * @param $percentage (0-100)
// * @return bool
// */
//function chance($percentage) {
//    if (rand(0, 100) <= $percentage) return true;
//    return false;
//}
//
//
///**
// * Generate random Lorum Ipsum string of multiple sentences.
// *
// * @param int $max Maximum amount of sentences that can be generated
// * @return string
// */
//function genLorum($max = 15) {
//    global $lorum;
//
//    $ret = "";
//    $r = rand(1, $max);
//    for ($i=0; $i!=$r; $i++) {
//        $ret .= $lorum[array_rand($lorum)];
//    }
//    return $ret;
//}
//
//
//function add_languages() {
//    global $languages;
//
//    $sql = "TRUNCATE lang;\n";
//
//    foreach ($languages as $key => $lang ) {
//        $sql .= sprintf("insert into lang (lang_name, lang_abbr, id) values ('%s','%s', %d);\n", $lang['name'], $lang['abbr'], $key);
//    }
//
//    echo $sql."\n\n";
//}
//
//function add_categories() {
//    global $categories;
//
//    $sql = "TRUNCATE categories;\n";
//
//    foreach ($categories as $key => $cat ) {
//        $sql .= sprintf("insert into categories (cat_title, cat_desc, id) values ('%s','%s', %d);\n", $cat['title'], $cat['desc'], $key);;
//    }
//
//    echo $sql."\n\n";
//}

?>
