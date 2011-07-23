<?php

class JsonView extends ApiView {
    public function render($content) {
        header('Content-Type: application/json; charset=utf8');
        $content = $this->addCount($content);
        echo json_encode($content, JSON_NUMERIC_CHECK);
        return true;
    }
}
