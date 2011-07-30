<?php

class TalkModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'start_date' => 'date_given',
            'average_rating' => 'avg_rating',
            'comments_enabled' => 'comments_enabled',
            'comment_count' => 'comment_count'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'slides_link' => 'slides_link',
            'language' => 'lang_name',
            'start_date' => 'date_given',
            'average_rating' => 'avg_rating',
            'comments_enabled' => 'comments_enabled',
            'comment_count' => 'comment_count'
            );
        return $fields;
    }
    public static function getTalksByEventId($db, $event_id, $resultsperpage, $start, $request, $verbose = false) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.event_id = :event_id';
        $sql .= static::buildLimit($resultsperpage, $start);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($db, $results, $request, $verbose);
            return $retval;
        }
        return false;
    }

    public static function transformResults($db, $results, $request, $verbose) {
        $list = parent::transformResults($results, $verbose);
        $host = $request->host;
        // loop again and add links specific to this item
        if(is_array($list) && count($list)) {
            foreach($results as $key => $row) {
                // add speakers
                $list[$key]['speakers'] = static::getSpeakers($db, $row['ID'], $request);
                $list[$key]['uri'] = 'http://' . $host . '/v2/talks/' . $row['ID'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/talks/' . $row['ID'] . '?verbose=yes';
                $list[$key]['website_uri'] = 'http://joind.in/talk/view/' . $row['ID'];
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/talks/' . $row['ID'] . '/comments';
                $list[$key]['event_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'];
            }

            if(count($list) > 1) {
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }

    public static function getTalkById($db, $talk_id, $request, $verbose = false) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.ID = :talk_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array("talk_id" => $talk_id));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($db, $results, $request, $verbose);
            return $retval;
        }
        return false;
    }

    public static function getBasicSQL() {
        $sql = 'select t.*, l.lang_name, '
            . '(select COUNT(ID) from talk_comments tc where tc.talk_id = t.ID) as comment_count, '
            . '(select ROUND(AVG(rating)) from talk_comments tc where tc.talk_id = t.ID) as avg_rating, '
            . 'CASE 
                WHEN (((t.date_given - 3600*24) < '.mktime(0,0,0).') and (t.date_given + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
               END as comments_enabled '
            . 'from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . '(e.pending = 0 or e.pending is NULL) and '
            . 'e.private <> "y"';
        return $sql;

    }

    public static function getSpeakers($db, $talk_id, $request) {
        $host = $request->host;
        $speaker_sql = 'select ts.*, user.full_name from talk_speaker ts '
            . 'left join user on user.ID = ts.speaker_id '
            . 'where ts.talk_id = :talk_id and ts.status IS NULL';
        $speaker_stmt = $db->prepare($speaker_sql);
        $speaker_stmt->execute(array("talk_id" => $talk_id));
        $speakers = $speaker_stmt->fetchAll();
        $retval = array();
        if(is_array($speakers)) {
           foreach($speakers as $person) {
               if(!empty($person['full_name'])) {
                   $entry['speaker_name'] = $person['full_name'];
                   $entry['speaker_uri'] = 'http://' . $host . '/v2/users/' . $person['speaker_id'];
               } else {
                   $entry['speaker_name'] = $person['speaker_name'];
               }
               $retval[] = $entry;
           }
        }
        return $retval;
    }
}
