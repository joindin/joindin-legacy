<?php
//echo 'data: '; print_r($items);

$doc=new DOMDocument('1.0');
$doc->formatOuput=true;

$resp=$doc->createElement('response');
$doc->appendChild($resp);

foreach($items as $k=>$v){
	$it=$doc->createElement('item');
	$resp->appendChild($it);

	foreach($v as $ik=>$iv){	
		$elem=$doc->createElement($ik,$iv);
		$it->appendChild($elem);
	}
}

echo $doc->saveXML();
?>