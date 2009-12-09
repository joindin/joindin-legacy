<?php

$doc=new DOMDocument('1.0');
$doc->formatOuput=true;

buildXML($doc,array('response'=>$items));
echo $doc->saveXML();

?>