<?php

class ApiView {
    protected function addCount($data) {
        $data['count'] = count($data);
        return $data;
    }
}
