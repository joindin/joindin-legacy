<?php

class JsonPView extends JsonView {
    public function __construct($callback) {
        $this->_callback = $callback;
    }
    public function render($content) {
        header('Content-Type: text/javascript; charset=utf8');
        $retval = $this->_callback . '(' . $this->buildOutput($content) . ');';
        echo $retval;
        return true;
    }
}
