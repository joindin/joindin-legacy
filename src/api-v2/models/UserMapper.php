<?php

/**
 * UserMapper
 * 
 * @uses ApiModel
 * @package API
 */
class UserMapper extends ApiMapper 
{

    /**
     * Default mapping for column names to API field names
     * 
     * @return array with keys as API fields and values as db columns
     */
    public function getDefaultFields() 
    {
        $fields = array(
            "username" => "username",
            "full_name" => "full_name",
            "twitter_username" => "twitter_username"
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
            "username" => "username",
            "full_name" => "full_name",
            "twitter_username" => "twitter_username"
            );
        return $fields;
    }

    public function getUserById($user_id, $verbose = false) 
    {
        $results = $this->getUsers(1, 0, 'ID=' . (int)$user_id, null);
        if ($results) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }

    protected function getUsers($resultsperpage, $start, $where = null, $order = null) 
    {
        $sql = 'select user.* '
            . 'from user '
            . 'where active = 1 ';
        
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        return false;
    }

    public function getUserList($resultsperpage, $start, $verbose = false) 
    {
        $order = 'user.ID';
        $results = $this->getUsers($resultsperpage, $start, null, $order);
        if (is_array($results)) {
            $retval = $this->transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    public function transformResults($results, $verbose) 
    {
        $list = parent::transformResults($results, $verbose);
        $base = $this->_request->base;
        $version = $this->_request->version;

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($results as $key => $row) {
                $list[$key]['uri'] = $base . '/' . $version . '/users/' 
                    . $row['ID'];
                $list[$key]['verbose_uri'] = $base . '/' . $version . '/users/' 
                    . $row['ID'] . '?verbose=yes';
                $list[$key]['website_uri'] = 'http://joind.in/user/view/' . $row['ID'];
                $list[$key]['talks_uri'] = $base . '/' . $version . '/users/' 
                    . $row['ID'] . '/talks/';
                $list[$key]['attended_events_uri'] = $base . '/' . $version . '/users/' 
                    . $row['ID'] . '/attended/';
            }
        }
        $retval = array();
        $retval['users'] = $list;
        $retval['meta'] = $this->getPaginationLinks($list);

        return $retval;
    }


    public function isSiteAdmin($user_id) {
        $results = $this->getUsers(1, 0, 'ID=' . (int)$user_id, null);
        if($results[0]['admin'] == 1) {
            return true;
        }
        return false;
    }
}
