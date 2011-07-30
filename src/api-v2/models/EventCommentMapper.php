<?php

class EventCommentMapper extends ApiMapper {
    public function getDefaultFields() {
        $fields = array(
            'comment' => 'comment'
            );
        return $fields;
    }

    public function getVerboseFields() {
        $fields = array(
            'comment' => 'comment',
            'source' => 'source',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public function getEventCommentsByEventId($event_id, $resultsperpage, $start, $verbose = false) {
        $sql = 'select * from event_comments '
            . 'where event_id = :event_id '
            . 'and active = 1 ';
        $sql .= $this->buildLimit($resultsperpage, $start);
        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    public function transformResults($results, $verbose) {
        $list = parent::transformResults($results, $verbose);
        $host = $this->_request->host;

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($results as $key => $row) {
                $list[$key]['event_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'];
                if($row['user_id']) {
                    $list[$key]['user_uri'] = 'http://' . $host . '/v2/users/' 
                        . $row['user_id'];
                }
            }

            if (count($list) > 1) {
                $list = $this->addPaginationLinks($list, $this->_request);
            }
        }
        return $list;
    }
}
