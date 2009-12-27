<?php
header('Content-Type: text/xml');
$doc=new DOMDocument('1.0');
$doc->formatOuput=true;

buildXML($doc,array('response'=>$items[0]));
echo $doc->saveXML();

?>