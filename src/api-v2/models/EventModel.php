<?php

/**
 * EventModel 
 * 
 * @uses ApiModel
 * @package API
 */
class EventModel extends ApiModel 
{
    /**
     * Default mapping for column names to API field names
     * 
     * @static
     * 
     * @return array with keys as API fields and values as db columns
     */
    public static function getDefaultFields() 
    {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'attendee_count' => 'attendee_count',
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
    public static function getVerboseFields() 
    {
        $fields = array(
            'event_id' => 'ID',
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
            'event_comment_count' => 'event_comment_count',
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end',
            'cfp_url' => 'event_cfp_url'
            );
        return $fields;
    }

    /**
     * Fetch the details for a single event
     * 
     * @param PDO $db database object
     * @param int $event_id events.ID value
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the event detail
     */
    public static function getEventById($db, $event_id, $verbose = false) 
    {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'ID = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(
            array(':event_id' => $event_id)
        );
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }

    /**
     * Internal function called by other event-fetching code, with changeable SQL
     * 
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param string $where one final thing to add to the where after an "AND"
     * @param string $order what goes after "ORDER BY"
     *
     * @return array the raw database results
     */
    protected static function getEvents($db, $resultsperpage, $start, 
    $where = null, $order = null) 
    {
        $sql = 'select events.*, '
            . '(select count(*) from user_attend where user_attend.eid = events.ID) 
                as attendee_count, '
            . '(select count(*) from event_comments where 
                event_comments.event_id = events.ID) 
                as event_comment_count, '
            . 'abs(datediff(from_unixtime(events.event_start), 
                from_unixtime('.mktime(0, 0, 0).'))) as score '
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
        $sql .= static::buildLimit($resultsperpage, $start);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute();
        if ($response) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * getEventList 
     * 
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public static function getEventList($db, $resultsperpage, $start, 
    $verbose = false) 
    {
        $order = 'events.event_start desc';
        $results = static::getEvents($db, $resultsperpage, $start, null, $order);
        if ($results) {
            $retval = static::transformResults($results, $verbose);
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
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public static function getHotEventList($db, $resultsperpage, $start, 
    $verbose = false) 
    {
        $order = '(((attendee_count + event_comment_count) * 0.5) 
                - EXP(GREATEST(1,score)/20)) desc';
        $results = static::getEvents($db, $resultsperpage, $start, null, $order);
        if ($results) {
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Future events, soonest first
     * 
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public static function getUpcomingEventList($db, $resultsperpage, $start, 
    $verbose = false) 
    {
        $where = '(events.event_start >=' . (mktime(0, 0, 0) - (3 * 86400)) . ')';
        $order = 'events.event_start';
        $results = static::getEvents($db, $resultsperpage, $start, $where, $order);
        if ($results) {
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Past events, most recent first
     * 
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public static function getPastEventList($db, $resultsperpage, $start, 
    $verbose = false) 
    {
        $where = '(events.event_start <' . (mktime(0, 0, 0)) . ')';
        $order = 'events.event_start desc';
        $results = static::getEvents($db, $resultsperpage, $start, $where, $order);
        if ($results) {
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Events with CfPs that close in the future and a cfp_url
     * 
     * @param PDO $db database object
     * @param int $resultsperpage how many records to return
     * @param int $start offset to start returning records from
     * @param boolean $verbose used to determine how many fields are needed
     * 
     * @return array the data, or false if something went wrong
     */
    public static function getOpenCfPEventList($db, $resultsperpage, $start, 
    $verbose = false) 
    {
        $where = 'events.event_cfp_url IS NOT NULL AND events.event_cfp_end >= ' . mktime(0, 0, 0);
        $order = 'events.event_start';
        $results = static::getEvents($db, $resultsperpage, $start, $where, $order);
        if ($results) {
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Add links to related stuff, including the talks/comments for this event
     * 
     * @param array $list Current data set
     * @param Request $request The Request object created at bootstrap
     * 
     * @return array A dataset now with each record having its links,
     *     and pagination if appropriate
     */
    public static function addHyperMedia($list, $request) 
    {
        $host = $request->host;

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($list as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'] . '?verbose=yes';
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'] . '/comments';
                $list[$key]['talks_link'] = 'http://' . $host . '/v2/events/' 
                . $row['event_id'] . '/talks';
                $list[$key]['website_uri'] = 'http://joind.in/event/view/' . $row['ID'];
            }

            if (count($list) > 1) {
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }
}
