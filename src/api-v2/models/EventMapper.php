<?php

/**
 * EventModel
 *
 * @uses ApiModel
 * @package API
 */
class EventMapper extends ApiMapper
{

    /**
     * Default mapping for column names to API field names
     *
     * @return array with keys as API fields and values as db columns
     */
    public function getDefaultFields()
    {
        $fields = array(
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'attendee_count' => 'attendee_count',
            'attending' => 'attending',
            'event_comments_count' => 'event_comments_count',
            'talk_comments_count' => 'talk_comments_count',
            'icon' => 'event_icon'
            );
        return $fields;
    }

    /**
     * Field/column name mappings for the verbose version
     *
     * This should contain everything above and then more in most cases
     *
     * @return array with keys as API fields and values as db columns
     */
    public function getVerboseFields()
    {
        $fields = array(
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon',
            'latitude' => 'event_lat',
            'longitude' => 'event_long',
            'tz_continent' => 'event_tz_cont',
            'tz_place' => 'event_tz_place',
            'location' => 'event_loc',
            'hashtag' => 'event_hashtag',
            'attendee_count' => 'attendee_count',
            'attending' => 'attending',
            'comments_enabled' => 'comments_enabled',
            'event_comments_count' => 'event_comments_count',
            'talk_comments_count' => 'talk_comments_count',
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end',
            'cfp_url' => 'event_cfp_url'
            );
        return $fields;
    }

    /**
     * Fetch the details for a single event
     *
     * @param int $event_id events.ID value
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the event detail
     */
    public function getEventById($event_id, $verbose = false, $connectedUserId = -1)
    {
        $order = 'events.event_start desc';
        $results = $this->getEvents(1, 0, 'events.ID=' . (int)$event_id, null, $connectedUserId);
        if ($results) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }

    /**
     * Internal function called by other event-fetching code, with changeable SQL
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param string $where one final thing to add to the where after an "AND"
     * @param string $order what goes after "ORDER BY"
     * @param int $connectedUserId Connected user
     *
     * @return array the raw database results
     */
    protected function getEvents($resultsperpage, $start, $where = null, $order = null,
        $connectedUserId = -1)
    {
        $this->getEventDataInTemporaryTables();

        $data = array('userId' => $connectedUserId);
        $attendingSql = '';
        if (false !== strpos($where, 'current_ua')) {
            $attendingSql = 'Left Outer Join user_attend As current_ua On current_ua.eid = events.ID';
        }

        $sql = '
            Select events.*,
                IFNULL(attendees, 0) As attendee_count,
                IFNULL(tmp_comments_count.event_comments, 0) As event_comments_count,
                IFNULL(tmp_talk_comments_count.talk_comments, 0) As talk_comments_count,
                abs(datediff(from_unixtime(events.event_start),
                    from_unixtime('.mktime(0, 0, 0).'))) as score,
                CASE
                 WHEN (((events.event_start - 3600*24) < '.mktime(0,0,0).') and (events.event_start + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                 ELSE 0
                END as comments_enabled,
                IF(connected_ua.ID Is Null, 0, 1) as attending
            From events
            ' . $attendingSql . '
            Left Outer Join user_attend As connected_ua On connected_ua.eid = events.ID
                And connected_ua.uid = :userId
            Left Outer Join tmp_attendee_count On tmp_attendee_count.event_id = events.ID
            Left Outer Join tmp_comments_count On tmp_comments_count.event_id = events.ID
            Left Outer Join tmp_talk_comments_count On tmp_talk_comments_count.event_id = events.ID
            Where events.active = 1
            And (events.pending = 0 or events.pending is NULL)
            And events.private <> "y"
        ';

        // where
        if ($where) {
            $sql .= ' and ' . $where;
        }

        // group by for the multiple attending recipes; only ever want to see each event once
        $sql .= ' group by events.ID ';

        // order by
        if ($order) {
            $sql .= ' order by ' . $order;
        }

        // limit clause
        $sql .= $this->buildLimit($resultsperpage, $start);

        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute($data);
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        return false;
    }

    /**
     * Copy some of the data needed for events into
     * temporary tables to speed up the extraction.
     */
    protected function getEventDataInTemporaryTables()
    {
        $sql = '
            Create Temporary Table tmp_comments_count (Primary Key (event_id))
            Select event_id, count(1) As event_comments
            From event_comments
            Where active = 1
            Group By event_id
        ';
        $this->_db->exec($sql);

        $sql = '
            Create Temporary Table tmp_talk_comments_count (Primary Key (event_id))
            Select event_id, Count(1) As talk_comments
            From talks
            Left Outer Join talk_comments On talks.ID = talk_comments.talk_id
            Where talks.active = 1
            And talk_comments.active = 1
            And private <> 1
            Group By talks.event_id
        ';
        $this->_db->exec($sql);

        $sql = '
            Create Temporary Table tmp_attendee_count (Primary Key (event_id))
            Select eid as event_id, count(1) As attendees
            From user_attend
            Group By eid
        ';
        $this->_db->exec($sql);
    }

    /**
     * getEventList
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getEventList($resultsperpage, $start, $verbose = false, $connectedUserId = -1)
    {
        $order = 'events.event_start desc';
        $results = $this->getEvents($resultsperpage, $start, null, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Events which are current and popular
     *
     * formula taken from original joindin codebase, uses number of people
     * attending and how soon/recent something is to calculate it's "hotness"
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getHotEventList($resultsperpage, $start, $verbose = false, $connectedUserId = -1)
    {
        $order = "score - ((event_comments_count + attendee_count + 1) / 5)";
        $results = $this->getEvents($resultsperpage, $start, null, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Future events, soonest first
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getUpcomingEventList($resultsperpage, $start, $verbose = false, $connectedUserId = -1)
    {
        $where = '(events.event_start >=' . (mktime(0, 0, 0) - (3 * 86400)) . ')';
        $order = 'events.event_start';
        $results = $this->getEvents($resultsperpage, $start, $where, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Past events, most recent first
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getPastEventList($resultsperpage, $start, $verbose = false, $connectedUserId = -1)
    {
        $where = '(events.event_start <' . (mktime(0, 0, 0)) . ')';
        $order = 'events.event_start desc';
        $results = $this->getEvents($resultsperpage, $start, $where, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Events with CfPs that close in the future and a cfp_url
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getOpenCfPEventList($resultsperpage, $start, $verbose = false, $connectedUserId = -1)
    {
        $where = 'events.event_cfp_url IS NOT NULL AND events.event_cfp_end >= ' . mktime(0, 0, 0);
        $order = 'events.event_start';
        $results = $this->getEvents($resultsperpage, $start, $where, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Turn results into arrays with correct fields, add hypermedia
     *
     * @param array $results Results of the database query
     * @param boolean $verbose whether to return detailed information
     * @return array A dataset now with each record having its links,
     *     and pagination if appropriate
     */
    public function transformResults($results, $verbose)
    {
        $list = parent::transformResults($results, $verbose);
        $base = $this->_request->base;
        $version = $this->_request->version;

        // add per-item links
        if (is_array($list) && count($list)) {
            foreach ($results as $key => $row) {
                // flip the attending to be true/false rather than user id or null
                if($row['attending']) {
                    $list[$key]['attending'] = true;
                } else {
                    $list[$key]['attending'] = false;
                }

                $list[$key]['tags'] = $this->getTags($row['ID']);;
                $list[$key]['uri'] = $base . '/' . $version . '/events/'
                    . $row['ID'];
                $list[$key]['verbose_uri'] = $base . '/' . $version . '/events/'
                    . $row['ID'] . '?verbose=yes';
                $list[$key]['comments_uri'] = $base . '/' . $version . '/events/'
                    . $row['ID'] . '/comments';
                $list[$key]['talks_uri'] = $base . '/' . $version . '/events/'
                . $row['ID'] . '/talks';
                $list[$key]['website_uri'] = 'http://joind.in/event/view/' . $row['ID'];
                // handle the slug
                if(!empty($row['event_stub'])) {
                    $list[$key]['humane_website_uri'] = 'http://joind.in/event/' . $row['event_stub'];
                }

                if($verbose) {
                    $list[$key]['all_talk_comments_uri'] = $base . '/' . $version . '/events/'
                        . $row['ID'] . '/talk_comments';
                    $list[$key]['hosts'] = $this->getHosts($row['ID']);
                }
            }
        }
        $retval = array();
        $retval['events'] = $list;
        $retval['meta'] = $this->getPaginationLinks($list);

        return $retval;
    }

    /**
     * Fetch the users who are hosting this event
     *
     * @param int $event_id
     * @return array The list of people hosting the event
     */
    protected function getHosts($event_id)
    {
        $base = $this->_request->base;
        $version = $this->_request->version;

        $host_sql = $this->getHostSql();
        $host_stmt = $this->_db->prepare($host_sql);
        $host_stmt->execute(array("event_id" => $event_id));
        $hosts = $host_stmt->fetchAll(PDO::FETCH_ASSOC);
        $retval = array();
        if(is_array($hosts)) {
           foreach($hosts as $person) {
               $entry = array();
               $entry['host_name'] = $person['full_name'];
               $entry['host_uri'] = $base . '/' . $version . '/users/' . $person['user_id'];
               $retval[] = $entry;
           }
        }
        return $retval;
    }

    /**
     * SQL for fetching event hosts, so it can be used in multiple places
     *
     * @return SQL to fetch hosts, containing an :event_id named parameter
     */
    protected function getHostSql() {
        $host_sql = 'select a.uid as user_id, u.full_name'
            . ' from user_admin a '
            . ' inner join user u on u.ID = a.uid '
            . ' where rid = :event_id and rtype="event" and rcode!="pending"';
        return $host_sql;
    }

    /**
     * Return an array of tags for the event
     *
     * @param int $event_id The event whose tags we want
     * @return array An array of tags
     */
    protected function getTags($event_id)
    {
        $tag_sql = 'select tag_value as tag'
            . ' from tags_events te'
            . ' inner join tags t on t.ID = te.tag_id'
            . ' where te.event_id = :event_id';
        $tag_stmt = $this->_db->prepare($tag_sql);
        $tag_stmt->execute(array("event_id" => $event_id));
        $tags = $tag_stmt->fetchAll(PDO::FETCH_ASSOC);
        $retval = array();
        if(is_array($tags)) {
           foreach($tags as $row) {
               $retval[] = $row['tag'];
           }
        }
        return $retval;
    }

    /**
     * Events that the currently logged-in user is marked as attending
     *
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * @param int $connectedUserId Connected user
     *
     * @return array the data, or false if something went wrong
     */
    public function getEventsAttendedByUser($user_id, $resultsperpage, $start, $verbose = false,
            $connectedUserId = -1)
    {
        $where = ' current_ua.uid = ' . (int)$user_id;
        $order = ' events.event_start desc ';
        $results = $this->getEvents($resultsperpage, $start, $where, $order, $connectedUserId);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Does the currently-authenticated user have rights on a particular event?
     *
     * @param int $event_id The identifier for the event to check
     * @return bool True if the user has privileges, false otherwise
     */
    public function thisUserHasAdminOn($event_id) {
        // do we even have an authenticated user?
        if(isset($this->_request->user_id)) {
            $user_mapper = new UserMapper($this->_db, $this->_request);

            // is user site admin?
            $is_site_admin = $user_mapper->isSiteAdmin($this->_request->user_id);
            if($is_site_admin) {
                return true;
            }

            // is user an event admin?
            $sql = $this->getHostSql();
            $sql .= ' AND u.ID = :user_id';
            $stmt = $this->_db->prepare($sql);
            $stmt->execute(array("event_id" => $event_id,
                "user_id" => $this->_request->user_id));
            $results = $stmt->fetchAll();
            if($results) {
                return true;
            }
        }
        return false;
    }
}
