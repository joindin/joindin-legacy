<?php

class ApiView {
    protected function addCount($content) {
        if(is_array($content)) {
            foreach($content as $name => $item) {
                // count what's in the non-meta element
                if($name == "meta") {
                    continue;
                } elseif(is_array($item)) {
                    $content['meta']['count'] = count($item);
                }
            }
        }
        return $content;
    }
}
