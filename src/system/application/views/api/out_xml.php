<?php
function addArrayToXML($xml, $data) {
	foreach($data as $key => $item) {
		if(is_numeric($key)){
			$key = "item";
		}
		if(is_string($item) or empty($item)) {
			$xml->addChild($key,$item);
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

?>
