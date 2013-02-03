<?php
function addArrayToXML($xml, $data) {
    foreach ($data as $key => $item) {
        if (is_numeric($key)) {
            $key = "item";
        }
        if (is_string($item) || empty($item) || is_bool($item)) {
            // we might have an empty array, this is expected
            if (is_array($item) AND count($item) == 0) {
                $item = '';
            }
            $xml->addChild($key, htmlspecialchars($item, ENT_NOQUOTES));
        } else {
            $child = $xml->addChild($key);
            addArrayToXML($child, $item);
        }
    }
}

header('Content-Type: text/xml');

$xml = simplexml_load_string('<?xml version="1.0" ?><response/>');
addArrayToXML($xml, $items);

echo $xml->asXML();

