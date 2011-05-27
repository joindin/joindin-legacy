<?php

class HtmlView extends ApiView {
    public function render($content) {
        header('Content-Type: text/html; charset=utf8');
        $content = $this->addCount($content);
        $this->print_array($content);
        return true;
    }

    protected function print_array($content) {
        foreach($content as $field => $value) {
            if(is_array($value)) {
                // recurse and print a primitive break
                echo "<strong>" . $field . ": </strong>";
                echo "<br />\n";

                $this->print_array($value);
                // newline
                echo "<br />\n";
                echo "<br />\n";
            } else {
                // field name
                echo "<strong>" . $field . ": </strong>";
                // value, with hyperlinked hyperlinks
                if(strpos($value, 'http://') === 0) {
                    echo "<a href=\"" . $value . "\">" . $value . "</a>";
                } else {
                    echo $value;
                }
                    
                // newline
                echo "<br />\n";
            }
        }
    }
}
