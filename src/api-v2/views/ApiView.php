<?php

class ApiView {
    protected function addCount($data) {
        if(is_array($data)) {
            $data['count'] = count($data);
        } else {
            $data['count'] = 0;
        }
        return $data;
    }
}
