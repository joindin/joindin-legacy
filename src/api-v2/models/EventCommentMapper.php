<?php

class EventCommentMapper extends ApiMapper {
    public function getDefaultFields() {
        // warning, users added in build array
        $fields = array(
            'comment' => 'comment',
            'rating' => 'rating',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public function getVerboseFields() {
        $fields = array(
            'comment' => 'comment',
            'rating' => 'rating',
            'source' => 'source',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public function getEventCommentsByEventId($event_id, $resultsperpage, $start, $verbose = false) {
        $sql = $this->getBasicSQL();
        $sql .= 'and event_id = :event_id ';
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

    public function getCommentById($comment_id, $verbose = false) {
        $sql = $this->getBasicSQL();
        $sql .= 'and ec.ID = :comment_id ';
        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(array(
            ':comment_id' => $comment_id
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

        if (is_array($list) && count($list)) {

            foreach ($results as $key => $row) {
                // figure out user
                if($row['user_id']) {
                    $list[$key]['user_display_name'] = $row['full_name'];
                    $list[$key]['user_uri'] = 'http://' . $host . '/v2/users/' 
                        . $row['user_id'];
                } else {
                    $list[$key]['user_display_name'] = $row['cname'];
                }

                // useful links
                $list[$key]['comment_uri'] = 'http://' . $host . '/v2/event_comments/' 
                    . $row['ID'];
                $list[$key]['verbose_comment_uri'] = 'http://' . $host . '/v2/event_comments/' 
                    . $row['ID'] . '?verbose=yes';
                $list[$key]['event_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'];
                $list[$key]['event_comments_uri'] = 'http://' . $host . '/v2/events/' 
                    . $row['event_id'] . '/comments';
            }

            if (count($list) > 1) {
                $list = $this->addPaginationLinks($list);
            }
        }
        return $list;
    }

    protected function getBasicSQL() {
        $sql = 'select ec.*, user.full_name '
            . 'from event_comments ec '
            . 'left join user on user.ID = ec.user_id '
            . 'where ec.active = 1 ';
        return $sql;

    }
}
