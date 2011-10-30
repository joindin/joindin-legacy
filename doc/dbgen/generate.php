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

    // Percentage the even has a single user
    define("EVENT_ADMIN_SINGLE_USER", "75");

    // Percentage of the event-admins that are still being claimed
    define("EVENT_ADMIN_PENDING", 5);

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

?>
