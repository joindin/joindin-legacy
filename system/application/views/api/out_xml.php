<?php
print_r($data);

$doc=new DOMDocument('1.0');
$doc->formatOuput=true;

$resp=$doc->createElement('response');
$doc->appendChild($resp);

foreach($data as $k=>$v){
	foreach($v as $ik=>$iv){
		$it=$doc->createElement('item');
		$resp->appendChild($it);
	
		$elem=$doc->createElement($ik,$iv);
		$it->appendChild($elem);
	}
}

echo $doc->saveXML();
?>