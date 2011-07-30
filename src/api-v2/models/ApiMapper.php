<?php

class ApiMapper {
    /**
     * Object constructor, sets up the db and some objects need request too
     * 
     * @param PDO     $db      The database connection handle
     * @param Request $request The request object (optional not all objects need it)
     */
    public function __construct(PDO $db, Request $request = NULL) {
        $this->_db = $db;
        if(isset($request)) {
            $this->_request = $request;
        }
        return true;
    }

    public function getDefaultFields() {
        return array();
    }
    public function getVerboseFields() {
        return array();
    }

    public function transformResults($results, $verbose) {
        $fields = $verbose ? static::getVerboseFields() : static::getDefaultFields();
        $retval = array();

        // format results to only include named fields
        foreach($results as $row) {
            $entry = array();
            foreach($fields as $key => $value) {
                // special handling for dates
                if(substr($key, -5) == '_date' && !empty($row[$value])) {
                    $entry[$key] = date('c', $row[$value]);
                } else {
                    $entry[$key] = mb_convert_encoding($row[$value], 'UTF-8');
                }
            }
            $retval[] = $entry;
        }
        return $retval;
    }

    protected function buildLimit($resultsperpage, $start) {
        if($resultsperpage == 0) {
            // special case, no limits
            $limit = '';
        } else {
            $limit = ' LIMIT '
                . $start . ','
                . $resultsperpage;
        }
        return $limit;
    }

    protected function addPaginationLinks($list, $request) {
        $list['meta']['count'] = count($list);
        $list['meta']['this_page'] = 'http://' . $request->host . $request->path_info .'?' . http_build_query($request->parameters);
        $next_params = $prev_params = $request->parameters;

        $next_params['start'] = $next_params['start'] + $next_params['resultsperpage'];
        $list['meta']['next_page'] = 'http://' . $request->host . $request->path_info . '?' . http_build_query($next_params);
        if($prev_params['start'] >= $prev_params['resultsperpage']) {
            $prev_params['start'] = $prev_params['start'] - $prev_params['resultsperpage'];
            $list['meta']['prev_page'] = 'http://' . $request->host . $request->path_info . '?' . http_build_query($prev_params);
        }
        return $list;
    }

}
