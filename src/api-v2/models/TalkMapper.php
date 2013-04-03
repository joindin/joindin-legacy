<?php

class TalkMapper extends ApiMapper {
    public function getDefaultFields() {
        $fields = array(
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'type' => 'category',
            'start_date' => 'date_given',
			'duration' => 'duration',
            'average_rating' => 'avg_rating',
            'comments_enabled' => 'comments_enabled',
            'comment_count' => 'comment_count'
            );
        return $fields;
    }

    public function getVerboseFields() {
        $fields = array(
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'type' => 'category',
            'slides_link' => 'slides_link',
            'language' => 'lang_name',
            'start_date' => 'date_given',
			'duration' => 'duration',
            'average_rating' => 'avg_rating',
            'comments_enabled' => 'comments_enabled',
            'comment_count' => 'comment_count'
            );
        return $fields;
    }

    public function getTalksByEventId($event_id, $resultsperpage, $start, $verbose = false) {
        $sql = $this->getBasicSQL();
        $sql .= ' and t.event_id = :event_id';
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
        $base = $this->_request->base;
        $version = $this->_request->version;

        // loop again and add links specific to this item
        if(is_array($list) && count($list)) {
            foreach($results as $key => $row) {
                // add speakers
                $list[$key]['speakers'] = $this->getSpeakers($row['ID']);
                $list[$key]['tracks'] = $this->getTracks($row['ID']);
                $list[$key]['uri'] = $base . '/' . $version . '/talks/' . $row['ID'];
                $list[$key]['verbose_uri'] = $base . '/' . $version . '/talks/' . $row['ID'] . '?verbose=yes';
                $list[$key]['website_uri'] = 'http://joind.in/talk/view/' . $row['ID'];
                $list[$key]['comments_uri'] = $base . '/' . $version . '/talks/' . $row['ID'] . '/comments';
                $list[$key]['verbose_comments_uri'] = $base . '/' . $version . '/talks/' . $row['ID'] . '/comments?verbose=yes';
                $list[$key]['event_uri'] = $base . '/' . $version . '/events/' . $row['event_id'];
            }
        }

        $retval = array();
        $retval['talks'] = $list;
        $retval['meta'] = $this->getPaginationLinks($list);

        return $retval;
    }

    public function getTalkById($talk_id, $verbose = false) {
        $sql = $this->getBasicSQL();
        $sql .= ' and t.ID = :talk_id';
        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(array("talk_id" => $talk_id));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $retval = $this->transformResults($results, $verbose);
                return $retval;
            }
        }
        return false;
    }

    public function getBasicSQL() {
        $sql = 'select t.*, l.lang_name, e.event_tz_place, e.event_tz_cont, '
            . '(select COUNT(ID) from talk_comments tc where tc.talk_id = t.ID) as comment_count, '
            . '(select get_talk_rating(t.ID)) as avg_rating, '
            . 'CASE 
                WHEN (((t.date_given - 3600*24) < '.mktime(0,0,0).') and (t.date_given + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
               END as comments_enabled, '
            . 'c.cat_title as category '
            . 'from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'join talk_cat tc on tc.talk_id = t.ID '
            . 'join categories c on c.ID = tc.cat_id '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . '(e.pending = 0 or e.pending is NULL) and '
            . '(e.private <> "y" or e.private is NULL)';
        return $sql;

    }

    protected function getSpeakers($talk_id) {
        $base = $this->_request->base;
        $version = $this->_request->version;

        $speaker_sql = 'select ts.*, user.full_name from talk_speaker ts '
            . 'left join user on user.ID = ts.speaker_id '
            . 'where ts.talk_id = :talk_id and ts.status IS NULL';
        $speaker_stmt = $this->_db->prepare($speaker_sql);
        $speaker_stmt->execute(array("talk_id" => $talk_id));
        $speakers = $speaker_stmt->fetchAll(PDO::FETCH_ASSOC);
        $retval = array();
        if(is_array($speakers)) {
           foreach($speakers as $person) {
               $entry = array();
               if($person['full_name']) {
                   $entry['speaker_name'] = $person['full_name'];
                   $entry['speaker_uri'] = $base . '/' . $version . '/users/' . $person['speaker_id'];
               } else {
                   $entry['speaker_name'] = $person['speaker_name'];
               }
               $retval[] = $entry;
           }
        }
        return $retval;
    }

    protected function getTracks($talk_id) {
        $host = $this->_request->host;
        $track_sql = 'select et.track_name '
            . 'from talk_track tt '
            . 'inner join event_track et on et.ID = tt.track_id '
            . 'where tt.talk_id = :talk_id';
        $track_stmt = $this->_db->prepare($track_sql);
        $track_stmt->execute(array("talk_id" => $talk_id));
        $tracks = $track_stmt->fetchAll(PDO::FETCH_ASSOC);
        $retval = array();
        if(is_array($tracks)) {
           foreach($tracks as $track) {
               $retval[] = $track;
           }
        }
        return $retval;
    }

    public function getTalksBySpeaker($user_id, $resultsperpage, $start, $verbose = false) {
        // based on getBasicSQL() but needs the speaker table joins
        $sql = 'select t.*, l.lang_name, e.event_tz_place, e.event_tz_cont, '
            . '(select COUNT(ID) from talk_comments tc where tc.talk_id = t.ID) as comment_count, '
            . '(select get_talk_rating(t.ID)) as avg_rating, '
            . 'CASE 
                WHEN (((t.date_given - 3600*24) < '.mktime(0,0,0).') and (t.date_given + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
               END as comments_enabled '
            . 'from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'left join talk_speaker ts on t.id = ts.talk_id '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . '(e.pending = 0 or e.pending is NULL) and '
            . '(e.private <> "y" or e.private is NULL) and '
            . 'ts.speaker_id = :user_id '
            . 'order by t.date_given desc';
        $sql .= $this->buildLimit($resultsperpage, $start);

        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(array(
            ':user_id' => $user_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }


    public function save($data) {
        $date = $data['date']->format('U');
 
        // TODO map from the field mappings in getVerboseFields()
        $sql = 'insert into talks (event_id, talk_title, talk_desc, '
            . 'lang, date_given) '
            . 'values (:event_id, :talk_title, :talk_description, '
            . '(select ID from lang where lang_name = :language), '
            . ':date)';

        $stmt = $this->_db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $data['event_id'],
            ':talk_title' => $data['title'],
            ':talk_description' => $data['description'],
            ':language' => $data['language'],
            ':date' => $date
        ));
        $talk_id = $this->_db->lastInsertId();

        // set talk type
        // TODO support more than just talks
        $cat_sql = 'insert into talk_cat (talk_id, cat_id) values (:talk_id, 1)';
        $cat_stmt = $this->_db->prepare($cat_sql);
        $cat_stmt->execute(array(':talk_id' => $talk_id));

        // save speakers
        if(isset($data['speakers']) && is_array($data['speakers'])) {
            foreach($data['speakers'] as $speaker) {
                $speaker_sql = 'insert into talk_speaker (talk_id, speaker_name) values '
                    . '(:talk_id, :speaker)';
                $speaker_stmt = $this->_db->prepare($speaker_sql);
                $speaker_stmt->execute(array(
                    ':talk_id' => $talk_id,
                    ':speaker' => $speaker
                ));
            }
        }

        return $talk_id;
    }

}
