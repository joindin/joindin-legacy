<?php

class EventModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon'
            );
        return $fields;
    }

    public static function getVerboseFields() {
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
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end'
            );
        return $fields;
    }

    public static function getEventById($db, $event_id, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'ID = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }

    public static function getEventList($db, $resultsperpage, $start, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'private <> "y" '
            . 'order by event_start desc';
        $sql .= static::buildLimit($resultsperpage, $start);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute();
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    public static function addHyperMedia($list, $request) {
        $host = $request->host;

        // add per-item links 
        if(is_array($list) && count($list)) {
            foreach($list as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/events/' . $row['event_id'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '?verbose=yes';
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '/comments';
                $list[$key]['talks_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '/talks';
            }
        }

        // add pagination and global links
        $list = static::addPaginationLinks($list, $request);
        return $list;
    }

    protected function addPaginationLinks($list, $request) {
        $list['links']['this_page'] = 'http://' . $request->host . $request->path_info .'?' . http_build_query($request->parameters);
        $next_params = $prev_params = $request->parameters;

        $next_params['start'] = $next_params['start'] + $next_params['resultsperpage'];
        $list['links']['next_page'] = 'http://' . $request->host . $request->path_info . '?' . http_build_query($next_params);
        if($prev_params['start'] >= $prev_params['resultsperpage']) {
            $prev_params['start'] = $prev_params['start'] - $prev_params['resultsperpage'];
            $list['links']['prev_page'] = 'http://' . $request->host . $request->path_info . '?' . http_build_query($prev_params);
        }
        return $list;
    }
}
