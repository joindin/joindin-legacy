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
            'icon' => 'event_icon',
	    'average_rating' => 'avg_rating'
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
            'attendee_count' => 'attendee_count',
            'comments_enabled' => 'comments_enabled',
            'event_comment_count' => 'event_comment_count',
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end',
            'cfp_url' => 'event_cfp_url',
	    'average_rating' => 'avg_rating'
            );
        return $fields;
    }

    /**
     * Fetch the details for a single event
     * 
     * @param int $event_id events.ID value
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the event detail
     */
    public function getEventById($event_id, $verbose = false) 
    {
        $sql = 'select events.*, '
            . '(select count(*) from user_attend where user_attend.eid = events.ID) 
                as attendee_count, '
            . '(select count(*) from event_comments where 
                event_comments.event_id = events.ID) 
                as event_comment_count, '
            . 'CASE 
                WHEN (((events.event_start - 3600*24) < '.mktime(0,0,0).') and (events.event_start + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
               END as comments_enabled, '
	    . '(select get_event_rating(events.ID)) as avg_rating '
            . 'from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'ID = :event_id';
        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(
            array(':event_id' => $event_id)
        );
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     *
     * @return array the raw database results
     */
    protected function getEvents($resultsperpage, $start, $where = null, $order = null) 
    {
        $sql = 'select events.*, '
            . '(select count(*) from user_attend where user_attend.eid = events.ID) 
                as attendee_count, '
            . '(select count(*) from event_comments where 
                event_comments.event_id = events.ID) 
                as event_comment_count, '
            . 'abs(datediff(from_unixtime(events.event_start), 
                from_unixtime('.mktime(0, 0, 0).'))) as score, '
            . 'CASE 
                WHEN (((events.event_start - 3600*24) < '.mktime(0,0,0).') and (events.event_start + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
               END as comments_enabled, '
	    . '(select get_talk_rating(t.ID)) as avg_rating '
            . 'from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'private <> "y" ';
        
        // where
        if ($where) {
            $sql .= ' and ' . $where;
        }

        // order by
        if ($order) {
            $sql .= ' order by ' . $order;
        }

        // limit clause
        $sql .= $this->buildLimit($resultsperpage, $start);

        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute();
        if ($response) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * getEventList 
     * 
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public function getEventList($resultsperpage, $start, $verbose = false) 
    {
        $order = 'events.event_start desc';
        $results = $this->getEvents($resultsperpage, $start, null, $order);
        if ($results) {
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
     * 
     * @return array the data, or false if something went wrong
     */
    public function getHotEventList($resultsperpage, $start, $verbose = false) 
    {
        $order = "score - ((event_comment_count + attendee_count + 1) / 5)";
        $results = $this->getEvents($resultsperpage, $start, null, $order);
        if ($results) {
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
     * 
     * @return array the data, or false if something went wrong
     */
    public function getUpcomingEventList($resultsperpage, $start, $verbose = false) 
    {
        $where = '(events.event_start >=' . (mktime(0, 0, 0) - (3 * 86400)) . ')';
        $order = 'events.event_start';
        $results = $this->getEvents($resultsperpage, $start, $where, $order);
        if ($results) {
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
     * 
     * @return array the data, or false if something went wrong
     */
    public function getPastEventList($resultsperpage, $start, $verbose = false) 
    {
        $where = '(events.event_start <' . (mktime(0, 0, 0)) . ')';
        $order = 'events.event_start desc';
        $results = $this->getEvents($resultsperpage, $start, $where, $order);
        if ($results) {
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
     * 
     * @return array the data, or false if something went wrong
     */
    public function getOpenCfPEventList($resultsperpage, $start, $verbose = false) 
    {
        $where = 'events.event_cfp_url IS NOT NULL AND events.event_cfp_end >= ' . mktime(0, 0, 0);
        $order = 'events.event_start';
        $results = $this->getEvents($resultsperpage, $start, $where, $order);
        if ($results) {
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
        $host = $this->_request->host;

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($results as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['ID'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['ID'] . '?verbose=yes';
                $list[$key]['comments_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['ID'] . '/comments';
                $list[$key]['talks_uri'] = 'http://' . $host . '/v2/events/' 
                . $row['ID'] . '/talks';
                $list[$key]['website_uri'] = 'http://joind.in/event/view/' . $row['ID'];
                if($verbose) {
                    $list[$key]['all_talk_comments_uri'] = 'http://' . $host . '/v2/events/' 
                        . $row['ID'] . '/talk_comments';
                }
            }
        }

        return $list;
    }
}
