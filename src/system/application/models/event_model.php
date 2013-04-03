<?php
/**
 * Joind.in event model
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Joind.in event model class
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */

class Event_model extends Model
{
    /**
     * Constructor does nothing other than call parent constructor
     *
     */
    function Event_model()
    {
        parent::Model();
    }

    /**
     * Match all data given against the events table to see if there's anything
     * matching
     *
     * @param mixed $data Data to be checked
     *
     * @return bool
     */
    function isUnique($data)
    {
        $q = $this->db->get_where('events', $data);
        $ret = $q->result();
        return (empty($ret)) ? true : false;
    }

    /**
     * Check the given string to see if it already exists
     * $pid is an optional event ID
     *
     * @param string $str String to be checked
     * @param mixed  $eid Optional event ID
     *
     * @return bool
     * 
     */
    function isUniqueStub($str, $eid=null)
    {
        $this->db->select('ID')
            ->from('events')
            ->where('event_stub', $str);
        if ($eid) {
            $this->db->where('ID !=', $eid);
        }
        
        $q   = $this->db->get();
        $ret = $q->result();
        return (empty($ret)) ? true : false;
    }

    //---------------------

    /**
     * Delete event
     *
     * @param mixed $id ID of the event to be deleted
     *
     * @return void
     */
    function deleteEvent($id)
    {
        
        // No mercy!
        $this->db->delete('events', array('ID'=>$id));
        
        $this->deleteEventTalks($id);
        $this->deleteTalkComments($id);
    }

    /**
     * Remove the talks related to an event ID
     *
     * Does not delete records, but sets active=0
     *
     * @param mixed $eid ID of the event
     *
     * @return void
     */
    function deleteEventTalks($eid)
    {
        $this->db->where('event_id', $eid);
        $this->db->update('talks', array('active'=>0));
    }

    /**
     * Remove the comments related to all of the talks on an event
     *
     * Does not delete records, but sets active=0
     * (useful for cleanup)
     *
     * @param mixed $eid Event ID
     *
     * @return void
     */
    function deleteTalkComments($eid)
    {
        $talks=$this->getEventTalks($eid);
        foreach ($talks as $value) {
            $this->db->where('talk_id', $value->ID);
            $this->db->update('talk_comments', array('active'=>0));
        }
    }
    //---------------------
    
    /**
     * Sets the Active and Pending statuses to make the event show correctly
     *
     * @param mixed $id ID to be used in the where clause
     *
     * @return void
     */
    function approvePendingEvent($id)
    {
        $arr=array(
            'active'    => 1,
            'pending'    => 0
        );
        $this->db->where('ID', $id);
        $this->db->update('events', $arr);
    }
    
    /**
     * Returns the details for a specific event, a series within a given date range,
     * or all events if no arguments have been provided.
     *
     * @param integer $id       ID of the event if wanting to fetch a single event
     * @param integer $start_dt If requesting events in a date range, this is the
     *                          lower date value (events later or equal to this date 
     *                          will be returned)
     * @param integer $end_dt   If requesting events in a date range, this is the
     *                          upper date value (events earlier or equal to this
     *                          date will be returned)
     * @param bool    $pending  Show only pending events, or only active
     *
     * @return stdClass[]
     */
    function getEventDetail($id = null, $start_dt = null, $end_dt = null,
        $pending = false
    ) {
        $this->load->helper("events");

        // get the current date (with out time)
        $now                     = mktime(0, 0, 0);
        $day_in_seconds          = 86400;
        $closing_days_in_seconds = 90 * $day_in_seconds;

        /** @var CI_DB_active_record $db  */
        $db = $this->db;

        // select all events, return whether they are allowed to comment (start -1
        // days till start + 90 days) and count attendees and comments
        $db->select(
            <<<SQL
            events.*,
            if (
                (
                    ((events.event_start - $day_in_seconds) < $now)
                    AND ((events.event_start + $closing_days_in_seconds) > $now)
                ),
            1, 0) AS allow_comments,
            COUNT(DISTINCT user_attend.ID) AS num_attend,
            COUNT(DISTINCT event_comments.ID) AS num_comments
SQL
            , false
        )
            ->from('events')
            ->join('user_attend', 'user_attend.eid=events.ID', 'left')
            ->join('event_comments', 'event_comments.event_id=events.ID', 'left')
            ->group_by('events.ID');

        // for a specific event, site admins always see it - for everyone else, or
        // for the list, observe the pending flags
        if ($this->user_model->isSiteAdmin() && isset($id)) {
            // just show it, no more filtering
        } else {
            if ($pending) {
                // pending events only
                $db->where('(events.active', 0)
                    ->where('events.pending', 1)
                    ->ar_where[] = ')';
            } else {
                $db->where('(events.active', 1)
                    ->where('(events.pending', null)
                    ->or_where('events.pending', 0)
                    ->ar_where[] = '))';
            }
        }

        // determine the selection criteria, if $id is a number use that, otherwise
        // limit based on start and end date
        if (is_numeric($id)) {
            $db->where('events.ID', $id);
        } elseif (($end_dt !== null) && ($start_dt !== null)) {
            // check whether the event start and end overlaps with the given date
            // range
            $db->where('events.event_start <=', $end_dt);
            $db->where('events.event_end >=', $start_dt);
            $db->order_by('events.event_start', 'DESC');
        }

        // retrieve the resultset
        $q   = $db->get();
        $res = $q->result();

        // Decorate results with "event is on now" flag
        if (is_array($res)) {
            foreach ($res as &$event) {
                if (!is_object($event)) {
                    continue;
                }

                $event->now = (event_isNowOn(
                    $event->event_start, $event->event_end
                )) ? "now" : "";
                $event->timezoneString = $event->event_tz_cont . '/' . 
                    $event->event_tz_place;
            }
        }

        return $res;
    }

    /**
     * Method to retrieve talks for a  given event
     *
     * @param mixed $id                  ID to be used in the where clause to match
     *                                   the event_id column
     * @param mixed $includeEventRelated If false, will add the condition 
     *                                   categories.cat_title <> "Event Related"
     *                                   Default = true
     * @param mixed $includePrivate      If false will not include talks related to 
     *                                   events with private field that is not NULL
     *                                   Default = false
     *
     * @return array
     */
    function getEventTalks($id, $includeEventRelated = true, $includePrivate = false)
    {
        $this->load->helper("events");
        $this->load->helper("talk");

        $private=($includePrivate) ? '' : ' and ifnull(private,0)!=1';
        $sql='
            select
                talks.talk_title,
                talks.speaker,
                talks.slides_link,
                talks.date_given,
                talks.duration,
                talks.event_id,
                talks.ID,
                talks.talk_desc,
                events.event_tz_cont,
                events.event_tz_place,
                events.event_start,
                events.event_end,
                (select l.lang_abbr from lang l where talks.lang=l.ID) lang,
                get_talk_rating(talks.ID) as rank,
                (select count(rating) from talk_comments where talk_id=talks.ID ' .
                    $private . ') comment_count,
                ifnull(categories.cat_title, \'Talk\') tcid
            from
                talks
            inner join lang on (lang.ID = talks.lang)
            inner join events on events.ID = talks.event_id
            left join talk_cat on talks.ID = talk_cat.talk_id
            left join categories on talk_cat.cat_id = categories.ID
            where
                ';
        if (!$includeEventRelated) {
            $sql .= 'categories.cat_title <> "Event Related" and
            ';
        }
        $sql .= sprintf(
            '
                event_id=%s and
                talks.active=1
            order by
                talks.date_given asc, talks.speaker asc
            ', $this->db->escape($id)
        );
        $q=$this->db->query($sql);
        $res = $q->result();

        // Loop through the talks deciding if they are currently on
        if (is_array($res) && count($res) > 0 && is_object($res[0])
            && event_isNowOn($res[0]->event_start, $res[0]->event_end)
        ) {
            $res = talk_listDecorateNowNext($res);
        }
        
        $CI=&get_instance();
        $CI->load->model('talk_speaker_model', 'tsm');
        foreach ($res as $k=>$talk) {
            $res[$k]->speaker=$CI->tsm->getTalkSpeakers($talk->ID);
        }

        return $res;
    }

    /**
     * Convenience method for retrieving just hot, upcoming or past events
     *
     * @param mixed   $type  Used to order & filter results
     *                       Can be 'hot', 'upcoming' or 'past' to be used
     * @param integer $limit Limit on results (default = null)
     *
     * @return array Event details
     */
    function getEventsOfType($type, $limit = null)
    {
        $where = null;
        $order_by = null;

        if ($type == "hot") {
            // if you change this, change the API too please
            $order_by = "score - ((num_comments + num_attend + 1) / 5)";
        }

        if ($type == "upcoming") {
            $order_by = "events.event_start asc";
            $where = '(events.event_start>='. (mktime(0, 0, 0) - (3 * 86400)).')';
        }

        if ($type == "past") {
            $where = '(events.event_end < '.mktime(0, 0, 0).')';
            $order_by = "events.event_start desc";
        }

        $result = $this->getEvents($where, $order_by, $limit);
        return $result;
    }

    /**
     * Get a current list of events
     *
     * @param string  $where    [optional] Optional "where" clause
     * @param string  $order_by Order by field
     * @param integer $limit    Limit on results
     *
     * @return array Event details
     */
    public function getEvents($where=null, $order_by = null, $limit = null)
    {
        $sql = 'SELECT * ,
            (select if (event_cfp_start IS NOT NULL AND event_cfp_start > 0 AND ' . 
                mktime(0, 0, 0) . ' BETWEEN event_cfp_start 
                AND event_cfp_end, 1, 0)) as is_cfp,
            (select count(*) from user_attend where user_attend.eid = events.ID) 
                as num_attend,
            (select count(*) from event_comments 
                where event_comments.event_id = events.ID) as num_comments,
            abs(0) as user_attending,
            abs(
                datediff(
                    from_unixtime(events.event_start),
                    from_unixtime('.mktime(0, 0, 0).')
                )
            ) as score,
            CASE 
                WHEN (
                    ((events.event_start - 86400) < '.mktime(0, 0, 0).')
                    and (events.event_start + (3*30*3600*24)) > ' . mktime(0, 0, 0) .
                ')
                THEN 1
                ELSE 0
            END as allow_comments
            FROM `events`
            WHERE active = 1 AND (pending = 0 OR pending IS NULL)';

        if ($where) {
            $sql .= ' AND (' . $where . ')';
        }

        // by default, don't show private events
        $sql.= " AND private!='Y'";

        if ($order_by) {
            $sql .= ' ORDER BY ' . $order_by;
        }

        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        $query  = $this->db->query($sql);
        $result = $query->result();

        $CI=&get_instance();
        $CI->load->model('tags_events_model', 'eventTags');
        foreach ($result as $index => $event) {
            $result[$index]->eventTags = $CI->eventTags->getTags($event->ID);
        }

        return $result;
    }

    /**
     * Convenience method for calling getEventsOfType() with type as 'hot'
     *
     * @param integer $limit Limit on results
     *
     * @return array Event details
     */
    function getHotEvents($limit = null)
    {
        $result = $this->getEventsOfType("hot", $limit);
        return $result;
    }

    /**
     * Convenience method for calling Event_model::getEventsOfType() with 
     * type = 'upcoming'
     *
     * @param integer $limit    Limit on results
     * @param mixed   $inc_curr Not currently used
     *
     * @return array Event details
     */
    function getUpcomingEvents($limit = null, $inc_curr = false)
    {
        // inc_curr not handled

        $result = $this->getEventsOfType("upcoming", $limit);
        return $result;
    }
    
    /**
     * Retrieves past events.  Also supports results per page & page no required
     *
     * If $per_page & $current page are both supplied, then this method will limit
     * the results to the the $current_page based on $per_page results per page
     * Uses Event_model::getEventsOfType() with type 'past'.
     *
     * @param integer $limit        Limit on results
     * @param mixed   $per_page     Used only in conjunction with $current_page.
     *                              No of records a page contains
     * @param mixed   $current_page Used only in conjunction with $per_page.
     *                              Which page to return results from.
     *                              0 = first page
     *
     * @uses Event_model::getEventsOfType()
     *
     * @return array Event details
     */
    function getPastEvents($limit = null, $per_page = null, $current_page = null)
    {
        $result = $this->getEventsOfType("past", $limit);            
        if ($per_page && $current_page) {
            $total_count = count($result) / $per_page;
            $result      = array_slice($result, ($current_page * $per_page),
                $per_page);
            $result['total_count'] = $total_count;
        }
        
        return $result;
    }

    /**
     * Find events tagged with the given data
     *
     * Singular tags for now, maybe multiple later?
     *
     * @param mixed $tagData Tag(s) to search on
     *
     * @return array Event results
     */
    public function getEventsByTag($tagData)
    {
        $CI=&get_instance();
        $CI->load->model('tags_events_model', 'eventTags');
        
        $sql = 'SELECT events.* ,
            (select count(*) from user_attend 
                where user_attend.eid = events.ID) as num_attend,
            (select count(*) from event_comments 
                where event_comments.event_id = events.ID) as num_comments,
            abs(0) as user_attending,
            abs(
                datediff(
                    from_unixtime(events.event_start),
                    from_unixtime(' . mktime(0, 0, 0).')
                )
            ) as score,
            CASE
                WHEN (
                    ((events.event_start - 86400) < '.mktime(0, 0, 0).')
                    and (events.event_start + (3*30*3600*24)) > ' . 
                        mktime(0, 0, 0) . '
                )
                THEN 1
                ELSE 0
                END as allow_comments
            FROM events, tags_events, tags
            WHERE active = 1 AND (pending = 0 OR pending IS NULL) AND
            tags_events.event_id = events.ID AND tags_events.tag_id = tags.ID AND
            tags.tag_value = "'.$this->db->escape($tagData).'"
            ORDER BY events.event_start ASC';

        $query  = $this->db->query($sql);
        $result = $query->result();
        foreach ($result as $index => $event) {
            $result[$index]->eventTags = $CI->eventTags->getTags($event->ID);
        }
        return $result;
    }

    /**
     * Selects user details from user_admin table for a given event
     *
     * @param mixed $eid         ID of the event in question
     * @param mixed $all_results If true, will not filter user_admin records
     *                           that have an rcode of NULL or 'pending'
     *                           Default = false
     *
     * @return object|array Returns a result object, or an empty array if no
     *                      results
     */
    function getEventAdmins($eid, $all_results=false)
    {
        $sql=sprintf(
            "
            select
                u.username,
                u.full_name,
                u.email,
                u.ID
            from
                user_admin ua,
                user u,
                events e
            where
                e.ID=%s and
                ua.rtype='event' and
                ua.rid=e.ID and
                u.ID=ua.uid
            ",
            $this->db->escape($eid)
        );

        if (!$all_results) {
            $sql .= " and (rcode!='pending' or IFNULL(rcode,0)!='pending' or " . 
                "rcode=NULL)";
        }
    
        return $this->db->query($sql)->result();
    }

    
    /**
     * Checks if a given user has already commented on an event
     *
     * @param mixed $eid     ID of the event to check
     * @param mixed $user_id ID of the user
     *
     * @return bool
     */
    function hasUserCommentedEvent($eid, $user_id)
    {
        $sql=sprintf(
            "
            SELECT event_id
            FROM event_comments
            WHERE event_id = %s
                AND user_id = %s
            ",
            $this->db->escape($eid),
            $this->db->escape($user_id)
        );
        $q = $this->db->query($sql);
        $r = $q->result();
        
        if (count($r) > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Look up an event ID based on its event_stub
     *
     * @param mixed $name Value to search for in the events table event_stub
     *        field
     *
     * @return object|array Returns a result object, or an empty array if no
     *                      results
     *
     */
    function getEventIdByName($name)
    {
        $q = $this->db->get_where('events', array('event_stub' => $name));
        return $q->result();
    }

    /**
     * Fetches an event using its title (event_name field)
     *
     * Search is case insensitive
     *
     * @param mixed $title Title of the event to be looked up.  Is compared to
     *                     the event_name field in the events table
     *
     * @return object|array Returns a result object, or an empty array if no
     *                      results
     */
    function getEventIdByTitle($title)
    {
        $this->db->select('id');
        $this->db->from('events');
        $this->db->where("lower(event_name)", strtolower($title));
        $q=$this->db->get();
        return $q->result();
    }
    
    /**
     * @param mixed $event_id ID of the event 
     *                        Needs to be either a string, or be able to
     *                        convert to a string for use in SQL statement
     *
     * @return object|array Results object or, if no results, an empty array
     *
     */
    function getEventClaims($event_id)
    {
        $sql=sprintf(
            '
            select
                t.id as talk_id,
                t.talk_title,
                ua.uid as user_id,
                ua.rid,
                u.full_name,
                ua.rcode
            from
                user_admin ua,
                events e,
                talks t,
                user u
            where
                ua.rid=t.id and
                e.id=t.event_id and
                u.id=ua.uid and
                ua.rtype = \'talk\' and
                e.id = %s
            ',
            $this->db->escape($event_id)
        );
        $q=$this->db->query($sql);
        $ret=$q->result();
        
        return $ret;
    }
    
    /**
     * Fetches all talks that have been claimed (have an associated
     * talk_speaker record) for a given event
     *
     * @param mixed $eid   ID of the event to be queried
     * @param mixed $talks (not used)
     *                     Defaults = null
     *
     * @return array Array of claimed talk details in the format...
     *               array(
     *                  <talk_id> => array(
     *                      <full_result_record> (object?)
     *                  )
     *               )
     */
    function getClaimedTalks($eid, $talks = null)
    {
        $this->load->helper('events');
        
        $sql=sprintf(
            "
            select
                ts.speaker_id,
                u.username,
                u.full_name,
                t.ID talk_id,
                ts.speaker_name
            from
                talks t,
                user u,
                talk_speaker ts
            where
                ts.talk_id = t.ID and
                t.event_id = %s and
                u.ID = ts.speaker_id
            ",
            $eid
        );
        $query  = $this->db->query($sql);
        $claims = $query->result();
        
        $claimedTalks = array();
        foreach ($claims as $claim) {
            $claimedTalks[$claim->talk_id][$claim->speaker_id]=$claim;
        }
        
        // This gives us a return array of all of the claimed talks
        // for the this event
        return $claimedTalks;
    }

    /**
     * Fetches details of talks, & their comments & ratings
     *
     * Expect speaker field to be empty, data was restructured (not brave
     * enough to remove)
     *
     * @param mixed $eid      ID of the event in question
     * @param mixed $order_by Order to sort results by.  Only valid values are
     *                        'tc.date_made' || 'tc.date_made DESC'
     *                        Default = null (= sort by talks.ID)
     *
     * @return object|array Returns a results object or, if no results, an
     *                      empty array
     *
     */
    function getEventFeedback($eid, $order_by = null)
    {
        // handle the ordering
        if ($order_by == 'tc.date_made' || $order_by == 'tc.date_made DESC') {
            // fine, sensible options that we'll allow
        } else {
            // if null, or indeed anything else, order by talk id (the
            // original default)
            $order_by = 't.ID';
        }

        $sql=sprintf(
            '
            select
                t.talk_title,
                t.speaker,
                t.date_given,
                t.duration,
                tc.date_made,
                tc.rating,
                tc.comment,
                u.full_name
            from
                talks t,
                talk_comments tc
            LEFT JOIN user u ON (u.ID = tc.user_id)
            where
                tc.private <> 1 AND
                t.ID=tc.talk_id AND
                t.event_id=%s
            order by %s
            ',
            $this->db->escape($eid),
            $order_by
        );
        $q=$this->db->query($sql);
        return $q->result();
    }

    /**
     * Fetches active talks that are in the 'Event Related' category
     *
     * Results are ordered by date & then speaker
     *
     * @param mixed $id ID of the event in question
     *                   Must be either a string, or able to cast as a
     *                   string
     *
     * @return object|array Returns a results object or, if not results, an
     *                      empty array
     *
     */
    function getEventRelatedSessions($id)
    {
        $sql=sprintf(
            '
            select
                talks.talk_title,
                talks.speaker,
                talks.slides_link,
                talks.date_given,
                talks.duration,
                talks.event_id,
                talks.ID,
                talks.talk_desc,
                events.event_tz_cont,
                events.event_tz_place,
                (select l.lang_abbr from lang l where talks.lang=l.ID) lang,
                get_talk_rating(talks.ID) rank,
                (
                    select count(rating) from talk_comments
                    where talk_id=talks.ID
                ) comment_count,
                ifnull(categories.cat_title, \'Talk\') tcid
            from
                talks
            inner join lang on (lang.ID = talks.lang)
            inner join events on events.ID = talks.event_id
            left join talk_cat on talks.ID = talk_cat.talk_id
            left join categories on talk_cat.cat_id = categories.ID
            where
                categories.cat_title = "Event Related" and
                event_id=%s and
                talks.active=1
            order by
                talks.date_given asc, talks.speaker asc
            ',
            $this->db->escape($id)
        );
        $q=$this->db->query($sql);
        return $q->result();
    }
    
    /**
     * Find the currently open calls for papers on events
     *
     * @return array Event details
     *
     * @uses Event_model::getEvents()
     */
    public function getCurrentCfp()
    {
        $where = 'event_cfp_start <= ' 
            . mktime( 0, 0, 0, date('m'), date('d'), date('Y')) 
            . ' AND ' . 'event_cfp_end >= ' 
            . mktime( 0, 0, 0, date('m'), date('d'), date('Y')
        );
        $order_by = "events.event_cfp_end asc";
        $result = $this->getEvents($where, $order_by, null);
        return $result;
    }

    //----------------------

    /**
     * Looks for events who's event_name of event_desc fields contain the
     * given search term
     *
     * @param mixed $term  Term to search for in event name or description
     *                     fields.
     *                     Must be either a string, or able to be cast as a
     *                     string
     * @param mixed $start The start of the date window to search within
     *                     Will include results on this date
     * @param mixed $end   The end of the date window to search within
     *                     Will include results on this date
     *
     * @return object|array Returns a results object or, if there are no
     *                      results, an empty array
     */
    function search($term, $start, $end)
    {
        $term = mysql_real_escape_string($term);

        $attend = '
            (
                SELECT COUNT(*) FROM user_attend
                WHERE eid = events.ID
                    AND uid = ' .
                    $this->db->escape((int)$this->session->userdata('ID')) .
            ') as user_attending';

        $this->db->select(
            'events.*,
            COUNT(DISTINCT user_attend.ID) AS num_attend,
            COUNT(DISTINCT event_comments.ID) AS num_comments,' .
            $attend
        );
        $this->db->from('events');
        $this->db->join('user_attend', 'user_attend.eid = events.ID', 'left');
        $this->db->join(
            'event_comments', 
            'event_comments.event_id = events.ID', 
            'left'
        );
        
        //if we have the dates, limit by them
        if ($start>0) { 
            $this->db->where('event_start >=', $start); 
        }
        if ($end>0) { 
            $this->db->where('event_start <=', $end); 
        }

        $term = '%'.$term.'%';
        $this->db->where(
            sprintf(
                '(event_name LIKE %1$s OR event_desc LIKE %1$s)',
                $this->db->escape($term)
            )
        );
        $this->db->limit(10);
        $this->db->group_by('events.ID');
        $this->db->order_by('event_start DESC');

        $q=$this->db->get();
        return $q->result();
    }
}

