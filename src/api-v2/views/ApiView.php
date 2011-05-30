<?php

class ApiView {
    protected function addCount($data) {
        if(is_array($data)) {
            // do nothing, this is added earlier
        } else {
            $data['meta']['count'] = 0;
        }
        return $data;
    }
}
