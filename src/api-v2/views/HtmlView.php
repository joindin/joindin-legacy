<?php

class HtmlView extends ApiView {
    public function render($content) {
        header('Content-Type: text/html');
        $content = $this->addCount($content);
        print_r($content);
        return true;
    }
}
