<?php

class JsonView extends ApiView {
    public function render($content) {
        header('Content-Type: application/json; charset=utf8');
        echo $this->buildOutput($content);
        return true;
    }

    /**
     *  Function to build output, can be used by JSON and JSONP
     */
    public function buildOutput ($content) {
        $content = $this->addCount($content);
        $retval =  json_encode($content, JSON_NUMERIC_CHECK);
        return $retval;
    }

}
