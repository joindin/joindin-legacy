<?php
require_once "generator_data.interface.php";


/**
 * We echo everything. This is because this class generates too much data that we can keep inside our memory.
 * The "caching" is pretty bad as well. When I'm running the "massive" parameters, the system uses 600M+ of memory.
 */

class Generator {
    protected $_data;       	// Configuration data
    protected $_cache;      	// Caching of data
    protected $_exiting_stubs; 	// Stubs that have already been generated, to stop duplicates.

    /**
     * @param Generator_Data_Interface $data
     */
    public function __construct(Generator_Data_Interface $data) {
        $this->_data = $data;
        $this->_existing_stubs = array();
    }

    /**
     * Does the actual generation
     * 
     * Will output the generated SQL. If you want to capture it (you shouldn't, it could be a lot of data),
     * use the output buffering.
     *
     * @return void
     */
    public function generate() {
        ob_implicit_flush(true);
        
        // Generate languages and categories (it's not really generated data)
        $this->_generateLanguages();
        $this->_generateCategories();

        // generate users
        $this->_generateUsers(COUNT_USERS);                       // Generate registered users

        // generate events and comments
        $this->_generateEvents(COUNT_EVENTS);
        $this->_generateEventComments(COUNT_EVENT_COMMENTS);      // random comment on random event

        // populate events with tracks, talks and comments
        $this->_generateTracks(COUNT_TRACKS);
        $this->_generateTalks(COUNT_TALKS);
        $this->_generateTalkComments(COUNT_TALK_COMMENTS);        // random comment on random talk

        // Connect users to events
        $this->_attachAdminUsersToEvents();                       // Iterates all events and randomly add admins
        $this->_attendUsersToEvents();                            // Iterates all events and randomly add users
    }

    /**
     * Returns current data
     *
     * @return Generator_Data_Interface
     */
    function getData() {
        return $this->_data;
    }

    /**
     * 
     * Returns a random object from the $tag namespace
     * @return mixed
     */
    protected function _cacheFetchRandom($tag) {
        $idx = array_rand($this->_cache[$tag]);
        return $this->_cache[$tag][$idx];
    }

    /**
     * Returns the complete $tag namespace
     * 
     * @param $tag
     * @return
     */
    protected function _cacheFetchTag($tag) {
       return $this->_cache[$tag];
    }
    
    /**
     * Find/Fetch an object based on the needle inside the $tag namespace
     * 
     * @param $tag
     * @param $property
     * @param $needle
     * @return array|null
     */
    protected function _cacheFetch($tag, $property, $needle) {
        if (! isset($this->_cache[$tag])) return null;

        foreach ($this->_cache[$tag] as $object) {
            if ($object->$property == $needle) return $object;
        }
        return null;
    }
    
    /**
     * Store an $object on index $id in the $tag namespace
     *
     * @param $tag
     * @param $id
     * @param $object
     * @return void
     */
    protected function _cacheStore($tag, $id, $object) {
        $this->_cache[$tag][$id] = $object;
    }

    /**
     * Generates the languages used
     *
     * @return string
     */
    protected function _generateLanguages() {
        echo "TRUNCATE lang;\n";
        foreach ($this->getData()->getLanguageData() as $key => $lang) {
            echo sprintf("insert into lang (lang_name, lang_abbr, id) values ('%s','%s', %d);\n", $lang['name'], $lang['abbr'], $key);
        }
        echo "\n\n";
    }

    /**
     * Generates the categories used
     * 
     * @return string
     */
    protected function _generateCategories() {
        echo "TRUNCATE categories;\n";
        foreach ($this->getData()->getCategoryData() as $key => $cat) {
            echo sprintf("insert into categories (cat_title, cat_desc, id) values ('%s','%s', %d);\n", $cat['title'], $cat['desc'], $key);;
        }
        echo "\n\n";
    }

    // Generate $count talks for random events
    protected function _generateTalks($count) {
        echo "TRUNCATE talks;\n";
        echo "INSERT INTO talks (talk_title, speaker, slides_link, date_given, event_id, ID, talk_desc, active, owner_id, lang) VALUES \n";

        $first = true;
        for ($id=1; $id!=$count+1; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "TALK: $id         (".(memory_get_usage(true)/1024)." Kb)        \r");
            
            $talk = new StdClass();
            $talk->id = $id;
            $talk->date_given = time() - rand(1000000, 10000000);

            // Add a dampening for this talk. This is a 0-5 value (0 being the most used) that will give an overall
            // view of the presentation. When a presentation wasn't good, it should reflect on the comments as well
            // because they should be lower than usual.
            $talk->dampening = floor(log(rand(1,256) / log(2)));

            // Store in cache
            $this->_cacheStore('talks', $id, $talk);

            // Fetch event to connect to this task
            $event = $this->_cacheFetchRandom('events');

            $talk->title = $this->_genTalkTitle();
            $talk->slides_link = $this->_chance(TALK_HAS_SLIDES) ? "http://slideshare.net/slidefromuser" : "";
            $talk->event_id = $event->id;
            $talk->description = $this->_genLorum(50);
            $talk->lang_id = array_rand($this->getData()->getLanguageData());

            if (! $first) echo ",\n";

            printf ("('%s', NULL, '%s', %d, %d, %d, '%s', 1, NULL, %d)",
                                   $talk->title, $talk->slides_link, $talk->date_given, $talk->event_id, $id, $talk->description, $talk->lang_id);

            $first = false;
        }
        echo ";";
        echo "\n\n";


        //
        // Add categories to the talks
        echo "TRUNCATE talk_cat;\n";
        echo "INSERT INTO talk_cat (talk_id, cat_id, ID) VALUES \n";
        $first = true;
        foreach($this->_cacheFetchTag('talks') as $talk) {
            $cat_id = array_rand($this->getData()->getCategoryData());

            if (! $first) echo ",\n";
            printf("(%d, %d, NULL)", $talk->id, $cat_id);

            $first = false;
        }
        echo ";";
        echo "\n\n";


        //
        // Add speakers to the talks
        echo "TRUNCATE talk_speaker;\n";
        echo "INSERT INTO talk_speaker (talk_id, speaker_name, ID, speaker_id, status) VALUES \n";

        $first = true;
        foreach($this->_cacheFetchTag('talks') as $talk) {
            // Check if we need multiple speakers or not
            $speaker_count = $this->_chance(TALK_HAS_MULTIPLE_SPEAKERS) ? rand(2, 4) : 1;

            for ($i=0; $i!=$speaker_count; $i++) {
                if ($this->_chance(TALK_IS_CLAIMED_BY_REGISTERED_USER)) {
                    $user = $this->_cacheFetchRandom('users');
                    $speaker_name = $user->fullname;
                    $speaker_id = $this->_chance(TALK_IS_CLAIMED) ? $user->id : "NULL";
                } else {
                    // Create an anonymous user
                    $user = $this->_genUser();
                    $speaker_name = $user->fullname;
                    $speaker_id = "NULL";
                }
                $status = $this->_chance(TALK_SPEAKER_PENDING) ? "pending" : "";

                if (! $first) echo ",\n";
                printf("(%d, '%s', NULL, %s, %s)", $talk->id, $speaker_name, $speaker_id, $status ? "'pending'" : "null");

                $first = false;
            }
        }
        echo ";";
        echo "\n\n";
    }

    // Generate $count tracks for random events
    protected function _generateTracks($count) {
        echo "TRUNCATE event_track;\n";
        echo "INSERT INTO event_track (event_id, track_name, track_desc, ID, track_color) VALUES \n";

        $first = true;
        for ($id=1; $id!=$count+1; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "TRACK: $id       (".(memory_get_usage(true)/1024)." Kb)        \r");
            $event = $this->_cacheFetchRandom('events');

            // Get the current track count for this event
            $track_id = isset($event->track_count) ? $event->track_count + 1 : 1;

            // Save the trackcount back into the event cache
            $event->track_count = $track_id;
            $this->_cacheStore('events', $event->id, $event);

            // Additional info
            $track_name = "Track ".$track_id;
            $track_desc = $this->_genLorum(5);

            $tmp = $this->getData()->getTrackColorData();
            $track_color = $tmp[array_rand($tmp)];

            if (! $first) echo ",\n";

            printf ("(%d, '%s', '%s', %d, '%s')",
                                   $event->id, $track_name, $track_desc, $id, $track_color);

            $first = false;
        }

        echo ";";
        echo "\n\n";
    }

    // Generate $count comments for talks
    protected function _generateTalkComments($count) {
        echo "TRUNCATE talk_comments;\n";
        echo "INSERT INTO talk_comments (talk_id, rating, comment, date_made, ID, private, active, user_id, comment_type, source) VALUES \n";

        $first = true;
        for ($id=1; $id!=$count+1; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "TALK COMMENT ID: $id       (".(memory_get_usage(true)/1024)." Kb)        \r");

            $talk = $this->_cacheFetchRandom('talks');

            // Don't comment on future talks
            if ($talk->date_given > time()) continue;

            $comment = $this->_genLorum();

            // Exponential randomness, 0 will be the least given, 5 the most. Will take the dampening
            // factor of the talk into account.
            $rating = floor(log(rand(1, 1 << (3+$talk->dampening)) / log(2)));

            $private = $this->_chance("COMMENT_IS_PRIVATE") ? 1 : 0;

            if ($this->_chance(TALK_COMMENT_IS_ANONYMOUS)) {
                $user_id = "NULL";
            } else {
                $user = $this->_cacheFetchRandom('users');
                $user_id = $user->id;
            }


            $tmp = $this->getData()->getCommentSourceData();
            $source = $tmp[array_rand($tmp)];

            if (! $first) echo ",\n";

            printf ("(%d, %d, '%s', %d, %d, %d, %d, %d, NULL, '%s')",
                                   $talk->id, $rating, $comment, (time()-rand(0,10000000)), $id, $private, 1, $user_id, $source);


            $first = false;
        }

        echo ";";
        echo "\n\n";
    }

    // Generate $count comments on events
    protected function _generateEventComments($count) {
        $first = true;
        $have_event_comments = false;
        for ($id=1; $id!=$count+1; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "EVENT COMMENT ID: $id       (".(memory_get_usage(true)/1024)." Kb)        \r");

            $event = $this->_cacheFetchRandom('events');

            // Don't comment on future events
            if ($event->start > time()) continue;


            if ($this->_chance(EVENT_COMMENT_IS_ANONYMOUS)) {
                $user_id = "NULL";
                $comment_name = "NULL";
            } else {
                $user = $this->_cacheFetchRandom('users');
                $comment_name = "'".$user->fullname."'";
                $user_id = $user->id;
            }

            $comment = $this->_genLorum();

            $tmp = $this->getData()->getCommentSourceData();
            $source = $tmp[array_rand($tmp)];

            if (! $first) {
                echo ",\n";
            } else {
                // It IS possible that we don't have any event comments at all. Therefore we can only push our SQL statements
                // when at least 1 comment is available (we know that for a fact at this point)
                echo "TRUNCATE event_comments;\n";
                echo "INSERT INTO event_comments (event_id, comment, date_made, user_id, active, ID, cname, comment_type, source) VALUES \n";
                $have_event_comments = true;
            }

            printf ("(%d, '%s', %d, %s, %d, %d, %s, NULL, '%s')",
                                   $event->id, $comment, (time()-rand(0,10000000)), $user_id, 1, $id, $comment_name, $source);

            $first = false;
        }

        if ($have_event_comments) {
            echo ";";
            echo "\n\n";
        }
    }

    // Let randomly attend users to events
    protected function _attendUsersToEvents() {
        echo "TRUNCATE user_attend;\n";
        echo "INSERT INTO user_attend (uid, eid) VALUES \n";

        // Fetch the number of events
        $events = $this->_cacheFetchTag('events');
        $event_count = count($events);

        $first = true;
        foreach ($this->_cacheFetchTag('users') as $user) {
            // The first user ALWAYS has an event. Otherwise we might end up with a empty
            // query. This is here to make sure that never can happen.
            $attended_events = rand($first ? 1 : 0, $event_count / 2);

            for ($i=0; $i!=$attended_events; $i++) {
                $event = $this->_cacheFetchRandom('events');

                if (! $first) echo ",\n";

                // @TODO: make sure we don't have the same UIDs at an EID
                printf ("(%d, %d)", $user->id, $event->id);

                $first = false;
            }
        }

        echo ";";
        echo "\n\n";
    }


    // Randomly attach user to events as admins
    protected function _attachAdminUsersToEvents() {
        echo "TRUNCATE user_admin;\n";
        echo "INSERT INTO user_admin (rid, uid, rtype, rcode) VALUES \n";

        $first = true;
        foreach ($this->_cacheFetchTag('events') as $event) {
            // A single or multi-user event
            if ($this->_chance(EVENT_ADMIN_SINGLE_USER)) {
                $admin_count = 1;
            } else {
                $admin_count = rand(1, (COUNT_USERS < 5 ? COUNT_USERS : 5));    // Maximum of five. It's hardcoded.
            }

            $userids = array();
            for ($i=0; $i!=$admin_count; $i++) {
                // Make sure we don't add the same user twice
                do {
                    $user = $this->_cacheFetchRandom('users');
                } while (in_array($user->id, $userids));
                $userids[] = $user->id;

                // Are we a pending event admin or not?
                $rcode = $this->_chance(EVENT_ADMIN_PENDING) ? "pending" : "";

                if (! $first) echo ",\n";
                $first = false;

                printf("(%d, %d, 'event', '%s')", $event->id, $user->id, $rcode);
            }
        }

        echo ";";
        echo "\n\n";
    }

    // Generate $count events
    protected function _generateEvents($count) {
        echo "TRUNCATE events;\n";
        echo "INSERT INTO `events` (`event_name`, `event_start`, `event_end`, `event_lat`, `event_long`, `ID`, `event_loc`, `event_desc`,
        `active`, `event_stub`, `event_icon`, `pending`, `event_hashtag`, `event_href`, `event_cfp_start`, `event_cfp_end`,
        `event_voting`, `private`, `event_tz_cont`, `event_tz_place`, `event_contact_name`, `event_contact_email`, `event_cfp_url`) VALUES\n";

        $first = true;
        for ($id=1; $id!=$count+1; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "EVENT ID: $id       (".(memory_get_usage(true)/1024)." Kb)        \r");


            $event = new StdClass();
            $event->id = $id;

            // Store this event in the cache
            $this->_cacheStore('events', $id, $event);


            // Fill the rest of the event
            $event->name = $this->_getEventTitle();

            // Find the duration
            $durations = explode(",", EVENT_DURATION);
            $percentage = rand(0, 100);
            if ($percentage < $durations[0]) {
                // one day event
                $duration_end = "+1 day";
            } elseif ($percentage < $durations[1]) {
                // 2 day event
                $duration_end = "+2 days";
            } elseif ($percentage < $durations[2]) {
                // 3 day event
                $duration_end = "+3 days";
            } else {
                // 5 day event
                $duration_end = "+5 days";
            }

            // Check if we need to be in the future
            $future = $this->_chance(EVENT_IN_FUTURE);
            if ($future) {
                // Future event, so + time
                $event->start = time() + rand(10000, 100000000);
                $event->end = strtotime($duration_end, $event->start);
            } else {
                // Past event, so - time
                $event->start = time() - rand(10000, 100000000);
                $event->end = strtotime($duration_end, $event->start);
            }

            // Get random city and add data
            $tmp = $this->getData()->getCityData();
            $city = $tmp[array_rand($tmp)];
            $event->location = $city[0];
            $event->lat = $city[1];
            $event->long = $city[2];

            // Global event info
            $stub = soundex($event->name);
            if (in_array($stub, $this->_existing_stubs)) {
                $stub = '';
            } else {
                $this->_existing_stubs[] = $stub;
            }
            $event->stub = $stub;
            $event->url = str_replace(" ", "", "http://".strtolower($event->name).".example.org");
            $event->hash = "#".$event->stub;
            $event->description = $this->_genLorum();
            $event->icon = "";

            // Call for papers
            if (! $future && $this->_chance(EVENT_HAS_CFP)) {
                // @TODO: Add CFP
                $event->cfp_start = null;
                $event->cfp_end = null;
            } else {
                $event->cfp_start = null;
                $event->cfp_end = null;
            }

            if (! $first) echo ",\n";

            printf ("('%s', %d, %d, %f, %f, %d, '%s', '%s', %d, '%s', '%s', %d, '%s', '%s', %s, %s, %d, %d, '%s', '%s', '%s', '%s', '%s')",
                             $event->name, $event->start, $event->end, $event->lat, $event->long, $id, $event->location, $event->description,
                             1, $event->stub, $event->icon, 0, $event->hash, $event->url, 
                             $event->cfp_start ? $event->cfp_start : 'null', 
                             $event->cfp_end ? $event->cfp_end : 'null',
                             0, 0, "Europe", "Amsterdam", "", "", $event->url."/cfp");

            $first = false;
        }

        echo ";";
        echo "\n\n";
    }

    // Generate $count users
    protected function _generateUsers($count) {
        echo "TRUNCATE user;\n";
        echo "INSERT INTO `user` (`username`, `password`, `email`, `last_login`, `ID`, `admin`, `full_name`, `active`, `twitter_username`, `request_code`) VALUES\n";
        echo "('imaadmin', '5f4dcc3b5aa765d61d8327deb882cf99', 'ima@sampledomain.com', unix_timestamp(), 1, 1, 'Ima Admin', 1, '', NULL)";

        for ($id=2; $id <= $count+2; $id++) {
            if ($id % 100 == 0) fwrite(STDERR, "USER ID: $id    (".(memory_get_usage(true)/1024)." Kb)        \r");

            // Generate and store user
            $user = $this->_genUser();
            $user->id = $id;
            $this->_cacheStore('users', $id, $user);

            // Generate additional information
            $user->password = md5($user->username."pass");
            $user->last_login = time() - rand(0, 1000000);
            $user->admin = ($this->_chance(USER_IS_ADMIN)) ? 1 : 0;
            $user->active = 1;
            $user->twitter = $this->_chance(USER_HAS_TWITTER) ? "@".$user->username : "";

            echo ",\n";
            printf ("('%s', '%s', '%s', %d, %d, %d, '%s', %d, '%s', NULL)",
                                   $user->username, $user->password, $user->email, $user->last_login, $user->id, $user->admin, $user->fullname, $user->active, $user->twitter);
        }

        echo ";";
        echo "\n\n";
    }

    /**
     * Generate a user-object with randomized user info. Needed for multiple occasions, so moved away to a separate function
     *
     * Takes already generated users in account so they are not generated again
     *
     * @return stdClass
     */
    protected function _genUser() {
        $data = $this->getData()->getUserGeneratorData();

        // Generate a user until we get one that isn't in the system yet
        do {
            // Fetch first and last name
            $first = ucfirst(strtolower($data->first[array_rand($data->first)]));
            $last = ucfirst(strtolower($data->last[array_rand($data->last)]));

            // Populate user object
            $user = new StdClass();
            $user->username = strtolower($first[0].$last);
            $user->fullname = $first . " " . $last;
            $user->email = strtolower($first).".".strtolower($last)."@example.org";
        } while ($this->_cacheFetch('user', "username", $user->username));

        return $user;
    }

    /**
     * Generate a event title
     * 
     * @return string
     */
    protected function _getEventTitle() {
        // Generate something like PyCon or PHPday, LinuxUUG etc
        $tmp = $this->getData()->getEventTitleGeneratorData();
        $name = $tmp->firstpart[array_rand($tmp->firstpart)] .
                $tmp->lastpart[array_rand($tmp->lastpart)];

        // Should we append the year?
        if ($this->_chance(EVENT_ADD_YEAR_TO_TITLE)) {
            // And should we add a full year (PyCon2011)
            if ($this->_chance(EVENT_ADD_FULLYEAR_TO_TITLE)) {
                // @TODO: We always add the current yet, it should reflect the year of the actual event date (but this
                // info is not available here yet.
                $name .= date("Y");
            } else {
                // Or just the last 2 digits of the year (PyCon11)
                $name .= date("y");
            }
        }

        return $name;
    }

    /***
     * Generates a talk title from some random (but fitting) words.
     * 
     * @return string
     */
    function _genTalkTitle() {
        $data = $this->getData()->getTalkTitleGeneratorData();

        $title = "";
        $title .= $data->a[array_rand($data->a)]. " ";  //  a-e is because of lack of imagination
        $title .= $data->b[array_rand($data->b)]. " ";
        $title .= $data->c[array_rand($data->c)]. " ";
        $title .= $data->d[array_rand($data->d)]. " ";
        $title .= $data->e[array_rand($data->e)];
        return $title;
    }

    /**
     * Returns true of $percentage percent of the time. For instance, when $percentage is 50, this will return true half
     * the time.
     *
     * @param $percentage (0-100)
     * @return bool
     */
    function _chance($percentage) {
        if (rand(0, 100) <= $percentage) return true;
        return false;
    }

    /**
     * Generate random Lorum Ipsum string of multiple sentences.
     *
     * @param int $max Maximum amount of sentences that can be generated
     * @return string
     */
    function _genLorum($max = 15) {
        $lorum = $this->getData()->getDescriptionGeneratorData();

        $number = rand(1, $max);
        for ($i=0, $ret="", $r=$number; $i!=$r; $i++) {
            $ret .= $lorum[array_rand($lorum)];

            if ($number > 10 && $i == (int)($number/2)) {
                $ret .= "\\n\\n";
            }
        }
        return trim($ret);
    }

} // End class

 ?>
