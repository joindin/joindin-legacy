<?php

class TalkCommentModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'rating' => 'rating',
            'comment' => 'comment',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'rating' => 'rating',
            'comment' => 'comment',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public static function getCommentsByTalkId($db, $talk_id, $resultsperpage, $start, $request, $verbose = false) {
        $sql = 'select * from talk_comments '
            . 'where talk_id = :talk_id';
        $sql .= static::buildLimit($resultsperpage, $start);
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':talk_id' => $talk_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $request, $verbose);
            return $retval;
        }
        return false;
    }

    public static function transformResults($results, $request, $verbose) {
        $list = parent::transformResults($results, $verbose);
        $host = $request->host;

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($results as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/talks/' 
                    . $row['talk_id'] . '/comments/' . $row['ID'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/talks/' 
                    . $row['talk_id'] . '/comments/' . $row['ID'] . '?verbose=yes';
                $list[$key]['talk_link'] = 'http://' . $host . '/v2/talks/' 
                    . $row['talk_id'];
                $list[$key]['user_link'] = 'http://' . $host . '/v2/users/' 
                    . $row['user_id'];
            }

            if (count($list) > 1) {
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }

}
